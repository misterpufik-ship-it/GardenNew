<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

$config = __DIR__ . '/../couch/config.php';
require_once $config;

$db = new mysqli(K_DB_HOST, K_DB_USER, K_DB_PASSWORD, K_DB_NAME);
$db->set_charset('utf8');

$text = K_DB_TABLES_PREFIX . 'couch_data_text';
$row = $db->query("SELECT value FROM `{$text}` WHERE field_id=88 AND page_id=3 LIMIT 1")->fetch_assoc();
$value = $row ? $row['value'] : '';
echo "len=" . strlen($value) . "\n";
echo substr($value, 0, 300) . "\n\n";
$data = @unserialize($value);
echo 'unserialize type: ' . gettype($data) . "\n";
if (is_array($data)) {
    echo 'count: ' . count($data) . "\n";
    print_r(array_slice($data, 0, 2, true));
}
