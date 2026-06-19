<?php
$config = '/home/m/mrpuffch/garden-lounge.pro/public_html/admiralteyskaya/couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require $config;
$h = K_DB_HOST; $p = 3306;
if (strpos($h, ':') !== false) list($h, $p) = explode(':', $h, 2);
$db = new mysqli($h, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$p);
$pref = K_DB_TABLES_PREFIX;
$sql = "SELECT f.name, dt.value FROM {$pref}couch_templates t JOIN {$pref}couch_pages p ON p.template_id=t.id AND p.is_master='1' JOIN {$pref}couch_fields f ON f.template_id=t.id JOIN {$pref}couch_data_text dt ON dt.page_id=p.id AND dt.field_id=f.id WHERE t.name='booking-settings.php' ORDER BY f.k_order";
$r = $db->query($sql);
while ($row = $r->fetch_assoc()) {
    $val = $row['value'];
    if (strpos($val, '8:') === 0) $val = substr($val, 0, 12) . '...';
    echo $row['name'] . '=' . $val . "\n";
}
