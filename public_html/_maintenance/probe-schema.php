<?php
$config = '/home/m/mrpuffch/garden-lounge.pro/public_html/admiralteyskaya/couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require $config;
$h = K_DB_HOST; $p = 3306;
if (strpos($h, ':') !== false) list($h, $p) = explode(':', $h, 2);
$db = new mysqli($h, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$p);
$pref = K_DB_TABLES_PREFIX;
$r = $db->query("SHOW COLUMNS FROM {$pref}couch_templates");
echo "TEMPLATES\n";
while ($row = $r->fetch_assoc()) echo $row['Field'] . "\n";
$r = $db->query("SHOW COLUMNS FROM {$pref}couch_fields");
echo "FIELDS\n";
while ($row = $r->fetch_assoc()) echo $row['Field'] . "\n";
