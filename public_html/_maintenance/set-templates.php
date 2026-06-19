<?php
$config = '/home/m/mrpuffch/garden-lounge.pro/public_html/admiralteyskaya/couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require $config;
require_once '/home/m/mrpuffch/garden-lounge.pro/public_html/admiralteyskaya/couch/addons/garden-booking/booking-lib.php';
$h = K_DB_HOST; $p = 3306;
if (strpos($h, ':') !== false) list($h, $p) = explode(':', $h, 2);
$db = new mysqli($h, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$p);
$pref = K_DB_TABLES_PREFIX;
$tpl = $default = garden_booking_default_message_template();
$page_id = 42;
function set_if_empty($db, $pref, $page_id, $name, $value) {
  $r = $db->query("SELECT dt.value, f.id fid FROM {$pref}couch_fields f JOIN {$pref}couch_data_text dt ON dt.field_id=f.id AND dt.page_id={$page_id} WHERE f.name='{$name}' LIMIT 1");
  $row = $r->fetch_assoc();
  if (!$row || trim($row['value']) === '') {
    $fid = (int)$row['fid'];
    $v = $db->real_escape_string($value);
    $db->query("UPDATE {$pref}couch_data_text SET value='{$v}' WHERE page_id={$page_id} AND field_id={$fid}");
    echo "set {$name}\n";
  }
}
set_if_empty($db, $pref, $page_id, 'adm_message_template', $default);
set_if_empty($db, $pref, $page_id, 'udel_message_template', $default);
