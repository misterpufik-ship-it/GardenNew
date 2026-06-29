<?php
/**
 * Set mobile preloader video path in CouchCMS.
 * ?key=<md5('garden-lounge-set-preloader-mobile')>
 */
$expectedKey = md5('garden-lounge-set-preloader-mobile');
if ((isset($_GET['key']) ? $_GET['key'] : '') !== $expectedKey) {
    http_response_code(403);
    exit("Forbidden\n");
}
header('Content-Type: text/plain; charset=utf-8');

$videoPath = '/video/preloader-mobile.mp4';
$fieldName = 'preloader_video_mobile';
$templateName = 'preloader-settings.php';

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

$prefix = K_DB_TABLES_PREFIX;
$fieldEsc = $db->real_escape_string($fieldName);
$tplEsc = $db->real_escape_string($templateName);
$videoEsc = $db->real_escape_string($videoPath);

$tpl = $db->query("SELECT id FROM {$prefix}couch_templates WHERE name='{$tplEsc}' LIMIT 1");
if (!$tpl || !($tplRow = $tpl->fetch_assoc())) {
    exit("Template not found: {$templateName}\n");
}
$templateId = (int)$tplRow['id'];

$pageRes = $db->query(
    "SELECT id FROM {$prefix}couch_pages WHERE template_id={$templateId} ORDER BY is_master DESC, id ASC LIMIT 1"
);
if (!$pageRes || !($pageRow = $pageRes->fetch_assoc())) {
    exit("Page not found for {$templateName}\n");
}
$pageId = (int)$pageRow['id'];

$fieldRes = $db->query(
    "SELECT id FROM {$prefix}couch_fields WHERE template_id={$templateId} AND name='{$fieldEsc}' LIMIT 1"
);
if (!$fieldRes || !($fieldRow = $fieldRes->fetch_assoc())) {
    exit("Field not found: {$fieldName}\n");
}
$fieldId = (int)$fieldRow['id'];

$dataRes = $db->query(
    "SELECT id, value FROM {$prefix}couch_data_text WHERE page_id={$pageId} AND field_id={$fieldId} LIMIT 1"
);
$old = '';
if ($dataRes && ($dataRow = $dataRes->fetch_assoc())) {
    $old = (string)$dataRow['value'];
    $db->query("UPDATE {$prefix}couch_data_text SET value='{$videoEsc}' WHERE id=" . (int)$dataRow['id'] . " LIMIT 1");
    echo "Updated {$fieldName} on page #{$pageId}\n";
} else {
    $db->query(
        "INSERT INTO {$prefix}couch_data_text (page_id, field_id, value) VALUES ({$pageId}, {$fieldId}, '{$videoEsc}')"
    );
    echo "Inserted {$fieldName} on page #{$pageId}\n";
}

$disk = dirname($root) . '/video/preloader-mobile.mp4';
echo "Old: {$old}\n";
echo "New: {$videoPath}\n";
echo "File on disk: " . (is_file($disk) ? 'yes' : 'no') . "\n";

$cacheDir = $root . '/couch/cache';
if (is_dir($cacheDir)) {
    foreach (glob($cacheDir . '/*.dat') as $file) {
        @unlink($file);
    }
    echo "Cleared couch cache\n";
}
