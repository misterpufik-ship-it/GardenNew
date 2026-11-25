<?php
// Idempotent CLI migration: add reserve-rule fields without changing existing settings.
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require_once dirname(__DIR__) . '/admiralteyskaya/couch/addons/garden-booking/booking-lib.php';
$db = garden_booking_db();
if (!$db) throw new RuntimeException('Database unavailable');
$db->set_charset('utf8mb4');
$prefix = K_DB_TABLES_PREFIX;
function reserve_sql_value($value) {
    global $db;
    return $value === null ? 'NULL' : "'" . $db->real_escape_string((string)$value) . "'";
}
$tpl = $db->query("SELECT id FROM {$prefix}couch_templates WHERE name='booking-settings.php'")->fetch_assoc();
if (!$tpl) throw new RuntimeException('Booking template missing');
$tid = (int)$tpl['id'];
$page = $db->query("SELECT id FROM {$prefix}couch_pages WHERE template_id=$tid AND is_master='1'")->fetch_assoc();
if (!$page) throw new RuntimeException('Master page missing');
$pid = (int)$page['id'];
$source = file_get_contents(dirname(__DIR__) . '/admiralteyskaya/booking-settings.php');
preg_match_all('~<cms:editable\s+([^>]*)(?:/>|>(.*?)</cms:editable>)~s', $source, $matches, PREG_SET_ORDER);
$created = 0;
foreach ($matches as $match) {
    preg_match_all("~([a-z_]+)='([^']*)'~", $match[1], $attributes, PREG_SET_ORDER);
    $attr = array();
    foreach ($attributes as $pair) $attr[$pair[1]] = $pair[2];
    if (!preg_match('/^(adm|udel)_reserve_/', $attr['name'])) continue;
    $name = reserve_sql_value($attr['name']);
    if ($db->query("SELECT id FROM {$prefix}couch_fields WHERE template_id=$tid AND name=$name")->num_rows) continue;
    $type = reserve_sql_value($attr['type']);
    $row = $db->query("SELECT * FROM {$prefix}couch_fields WHERE template_id=$tid AND k_type=$type LIMIT 1")->fetch_assoc();
    if (!$row) throw new RuntimeException('No sample field for '.$attr['type']);
    unset($row['id']);
    $row['name'] = $attr['name'];
    $row['label'] = $attr['label'];
    $row['k_group'] = isset($attr['group']) ? $attr['group'] : '';
    $row['k_order'] = $attr['order'];
    $row['k_desc'] = isset($attr['desc']) ? $attr['desc'] : '';
    $row['default_data'] = isset($match[2]) ? $match[2] : '';
    $row['data'] = '';
    $row['opt_values'] = isset($attr['opt_values']) ? $attr['opt_values'] : '';
    $row['opt_selected'] = $attr['type'] === 'dropdown' ? $row['default_data'] : '';
    $cols = implode('`,`', array_keys($row));
    $values = implode(',', array_map('reserve_sql_value', array_values($row)));
    if (!$db->query("INSERT INTO {$prefix}couch_fields (`$cols`) VALUES ($values)")) throw new RuntimeException($db->error);
    $fid = (int)$db->insert_id;
    if ($attr['type'] !== 'group') {
        $value = reserve_sql_value($row['default_data']);
        if (!$db->query("INSERT INTO {$prefix}couch_data_text (page_id,field_id,value) VALUES ($pid,$fid,$value)")) throw new RuntimeException($db->error);
    }
    $created++;
}
echo "Reserve-rule fields created: $created\n";
