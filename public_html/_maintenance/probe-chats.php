<?php
$config = '/home/m/mrpuffch/garden-lounge.pro/public_html/admiralteyskaya/couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require $config;
$h = K_DB_HOST; $p = 3306;
if (strpos($h, ':') !== false) list($h, $p) = explode(':', $h, 2);
$db = new mysqli($h, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$p);
$pref = K_DB_TABLES_PREFIX;
$r = $db->query("SELECT f.name, dt.value FROM {$pref}couch_data_text dt JOIN {$pref}couch_fields f ON f.id=dt.field_id WHERE f.name IN ('adm_chat_id','udel_chat_id','adm_bot_token','udel_bot_token','adm_message_template','udel_message_template')");
while ($row = $r->fetch_assoc()) {
  $v = $row['value'];
  if (strpos($v, '8:') === 0) $v = substr($v,0,12).'...';
  echo $row['name'] . '=' . $v . "\n";
}
