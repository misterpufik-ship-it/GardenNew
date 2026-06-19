<?php
if ((isset($_GET['token']) ? $_GET['token'] : '') !== 'gl-home-fix-20260619') {
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
require_once K_COUCH_DIR . 'functions.php';

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ? ini_get('mysqli.default_port') : 3306;
if (strpos($host, ':') !== false) {
    $parts = explode(':', $host, 2);
    $host = $parts[0];
    $port = $parts[1];
}

$db = @new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
if ($db->connect_errno) {
    http_response_code(500);
    exit('db connect failed');
}
$db->set_charset('utf8');

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';
$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$name = 'home.php';

header('Content-Type: text/plain; charset=utf-8');

function qval($db, $value)
{
    if ($value === null) {
        return 'NULL';
    }
    return "'" . $db->real_escape_string((string)$value) . "'";
}

$res = $db->query("SELECT id, executable, hidden FROM `{$templates}` WHERE name='" . $db->real_escape_string($name) . "' LIMIT 1");
$row = $res ? $res->fetch_assoc() : null;
if (!$row) {
    exit("Template {$name} not found. Log into Couch admin and open Главная once.\n");
}

$templateId = (int)$row['id'];
$db->query("UPDATE `{$templates}` SET executable='1', hidden='0', title='Главная' WHERE id={$templateId} LIMIT 1");
echo "home.php template #{$templateId}: executable=1 hidden=0\n";

$pageRes = $db->query("SELECT id, is_master FROM `{$pages}` WHERE template_id={$templateId} LIMIT 1");
$page = $pageRes ? $pageRes->fetch_assoc() : null;

if (!$page) {
    $refPage = $db->query("SELECT * FROM `{$pages}` WHERE template_id=1 LIMIT 1");
    $refPageRow = $refPage ? $refPage->fetch_assoc() : null;
    if (!$refPageRow) {
        exit("reference page missing\n");
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
    $vals = array();
    foreach (array_values($refPageRow) as $v) {
        $vals[] = qval($db, $v);
    }
    $sql = "INSERT INTO `{$pages}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $vals) . ")";
    if (!$db->query($sql)) {
        exit("page insert failed: " . $db->error . "\n");
    }
    echo "Created page #{$db->insert_id}\n";
} else {
    $db->query("UPDATE `{$pages}` SET is_master=1, page_name='index', page_title='Главная' WHERE id=" . (int)$page['id'] . " LIMIT 1");
    echo "Page #{$page['id']} updated (is_master=1)\n";
}

$fieldCount = $db->query("SELECT COUNT(*) AS c FROM `{$fields}` WHERE template_id={$templateId}");
if ($fieldCount && ($fc = $fieldCount->fetch_assoc())) {
    echo "Fields registered: {$fc['c']}\n";
    if ((int)$fc['c'] === 0) {
        echo "WARNING: no fields — open /admiralteyskaya/couch/ admin and visit Главная template once.\n";
    }
}

$cacheDir = K_COUCH_DIR . 'cache';
$removed = 0;
if (is_dir($cacheDir)) {
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cacheDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($items as $item) {
        $path = $item->getPathname();
        if ($item->isDir()) {
            if ($path !== $cacheDir && basename($path) !== 'booking-throttle') {
                @rmdir($path);
            }
            continue;
        }
        if (basename($path) === '.htaccess') {
            continue;
        }
        if (@unlink($path)) {
            $removed++;
        }
    }
}
if (isset($FUNCS) && method_exists($FUNCS, 'invalidate_cache')) {
    $FUNCS->invalidate_cache();
}

echo "Cache cleared ({$removed} files)\n";
echo "OK\n";
