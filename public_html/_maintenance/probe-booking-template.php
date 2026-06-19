<?php
$config = '/home/m/mrpuffch/garden-lounge.pro/public_html/admiralteyskaya/couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require $config;
$h = K_DB_HOST; $p = 3306;
if (strpos($h, ':') !== false) list($h, $p) = explode(':', $h, 2);
$db = new mysqli($h, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$p);
$pref = K_DB_TABLES_PREFIX;
$t = $db->query("SELECT id,name,title FROM {$pref}couch_templates WHERE name='booking-settings.php'");
$row = $t->fetch_assoc();
echo 'template=' . json_encode($row) . "\n";
if ($row) {
  $r = $db->query("SELECT COUNT(*) c FROM {$pref}couch_fields WHERE template_id=".(int)$row['id']);
  echo 'fields=' . $r->fetch_assoc()['c'] . "\n";
  $r = $db->query("SELECT name FROM {$pref}couch_fields WHERE template_id=".(int)$row['id']." ORDER BY k_order");
  while ($x = $r->fetch_assoc()) echo $x['name']."\n";
}
