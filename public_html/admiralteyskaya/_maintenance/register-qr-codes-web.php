<?php
/**
 * Web runner: register qr-codes.php template and patch admin menu.
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/register-qr-codes-web.php?key=<md5>
 * key = md5('garden-lounge-register-qr-codes')
 */
$expectedKey = md5('garden-lounge-register-qr-codes');
if ((isset($_GET['key']) ? $_GET['key'] : '') !== $expectedKey) {
    http_response_code(403);
    exit("Forbidden\n");
}
header('Content-Type: text/plain; charset=utf-8');

$root = realpath(__DIR__ . '/..');
$config = $root . '/couch/config.php';
if (!is_file($config)) {
    exit("CouchCMS config not found\n");
}

define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ?: 3306;
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
}

mysqli_report(MYSQLI_REPORT_OFF);
$db = @new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
if ($db->connect_errno) {
    exit("DB connection failed: {$db->connect_error}\n");
}
$db->set_charset('utf8');

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';
$fields = K_DB_TABLES_PREFIX . 'couch_fields';

function gl_qval($db, $value)
{
    if ($value === null) {
        return 'NULL';
    }
    return "'" . $db->real_escape_string((string)$value) . "'";
}

function gl_fetch_one($db, $sql)
{
    $res = $db->query($sql);
    return $res ? $res->fetch_assoc() : null;
}

$name = 'qr-codes.php';
$title = 'QR коды';
$order = 2;
$cloneFrom = 'admin-instructions.php';

$row = gl_fetch_one($db, "SELECT id FROM `{$templates}` WHERE name='" . $db->real_escape_string($name) . "' LIMIT 1");
if (!$row) {
    $sample = gl_fetch_one($db, "SELECT * FROM `{$templates}` WHERE name='" . $db->real_escape_string($cloneFrom) . "' LIMIT 1");
    if (!$sample) {
        $sample = gl_fetch_one($db, "SELECT * FROM `{$templates}` WHERE name='preloader-settings.php' LIMIT 1");
    }
    if (!$sample) {
        exit("No clone source template found\n");
    }
    unset($sample['id']);
    $sample['name'] = $name;
    $sample['title'] = $title;
    $sample['executable'] = '0';
    $sample['hidden'] = '0';
    $sample['clonable'] = '0';
    if (isset($sample['order'])) {
        $sample['order'] = (string)$order;
    }
    $cols = array_keys($sample);
    $vals = array();
    foreach (array_values($sample) as $v) {
        $vals[] = gl_qval($db, $v);
    }
    $sql = "INSERT INTO `{$templates}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $vals) . ")";
    if (!$db->query($sql)) {
        exit("Template insert failed: {$db->error}\n");
    }
    $templateId = (int)$db->insert_id;
    echo "Created template {$name} (#{$templateId})\n";
} else {
    $templateId = (int)$row['id'];
    $db->query(
        "UPDATE `{$templates}` SET executable='0', hidden='0', title='" .
        $db->real_escape_string($title) . "', `order`='" . (int)$order . "' WHERE id={$templateId} LIMIT 1"
    );
    echo "Template {$name} exists (#{$templateId})\n";
}

$page = gl_fetch_one($db, "SELECT id FROM `{$pages}` WHERE template_id={$templateId} AND is_master='1' LIMIT 1");
if (!$page) {
    $ref = gl_fetch_one($db, "SELECT * FROM `{$pages}` WHERE template_id={$templateId} LIMIT 1");
    if (!$ref) {
        $ref = gl_fetch_one($db, "SELECT * FROM `{$pages}` WHERE template_id=1 LIMIT 1");
    }
    if ($ref) {
        unset($ref['id']);
        $now = date('Y-m-d H:i:s');
        $ref['template_id'] = (string)$templateId;
        $ref['page_title'] = $title;
        $ref['page_name'] = 'index';
        $ref['creation_date'] = $now;
        $ref['modification_date'] = $now;
        $ref['publish_date'] = $now;
        $ref['is_master'] = '1';
        $cols = array_keys($ref);
        $vals = array();
        foreach (array_values($ref) as $v) {
            $vals[] = gl_qval($db, $v);
        }
        $sql = "INSERT INTO `{$pages}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $vals) . ")";
        if (!$db->query($sql)) {
            exit("Master page insert failed: {$db->error}\n");
        }
        echo "Created master page\n";
    }
} else {
    echo "Master page #" . (int)$page['id'] . "\n";
}

$fieldName = 'qr_manager';
$field = gl_fetch_one(
    $db,
    "SELECT id FROM `{$fields}` WHERE template_id={$templateId} AND name='" . $db->real_escape_string($fieldName) . "' LIMIT 1"
);
if (!$field) {
    $sample = gl_fetch_one($db, "SELECT * FROM `{$fields}` WHERE k_type='message' LIMIT 1");
    if ($sample) {
        unset($sample['id']);
        $sample['template_id'] = (string)$templateId;
        $sample['name'] = $fieldName;
        $sample['label'] = 'QR коды';
        $sample['k_group'] = '';
        $sample['k_order'] = '1';
        $sample['k_type'] = 'message';
        if (isset($sample['_html'])) {
            $sample['_html'] = '';
        }
        $cols = array_keys($sample);
        $vals = array();
        foreach (array_values($sample) as $v) {
            $vals[] = gl_qval($db, $v);
        }
        $sql = "INSERT INTO `{$fields}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $vals) . ")";
        if ($db->query($sql)) {
            echo "Added field {$fieldName}\n";
        } else {
            echo "Field insert failed: {$db->error}\n";
        }
    } else {
        echo "No sample message field; template parse on first admin open will create it\n";
    }
} else {
    echo "Field {$fieldName} exists\n";
}

require __DIR__ . '/patch-kfunctions-qr-codes.php';

$qrLib = dirname($root) . '/qr/lib.php';
if (is_file($qrLib)) {
    if (!defined('GL_QR_LIB')) {
        define('GL_QR_LIB', 1);
    }
    require_once $qrLib;
    $data = gl_qr_load();
    echo 'QR storage ready, codes=' . count($data['codes']) . "\n";
}

echo "Done. Open admin: /admiralteyskaya/couch/?o=qr-codes.php\n";
