<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

$config = __DIR__ . '/../couch/config.php';
require_once $config;

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ?: 3306;
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
}

$db = new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int) $port);
if ($db->connect_errno) {
    fwrite(STDERR, $db->connect_error . "\n");
    exit(1);
}
$db->set_charset('utf8');

$text = K_DB_TABLES_PREFIX . 'couch_data_text';
$row = $db->query("SELECT value FROM `{$text}` WHERE field_id=88 AND page_id=3 LIMIT 1")->fetch_assoc();
$value = $row ? $row['value'] : '';
echo "legacy len=" . strlen($value) . "\n";
echo substr($value, 0, 400) . "\n\n";
$data = @unserialize($value);
echo 'legacy unserialize type: ' . gettype($data) . "\n";
if (is_array($data)) {
    echo 'legacy count: ' . count($data) . "\n";
    print_r(array_slice($data, 0, 1, true));
}

$row2 = $db->query("SELECT value FROM `{$text}` WHERE field_id=757 AND page_id=3 LIMIT 1")->fetch_assoc();
$value2 = $row2 ? $row2['value'] : '';
echo "\nnew len=" . strlen($value2) . "\n";
echo substr($value2, 0, 400) . "\n\n";
$data2 = @unserialize($value2);
if (is_array($data2)) {
    echo 'new count: ' . count($data2) . "\n";
    print_r(array_slice($data2, 0, 1, true));
}
