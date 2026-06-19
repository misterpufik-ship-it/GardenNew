<?php
$config = '/home/m/mrpuffch/garden-lounge.pro/public_html/admiralteyskaya/couch/config.php';
if (!is_file($config)) { echo "no config\n"; exit(1); }
define('K_COUCH_DIR', dirname($config) . '/');
require $config;
$h = K_DB_HOST; $p = 3306;
if (strpos($h, ':') !== false) list($h, $p) = explode(':', $h, 2);
$db = new mysqli($h, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$p);
if ($db->connect_errno) { echo $db->connect_error; exit(1); }
$pref = K_DB_TABLES_PREFIX;
$r = $db->query("SELECT f.name, LEFT(dt.value, 24) AS val FROM {$pref}couch_data_text dt JOIN {$pref}couch_fields f ON f.id=dt.field_id WHERE dt.value LIKE '8:%' OR f.name LIKE '%bot%' OR f.name LIKE '%chat%' LIMIT 20");
while ($row = $r->fetch_assoc()) {
    echo $row['name'] . '=' . $row['val'] . "\n";
}
$t = $db->query("SELECT id,name,title FROM {$pref}couch_templates WHERE name='booking-settings.php' LIMIT 1");
$row = $t ? $t->fetch_assoc() : null;
echo 'booking_template=' . ($row ? $row['id'] : 'missing') . "\n";
$secrets = '/home/m/mrpuffch/garden-lounge.pro/public_html/admiralteyskaya/couch/booking-secrets.php';
echo 'secrets=' . (is_file($secrets) ? 'yes' : 'no') . "\n";
