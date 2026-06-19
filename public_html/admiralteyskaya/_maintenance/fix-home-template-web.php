<?php
if (($_GET['token'] ?? '') !== 'gl-home-fix-20260619') {
    http_response_code(404);
    exit;
}

$config = __DIR__ . '/../couch/config.php';
if (!is_file($config)) {
    http_response_code(500);
    exit('config missing');
}

define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ?: 3306;
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
}

$db = @new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
if ($db->connect_errno) {
    http_response_code(500);
    exit('db connect failed');
}
$db->set_charset('utf8');

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';
$name = 'home.php';

header('Content-Type: text/plain; charset=utf-8');

function qval(mysqli $db, $value): string
{
    if ($value === null) {
        return 'NULL';
    }
    return "'" . $db->real_escape_string((string)$value) . "'";
}

$res = $db->query("SELECT id, name, executable, hidden FROM `{$templates}` WHERE name='" . $db->real_escape_string($name) . "' LIMIT 1");
$row = $res ? $res->fetch_assoc() : null;

if (!$row) {
    $ref = $db->query("SELECT * FROM `{$templates}` WHERE name='index.php' LIMIT 1");
    $refRow = $ref ? $ref->fetch_assoc() : null;
    if (!$refRow) {
        exit("reference template index.php not found\n");
    }
    unset($refRow['id']);
    $refRow['name'] = $name;
    $refRow['title'] = 'Главная';
    $refRow['executable'] = 1;
    $refRow['hidden'] = 0;
    $refRow['clonable'] = 0;
    $cols = array_keys($refRow);
    $vals = array_map(fn($v) => qval($db, $v), array_values($refRow));
    $sql = "INSERT INTO `{$templates}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $vals) . ")";
    if (!$db->query($sql)) {
        exit("template insert failed: " . $db->error . "\n");
    }
    $row = ['id' => (int)$db->insert_id, 'name' => $name, 'executable' => 1, 'hidden' => 0];
    echo "Registered template #{$row['id']}\n";
}

$templateId = (int)$row['id'];
$db->query("UPDATE `{$templates}` SET executable='1', hidden='0' WHERE id={$templateId} LIMIT 1");
echo "Template home.php id={$templateId}: executable=1 hidden=0\n";

$pageRes = $db->query("SELECT id, template_id, page_name, is_master FROM `{$pages}` WHERE template_id={$templateId} LIMIT 1");
$page = $pageRes ? $pageRes->fetch_assoc() : null;

if ($page) {
    echo "Page: #{$page['id']} template_id={$page['template_id']} name={$page['page_name']} is_master={$page['is_master']}\n";
    if ((int)$page['is_master'] !== 1) {
        $db->query("UPDATE `{$pages}` SET is_master=1 WHERE id=" . (int)$page['id'] . " LIMIT 1");
        echo "Fixed is_master=1\n";
    }
} else {
    $refPage = $db->query("SELECT * FROM `{$pages}` WHERE template_id=1 LIMIT 1");
    $refPageRow = $refPage ? $refPage->fetch_assoc() : null;
    if (!$refPageRow) {
        exit("reference page for index.php not found\n");
    }
    unset($refPageRow['id']);
    $now = date('Y-m-d H:i:s');
    $refPageRow['template_id'] = $templateId;
    $refPageRow['page_title'] = 'Главная';
    $refPageRow['page_name'] = 'index';
    $refPageRow['creation_date'] = $now;
    $refPageRow['modification_date'] = $now;
    $refPageRow['publish_date'] = $now;
    $refPageRow['is_master'] = 1;
    $cols = array_keys($refPageRow);
    $vals = array_map(fn($v) => qval($db, $v), array_values($refPageRow));
    $sql = "INSERT INTO `{$pages}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $vals) . ")";
    if (!$db->query($sql)) {
        exit("page insert failed: " . $db->error . "\n");
    }
    echo "Created page #{$db->insert_id} for home.php\n";
} else {
    echo "Page exists: #{$page['id']}\n";
}

$cacheDir = K_COUCH_DIR . 'cache/';
if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '*');
    if ($files) {
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }
    echo "Cache cleared\n";
}

echo "OK\n";

$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$fieldCount = $db->query("SELECT COUNT(*) AS c FROM `{$fields}` WHERE template_id={$templateId}");
if ($fieldCount && ($fc = $fieldCount->fetch_assoc())) {
    echo "Fields for home.php template: {$fc['c']}\n";
}

$allHome = $db->query("SELECT id, name, executable, hidden FROM `{$templates}` WHERE name LIKE '%home%'");
echo "Templates matching home:\n";
while ($t = $allHome->fetch_assoc()) {
    echo "  #{$t['id']} {$t['name']} exec={$t['executable']} hidden={$t['hidden']}\n";
}

$page43 = $db->query("SELECT id, template_id, page_name, is_master FROM `{$pages}` WHERE id=43 LIMIT 1");
if ($page43 && ($p43 = $page43->fetch_assoc())) {
    echo "Page #43 belongs to template_id={$p43['template_id']}\n";
}
