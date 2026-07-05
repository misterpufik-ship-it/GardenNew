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

$mmTemplates = K_DB_TABLES_PREFIX . 'couch_templates';
$mmPages = K_DB_TABLES_PREFIX . 'couch_pages';
$mmFieldsTable = K_DB_TABLES_PREFIX . 'couch_fields';
$mmDataText = K_DB_TABLES_PREFIX . 'couch_data_text';

function gl_fetch_one_dump($db, $sql)
{
    $res = $db->query($sql);
    return $res ? $res->fetch_assoc() : null;
}

$template = gl_fetch_one_dump($db, "SELECT id FROM `{$mmTemplates}` WHERE name='layout-mobile-menu.php' LIMIT 1");
if (!$template) {
    exit("Template not found\n");
}
$mmTemplateId = (int) $template['id'];
$page = gl_fetch_one_dump($db, "SELECT id FROM `{$mmPages}` WHERE template_id={$mmTemplateId} AND is_master='1' LIMIT 1");
if (!$page) {
    exit("Master page not found\n");
}
$mmPageId = (int) $page['id'];

$res = $db->query("SELECT f.id AS field_id, f.name, f.not_active FROM `{$mmFieldsTable}` f WHERE f.template_id={$mmTemplateId} AND f.k_type NOT IN ('group','message') ORDER BY f.k_order, f.id");
if (!$res) {
    exit("Query failed: {$db->error}\n");
}

echo "layout-mobile-menu.php page_id={$mmPageId}\n\n";
while ($row = $res->fetch_assoc()) {
    $fieldId = (int) $row['field_id'];
    $valRow = gl_fetch_one_dump($db, "SELECT value FROM `{$mmDataText}` WHERE page_id={$mmPageId} AND field_id={$fieldId} ORDER BY id DESC LIMIT 1");
    $value = $valRow ? (string) $valRow['value'] : '';
    $flag = $row['not_active'] ? ' [legacy/hidden]' : '';
    echo $row['name'] . $flag . ': ' . $value . "\n";
}
