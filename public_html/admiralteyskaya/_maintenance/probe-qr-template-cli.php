<?php
if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}
define('K_COUCH_DIR', dirname(__DIR__) . '/couch/');
require dirname(__DIR__) . '/couch/config.php';
$db = new mysqli('127.0.0.1', K_DB_USER, K_DB_PASSWORD, K_DB_NAME);
$t = K_DB_TABLES_PREFIX . 'couch_templates';
$r = $db->query("SELECT id,name,title,hidden,executable FROM `{$t}` WHERE name LIKE '%qr%' OR name LIKE '%settings%' OR name LIKE 'layout%' OR name='admin-labels.php' OR name='home.php' ORDER BY name");
if (!$r) {
    echo "ERR " . $db->error . "\n";
    exit(1);
}
while ($row = $r->fetch_assoc()) {
    echo json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
}
