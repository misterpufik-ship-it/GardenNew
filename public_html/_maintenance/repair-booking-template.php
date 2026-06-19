<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

$config = '/home/m/mrpuffch/garden-lounge.pro/public_html/admiralteyskaya/couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require $config;

$host = K_DB_HOST;
$port = 3306;
if (strpos($host, ':') !== false) list($host, $port) = explode(':', $host, 2);
$db = new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
$db->set_charset('utf8');

$tables = array(
    'templates' => K_DB_TABLES_PREFIX . 'couch_templates',
    'fields' => K_DB_TABLES_PREFIX . 'couch_fields',
    'pages' => K_DB_TABLES_PREFIX . 'couch_pages',
    'text' => K_DB_TABLES_PREFIX . 'couch_data_text',
);

function q($db, $value) {
    if ($value === null) return 'NULL';
    return "'" . $db->real_escape_string((string)$value) . "'";
}
function one($db, $sql) {
    $res = $db->query($sql);
    return ($res && ($row = $res->fetch_assoc())) ? $row : null;
}
function insert_row($db, $table, $row) {
    unset($row['id']);
    $cols = array_keys($row);
    $vals = array();
    foreach ($row as $v) $vals[] = q($db, $v);
    $sql = "INSERT INTO `{$table}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $vals) . ")";
    if (!$db->query($sql)) throw new RuntimeException($db->error);
    return (int)$db->insert_id;
}

$tpl = one($db, "SELECT * FROM {$tables['templates']} WHERE name='booking-settings.php' LIMIT 1");
if (!$tpl) {
    fwrite(STDERR, "booking-settings.php template missing\n");
    exit(1);
}
$template_id = (int)$tpl['id'];

$page = one($db, "SELECT * FROM {$tables['pages']} WHERE template_id={$template_id} AND is_master='1' LIMIT 1");
if (!$page) {
    $source_page = one($db, "SELECT * FROM {$tables['pages']} WHERE template_id=(SELECT id FROM {$tables['templates']} WHERE name='globals.php' LIMIT 1) AND is_master='1' LIMIT 1");
    $new_page = $source_page;
    $new_page['template_id'] = (string)$template_id;
    $new_page['page_title'] = 'Default page for booking-settings.php * PLEASE CHANGE THIS TITLE *';
    $new_page['page_name'] = 'booking-settings';
    $page_id = insert_row($db, $tables['pages'], $new_page);
} else {
    $page_id = (int)$page['id'];
}

$count = one($db, "SELECT COUNT(*) AS c FROM {$tables['fields']} WHERE template_id={$template_id}");
if ((int)$count['c'] > 0) {
    echo "fields_already_present={$count['c']}\n";
    exit(0);
}

$source_tpl = one($db, "SELECT id FROM {$tables['templates']} WHERE name='globals.php' LIMIT 1");
$sample_field = one($db, "SELECT * FROM {$tables['fields']} WHERE template_id=" . (int)$source_tpl['id'] . " AND k_type='text' LIMIT 1");

$fields = array(
    array('booking_intro', 'message', 'Справка', '', 1, ''),
    array('group_admiral_booking', 'group', 'Адмиралтейская — отправка', '', 10, ''),
    array('adm_booking_enabled', 'dropdown', 'Включить отправку', 'group_admiral_booking', 11, '1', 'Нет=0 | Да=1'),
    array('adm_branch_label', 'text', 'Название филиала в сообщении', 'group_admiral_booking', 12, 'Адмиралтейская'),
    array('adm_telegram_link', 'text', 'Telegram (для справки)', 'group_admiral_booking', 13, 'https://t.me/gardenlounge_admiral'),
    array('adm_bot_token', 'text', 'Bot Token', 'group_admiral_booking', 14, ''),
    array('adm_chat_id', 'text', 'Chat ID', 'group_admiral_booking', 15, '@gardenlounge_admiral'),
    array('group_udelnaya_booking', 'group', 'Удельная — отправка', '', 20, ''),
    array('udel_booking_enabled', 'dropdown', 'Включить отправку', 'group_udelnaya_booking', 21, '1', 'Нет=0 | Да=1'),
    array('udel_branch_label', 'text', 'Название филиала в сообщении', 'group_udelnaya_booking', 22, 'Удельная'),
    array('udel_telegram_link', 'text', 'Telegram (для справки)', 'group_udelnaya_booking', 23, 'https://t.me/Garden_lounge_spb'),
    array('udel_bot_token', 'text', 'Bot Token', 'group_udelnaya_booking', 24, ''),
    array('udel_chat_id', 'text', 'Chat ID', 'group_udelnaya_booking', 25, '@Garden_lounge_spb'),
);

foreach ($fields as $field) {
    $row = $sample_field;
    $row['template_id'] = (string)$template_id;
    $row['name'] = $field[0];
    $row['k_type'] = $field[1];
    $row['label'] = $field[2];
    $row['k_group'] = $field[3];
    $row['k_order'] = (string)$field[4];
    $row['default_data'] = $field[5];
    $row['data'] = '';
    if ($field[1] === 'dropdown' && isset($field[6])) {
        $row['opt_values'] = $field[6];
        $row['opt_selected'] = $field[5];
    } else {
        $row['opt_values'] = '';
        $row['opt_selected'] = '';
    }
    $field_id = insert_row($db, $tables['fields'], $row);
    if ($field[1] !== 'group' && $field[1] !== 'message' && $field[5] !== '') {
        $db->query("INSERT INTO {$tables['text']} (page_id, field_id, value) VALUES ({$page_id}, {$field_id}, " . q($db, $field[5]) . ")");
    }
}

echo "fields_created=" . count($fields) . " page_id={$page_id}\n";
