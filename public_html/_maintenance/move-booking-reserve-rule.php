<?php
// Preserve existing field IDs and values while moving rules to branch booking pages.
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require_once dirname(__DIR__) . '/admiralteyskaya/couch/addons/garden-booking/booking-lib.php';
$db = garden_booking_db();
if (!$db) throw new RuntimeException('Database unavailable');
$prefix = K_DB_TABLES_PREFIX;
function reserve_move_query($sql) {
    global $db;
    $result = $db->query($sql);
    if (!$result) throw new RuntimeException($db->error);
    return $result;
}
foreach (array('adm' => 'reservation.php', 'udel' => 'udelnaya/reservation.php') as $branch => $template) {
    $target = reserve_move_query("SELECT t.id AS tid,p.id AS pid FROM {$prefix}couch_templates t JOIN {$prefix}couch_pages p ON p.template_id=t.id AND p.is_master='1' WHERE t.name='$template'")->fetch_assoc();
    if (!$target) throw new RuntimeException('Missing target '.$template);
    $tid = (int)$target['tid']; $pid = (int)$target['pid'];
    $fields = reserve_move_query("SELECT f.id,f.name FROM {$prefix}couch_fields f JOIN {$prefix}couch_templates t ON t.id=f.template_id WHERE t.name='booking-settings.php' AND f.name LIKE '{$branch}\_reserve\_%'");
    $moved = 0;
    while ($field = $fields->fetch_assoc()) {
        $fid = (int)$field['id'];
        $name = $db->real_escape_string($field['name']);
        if (reserve_move_query("SELECT id FROM {$prefix}couch_fields WHERE template_id=$tid AND name='$name'")->num_rows) throw new RuntimeException('Duplicate target field '.$name);
        reserve_move_query("UPDATE {$prefix}couch_data_text SET page_id=$pid WHERE field_id=$fid");
        reserve_move_query("UPDATE {$prefix}couch_fields SET template_id=$tid WHERE id=$fid");
        $moved++;
    }
    $count = reserve_move_query("SELECT COUNT(*) AS n FROM {$prefix}couch_fields WHERE template_id=$tid AND name LIKE '{$branch}\_reserve\_%'")->fetch_assoc();
    if ((int)$count['n'] !== 8) throw new RuntimeException('Expected 8 fields in '.$template);
    echo "$template: moved $moved fields; verified 8 fields\n";
}
