<?php
$config = __DIR__ . '/../admiralteyskaya/couch/config.php';
require_once $config;
$host = K_DB_HOST;
$port = 3306;
if ( strpos($host, ':') !== false ) {
    list($host, $port) = explode(':', $host, 2);
}
$db = new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
$table = K_DB_TABLES_PREFIX . 'data_text';
$r = $db->query("SELECT value FROM {$table} WHERE value LIKE '%misterpufik%' LIMIT 50");
while ( $row = $r->fetch_assoc() ) {
    echo $row['value'] . "\n";
}
