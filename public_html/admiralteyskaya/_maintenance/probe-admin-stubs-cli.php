<?php
if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}
define('K_COUCH_DIR', dirname(__DIR__) . '/couch/');
require dirname(__DIR__) . '/couch/config.php';

$db = new mysqli('127.0.0.1', K_DB_USER, K_DB_PASSWORD, K_DB_NAME);
$db->set_charset('utf8mb4');
$t = K_DB_TABLES_PREFIX . 'couch_templates';
$p = K_DB_TABLES_PREFIX . 'couch_pages';

$sql = "SELECT t.name, t.title AS tpl_title, t.hidden, t.executable, p.page_title
        FROM `{$t}` t
        LEFT JOIN `{$p}` p ON p.template_id = t.id AND p.is_master = '1'
        WHERE t.name LIKE 'layout%' OR t.name LIKE '%settings%' OR t.name IN ('qr-codes.php','admin-labels.php','home.php','globals.php','index.php')
        ORDER BY t.name";
$res = $db->query($sql);
while ($row = $res->fetch_assoc()) {
    echo implode("\t", array(
        $row['name'],
        $row['tpl_title'],
        $row['page_title'] ?? '',
        $row['hidden'],
        $row['executable'],
    )) . "\n";
}
