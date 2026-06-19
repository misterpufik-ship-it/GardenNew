<?php
$config = '/home/m/mrpuffch/garden-lounge.pro/public_html/admiralteyskaya/couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require $config;
$h = K_DB_HOST; $p = 3306;
if (strpos($h, ':') !== false) list($h, $p) = explode(':', $h, 2);
$db = new mysqli($h, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$p);
$pref = K_DB_TABLES_PREFIX;
$r = $db->query("SELECT dt.value FROM {$pref}couch_data_text dt WHERE dt.value REGEXP '^[0-9]+:[A-Za-z0-9_-]{20,}' LIMIT 5");
while ($row = $r->fetch_assoc()) echo substr($row['value'], 0, 12) . "...\n";
