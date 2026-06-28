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

$sql =
    "SELECT dt.id, dt.value " .
    "FROM {$prefix}couch_templates t " .
    "INNER JOIN {$prefix}couch_pages p ON p.template_id=t.id AND p.is_master='1' " .
    "INNER JOIN {$prefix}couch_fields f ON f.template_id=t.id AND f.name='{$fieldEsc}' " .
    "INNER JOIN {$prefix}couch_data_text dt ON dt.page_id=p.id AND dt.field_id=f.id " .
    "WHERE t.name='{$tplEsc}' " .
    "LIMIT 1";

$res = $db->query($sql);
if (!$res || !($row = $res->fetch_assoc())) {
    exit("Field {$fieldName} not found for {$templateName}\n");
}

$old = (string)$row['value'];
$id = (int)$row['id'];
$db->query("UPDATE {$prefix}couch_data_text SET value='{$videoEsc}' WHERE id={$id} LIMIT 1");

$disk = dirname($root) . '/video/preloader-mobile.mp4';
$diskOk = is_file($disk) ? 'yes' : 'no';

echo "Updated {$fieldName}\n";
echo "Old: {$old}\n";
echo "New: {$videoPath}\n";
echo "File on disk (site root): {$diskOk}\n";
