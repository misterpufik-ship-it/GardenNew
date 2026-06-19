<?php
$config = __DIR__ . '/../couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ?: 3306;
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
}
$db = new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int) $port);
$db->set_charset('utf8');

$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$dataText = K_DB_TABLES_PREFIX . 'couch_data_text';
$pageId = 43;

$res = $db->query(
    "SELECT f.id, f.name, f.k_type, f.k_group, LENGTH(t.value) AS len, t.value " .
    "FROM `{$dataText}` t JOIN `{$fields}` f ON f.id=t.field_id " .
    "WHERE t.page_id={$pageId} AND (f.name LIKE '%gallery%' OR f.k_group LIKE '%gallery%') ORDER BY f.id"
);
echo "Gallery data for home page #{$pageId}:\n";
while ($res && ($row = $res->fetch_assoc())) {
    echo "#{$row['id']} {$row['name']} group={$row['k_group']} [{$row['k_type']}] len={$row['len']}\n";
    if ($row['len'] > 0 && $row['len'] < 600) {
        echo "  {$row['value']}\n";
    }
}

$res2 = $db->query("SELECT id, name, _html FROM `{$fields}` WHERE name IN ('home_adm_gallery','home_udel_gallery','home_gallery_img')");
while ($res2 && ($row = $res2->fetch_assoc())) {
    echo "\nField #{$row['id']} {$row['name']} _html contains:\n";
    echo (strpos($row['_html'], 'home_adm_gallery_img') !== false ? '  home_adm_gallery_img YES' : '  home_adm_gallery_img NO') . "\n";
    echo (strpos($row['_html'], 'home_gallery_img') !== false ? '  home_gallery_img YES' : '  home_gallery_img NO') . "\n";
}
