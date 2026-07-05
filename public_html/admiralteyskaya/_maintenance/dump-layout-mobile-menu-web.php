<?php
/**
 * Read-only dump of layout-mobile-menu field values (current + legacy).
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/dump-layout-mobile-menu-web.php?key=<md5>
 * key = md5('garden-lounge-dump-layout-mobile-menu')
 */
$expectedKey = md5('garden-lounge-dump-layout-mobile-menu');
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

$db = @new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int) $port);
if ($db->connect_errno) {
    exit("DB connection failed: {$db->connect_error}\n");
}
$db->set_charset('utf8');

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';
$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$dataText = K_DB_TABLES_PREFIX . 'couch_data_text';

$template = $db->query("SELECT id FROM `{$templates}` WHERE name='layout-mobile-menu.php' LIMIT 1")->fetch_assoc();
if (!$template) {
    exit("Template not found\n");
}
$templateId = (int) $template['id'];
$page = $db->query("SELECT id FROM `{$pages}` WHERE template_id={$templateId} AND is_master='1' LIMIT 1")->fetch_assoc();
if (!$page) {
    exit("Master page not found\n");
}
$pageId = (int) $page['id'];

function gl_fetch_one_dump($db, $sql)
{
    $res = $db->query($sql);
    return $res ? $res->fetch_assoc() : null;
}

$res = $db->query("SELECT f.id AS field_id, f.name, f.label, f.not_active FROM `{$fields}` f WHERE f.template_id={$templateId} AND f.k_type NOT IN ('group','message') ORDER BY f.k_order, f.id");
echo "layout-mobile-menu.php page_id={$pageId}\n\n";
while ($row = $res->fetch_assoc()) {
    $fieldId = (int) $row['field_id'];
    $valRow = gl_fetch_one_dump($db, "SELECT value FROM `{$dataText}` WHERE page_id={$pageId} AND field_id={$fieldId} ORDER BY id DESC LIMIT 1");
    $value = $valRow ? (string) $valRow['value'] : '';
    $flag = $row['not_active'] ? ' [legacy/hidden]' : '';
    echo $row['name'] . $flag . ': ' . $value . "\n";
}
exit;
