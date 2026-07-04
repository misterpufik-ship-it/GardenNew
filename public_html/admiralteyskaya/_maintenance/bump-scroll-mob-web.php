<?php
/**
 * Bump mobile scroll offsets 64 → 72 in layout-scroll.php CMS data.
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/bump-scroll-mob-web.php?key=<md5>
 * key = md5('garden-lounge-bump-scroll-mob')
 */
$expectedKey = md5('garden-lounge-bump-scroll-mob');
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
$dataText = K_DB_TABLES_PREFIX . 'couch_data_text';

$tpl = $db->query("SELECT id FROM `{$templates}` WHERE name='layout-scroll.php' LIMIT 1");
$row = $tpl ? $tpl->fetch_assoc() : null;
if (!$row) {
    exit("layout-scroll.php template not found\n");
}
$templateId = (int)$row['id'];

$page = $db->query("SELECT id FROM `{$pages}` WHERE template_id={$templateId} AND is_master='1' LIMIT 1");
$pageRow = $page ? $page->fetch_assoc() : null;
if (!$pageRow) {
    exit("Master page not found\n");
}
$pageId = (int)$pageRow['id'];

$res = $db->query(
    "SELECT f.id, f.name, d.value FROM `{$fields}` f " .
    "LEFT JOIN `{$dataText}` d ON d.field_id=f.id AND d.page_id={$pageId} " .
    "WHERE f.template_id={$templateId} AND (f.name LIKE '%\\_mob' OR f.name LIKE '%\\_mob\\_%')"
);
if (!$res) {
    exit("Query failed: {$db->error}\n");
}

$updated = 0;
while ($field = $res->fetch_assoc()) {
    $fieldId = (int)$field['id'];
    $name = $field['name'];
    $value = (string)$field['value'];
    if ($value !== '64') {
        echo "Skip {$name}: {$value}\n";
        continue;
    }
    $sql = "UPDATE `{$dataText}` SET value='72' WHERE page_id={$pageId} AND field_id={$fieldId} LIMIT 1";
    if ($db->query($sql) && $db->affected_rows > 0) {
        $updated++;
        echo "Updated {$name}: 64 → 72\n";
    }
}

$cacheDir = $root . '/couch/cache';
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

echo "Done. Updated {$updated} field(s), cleared {$removed} cache file(s).\n";
