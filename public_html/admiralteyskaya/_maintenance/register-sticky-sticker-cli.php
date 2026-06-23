<?php
/**
 * Регистрирует шаблоны sticky-sticker.php в CouchCMS (нужен super-admin).
 *
 * CLI:
 *   php _maintenance/register-sticky-sticker-cli.php
 *
 * HTTP:
 *   /admiralteyskaya/_maintenance/register-sticky-sticker-cli.php?key=<md5>
 *   key = md5('garden-lounge-register-sticky')
 */

$isWeb = (PHP_SAPI !== 'cli');
if ($isWeb) {
    $expectedKey = md5('garden-lounge-register-sticky');
    if ((isset($_GET['key']) ? $_GET['key'] : '') !== $expectedKey) {
        http_response_code(403);
        exit("Forbidden\n");
    }
    header('Content-Type: text/plain; charset=utf-8');
}

$root = realpath(__DIR__ . '/..');
if (!$root) {
    $msg = "Cannot resolve template root\n";
    if ($isWeb) {
        echo $msg;
    } else {
        fwrite(STDERR, $msg);
    }
    exit(1);
}

$config = $root . '/couch/config.php';
if (!is_file($config)) {
    $msg = "CouchCMS config not found\n";
    if ($isWeb) {
        echo $msg;
    } else {
        fwrite(STDERR, $msg);
    }
    exit(1);
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
    $msg = "DB connection failed: {$db->connect_error}\n";
    if ($isWeb) {
        echo $msg;
    } else {
        fwrite(STDERR, $msg);
    }
    exit(1);
}
$db->set_charset('utf8');

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';

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

function gl_ensure_template($db, $templates, $pages, $name, $title, $order, $cloneFrom)
{
    $row = gl_fetch_one($db, "SELECT id FROM `{$templates}` WHERE name='" . $db->real_escape_string($name) . "' LIMIT 1");
    if (!$row) {
        $sample = gl_fetch_one($db, "SELECT * FROM `{$templates}` WHERE name='" . $db->real_escape_string($cloneFrom) . "' LIMIT 1");
        if (!$sample) {
            echo "Template {$name} missing and clone source {$cloneFrom} not found\n";
            return 0;
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
            echo "Template insert failed for {$name}: {$db->error}\n";
            return 0;
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

    $page = gl_fetch_one($db, "SELECT id FROM `{$pages}` WHERE template_id={$templateId} LIMIT 1");
    if (!$page) {
        $ref = gl_fetch_one($db, "SELECT * FROM `{$pages}` WHERE template_id=1 LIMIT 1");
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
            if ($db->query($sql)) {
                echo "Created master page for {$name}\n";
            }
        }
    }

    return $templateId;
}

$targets = [
    [
        'file' => $root . '/sticky-sticker.php',
        'uri' => '/admiralteyskaya/sticky-sticker.php',
        'name' => 'sticky_sticker',
        'page_title' => 'Липкий стикер',
        'order' => 165,
    ],
    [
        'file' => dirname($root) . '/udelnaya/sticky-sticker.php',
        'uri' => '/udelnaya/sticky-sticker.php',
        'name' => 'sticky_sticker_udel',
        'page_title' => 'Липкий стикер',
        'order' => 165,
    ],
];

foreach ($targets as $target) {
    if (!is_file($target['file'])) {
        $msg = "Missing file: {$target['file']}\n";
        if ($isWeb) {
            echo $msg;
        } else {
            fwrite(STDERR, $msg);
        }
        exit(1);
    }
    gl_ensure_template($db, $templates, $pages, $target['name'], $target['page_title'], $target['order'], 'gallery_section');
}

chdir($root);
require_once $root . '/couch/cms.php';

global $AUTH, $DB;

if (!isset($AUTH->user) || !is_object($AUTH->user)) {
    $msg = "Couch auth not initialized\n";
    if ($isWeb) {
        echo $msg;
    } else {
        fwrite(STDERR, $msg);
    }
    exit(1);
}

$AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;

foreach ($targets as $target) {
    chdir(dirname($target['file']));
    $_SERVER['HTTP_HOST'] = 'garden-lounge.pro';
    $_SERVER['REQUEST_URI'] = $target['uri'];
    $_SERVER['SCRIPT_NAME'] = $target['uri'];
    $_SERVER['SCRIPT_FILENAME'] = $target['file'];
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

    echo "Parsing {$target['name']} as super-admin...\n";
    ob_start();
    require $target['file'];
    ob_end_clean();

    $tplRows = $DB->select($templates, array('id', 'name', 'title'), "name='" . $DB->sanitize($target['name']) . "'");
    if (!count($tplRows)) {
        $msg = "Template {$target['name']} not found in DB after parse\n";
        if ($isWeb) {
            echo $msg;
        } else {
            fwrite(STDERR, $msg);
        }
        exit(1);
    }

    $templateId = (int) $tplRows[0]['id'];
    $DB->update(
        $templates,
        array('executable' => '0', 'hidden' => '0', 'title' => $target['page_title'], 'clonable' => '0'),
        "id='" . $DB->sanitize($templateId) . "'"
    );

    $fieldRows = $DB->select($fields, array('id', 'name'), "template_id='" . $DB->sanitize($templateId) . "'");
    echo "{$target['name']} fields in DB: " . count($fieldRows) . "\n";
}

define('GL_SKIP_CLI_CHECK', true);
require __DIR__ . '/clear-couch-cache-cli.php';
