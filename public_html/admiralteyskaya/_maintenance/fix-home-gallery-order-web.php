<?php
if ((isset($_GET['token']) ? $_GET['token'] : '') !== 'gl-home-gallery-20260712') {
    http_response_code(404);
    exit;
}

set_time_limit(30);
ini_set('display_errors', '1');
error_reporting(E_ALL);
header('Content-Type: text/plain; charset=utf-8');

$config = __DIR__ . '/../couch/config.php';
if (!is_file($config)) {
    http_response_code(500);
    exit("config missing\n");
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
$pagesTable = K_DB_TABLES_PREFIX . 'couch_pages';
$fieldsTable = K_DB_TABLES_PREFIX . 'couch_fields';
$dataText = K_DB_TABLES_PREFIX . 'couch_data_text';

function qrow($db, $sql)
{
    $res = $db->query($sql);
    return $res ? $res->fetch_assoc() : null;
}

$tpl = qrow($db, "SELECT id FROM `{$templates}` WHERE name='home.php' LIMIT 1");
if (!$tpl) {
    exit("home.php template missing\n");
}

$field = qrow(
    $db,
    "SELECT id FROM `{$fieldsTable}` WHERE template_id=" . (int)$tpl['id'] .
    " AND name='home_adm_gallery' LIMIT 1"
);
if (!$field) {
    exit("home_adm_gallery field missing\n");
}

$page = qrow($db, "SELECT id FROM `{$pagesTable}` WHERE template_id=" . (int)$tpl['id'] . " LIMIT 1");
if (!$page) {
    exit("home.php page missing\n");
}

$row = qrow(
    $db,
    "SELECT value FROM `{$dataText}` WHERE field_id=" . (int)$field['id'] .
    " AND page_id=" . (int)$page['id'] . " LIMIT 1"
);
if (!$row || !$row['value']) {
    exit("gallery data missing\n");
}

$items = @unserialize($row['value']);
if (!is_array($items) || count($items) < 2) {
    exit("gallery has less than 2 items\n");
}

$darkNeedles = array('garden-main-1', 'garden_main_1');
$score = function ($item) use ($darkNeedles) {
    $img = is_array($item) && isset($item['home_adm_gallery_img']) ? (string)$item['home_adm_gallery_img'] : '';
    foreach ($darkNeedles as $needle) {
        if ($needle !== '' && stripos($img, $needle) !== false) {
            return 1;
        }
    }
    return 0;
};

$before = array();
foreach ($items as $item) {
    $before[] = is_array($item) && isset($item['home_adm_gallery_img']) ? basename((string)$item['home_adm_gallery_img']) : '?';
}

usort($items, function ($a, $b) use ($score) {
    return $score($a) - $score($b);
});

$after = array();
foreach ($items as $item) {
    $after[] = is_array($item) && isset($item['home_adm_gallery_img']) ? basename((string)$item['home_adm_gallery_img']) : '?';
}

echo 'before: ' . implode(', ', $before) . "\n";
echo 'after: ' . implode(', ', $after) . "\n";

if ($before !== $after) {
    $escaped = $db->real_escape_string(serialize($items));
    if (!$db->query(
        "UPDATE `{$dataText}` SET value='{$escaped}' WHERE field_id=" . (int)$field['id'] .
        " AND page_id=" . (int)$page['id'] . " LIMIT 1"
    )) {
        exit("update failed: {$db->error}\n");
    }
    echo "gallery updated\n";
} else {
    echo "gallery already optimal\n";
}

$cacheDir = K_COUCH_DIR . 'cache';
$removed = 0;
if (is_dir($cacheDir)) {
    $itemsIt = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cacheDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($itemsIt as $item) {
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

echo "cache cleared ({$removed} files)\n";
echo "OK\n";
