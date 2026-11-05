<?php
if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}
define('K_COUCH_DIR', dirname(__DIR__) . '/couch/');
require dirname(__DIR__) . '/couch/config.php';
$db = new mysqli('127.0.0.1', K_DB_USER, K_DB_PASSWORD, K_DB_NAME);
$t = K_DB_TABLES_PREFIX . 'couch_templates';
$r = $db->query("SELECT name,title FROM `{$t}` ORDER BY name");
while ($row = $r->fetch_assoc()) {
    echo $row['name'] . "\t" . $row['title'] . "\n";
}
