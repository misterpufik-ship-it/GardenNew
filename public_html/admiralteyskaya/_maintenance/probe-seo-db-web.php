<?php
/**
 * Probe SEO field values in CouchCMS DB.
 * ?key=<md5('garden-lounge-probe-seo')>
 */
$expectedKey = md5('garden-lounge-probe-seo');
if ((isset($_GET['key']) ? $_GET['key'] : '') !== $expectedKey) {
    http_response_code(403);
    exit("Forbidden\n");
}
header('Content-Type: text/plain; charset=utf-8');

$root = realpath(__DIR__ . '/..');
$config = $root . '/couch/config.php';
require_once $config;

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ?: 3306;
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
}
$db = new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
$db->set_charset('utf8mb4');
$prefix = K_DB_TABLES_PREFIX;

$res = $db->query(
    "SELECT t.name AS tpl, p.id AS pid, p.page_title, p.page_desc, f.name AS field, dt.value " .
    "FROM {$prefix}couch_data_text dt " .
    "JOIN {$prefix}couch_pages p ON p.id = dt.page_id " .
    "JOIN {$prefix}couch_templates t ON t.id = p.template_id " .
    "JOIN {$prefix}couch_fields f ON f.id = dt.field_id " .
    "WHERE f.name IN ('seo_title_default','seo_desc_default') " .
    "ORDER BY t.name, p.id, f.name"
);
while ($row = $res->fetch_assoc()) {
    echo "{$row['tpl']} page={$row['pid']} field={$row['field']}\n";
    echo "  page_title: {$row['page_title']}\n";
    echo "  value: {$row['value']}\n\n";
}

echo "--- executable index templates ---\n";
$res2 = $db->query(
    "SELECT id, name, title, executable FROM {$prefix}couch_templates " .
    "WHERE name LIKE '%index%' OR title LIKE '%Общая%' OR title LIKE '%УУ%' ORDER BY id"
);
while ($row = $res2->fetch_assoc()) {
    echo "#{$row['id']} name={$row['name']} title={$row['title']} exec={$row['executable']}\n";
}
