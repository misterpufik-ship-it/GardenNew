<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

$config = '/home/m/mrpuffch/garden-lounge.pro/public_html/admiralteyskaya/couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;
require_once '/home/m/mrpuffch/garden-lounge.pro/public_html/admiralteyskaya/couch/addons/garden-booking/booking-lib.php';

$options = getopt('', array(
    'adm-token:',
    'udel-token:',
    'test-send-admiral',
    'test-send-udelnaya',
));

$host = K_DB_HOST;
$port = 3306;
if (strpos($host, ':') !== false) list($host, $port) = explode(':', $host, 2);
$db = new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
if ($db->connect_errno) {
    fwrite(STDERR, $db->connect_error . "\n");
    exit(1);
}
$db->set_charset('utf8');

$tables = array(
    'templates' => K_DB_TABLES_PREFIX . 'couch_templates',
    'fields' => K_DB_TABLES_PREFIX . 'couch_fields',
    'pages' => K_DB_TABLES_PREFIX . 'couch_pages',
    'text' => K_DB_TABLES_PREFIX . 'couch_data_text',
);

function q($db, $value) {
    return "'" . $db->real_escape_string((string)$value) . "'";
}
function one($db, $sql) {
    $res = $db->query($sql);
    return ($res && ($row = $res->fetch_assoc())) ? $row : null;
}
function set_field_text($db, $tables, $page_id, $field_name, $value) {
    $row = one($db, "SELECT f.id AS field_id FROM {$tables['fields']} f INNER JOIN {$tables['templates']} t ON t.id=f.template_id WHERE t.name='booking-settings.php' AND f.name=" . q($db, $field_name) . " LIMIT 1");
    if (!$row) return false;
    $field_id = (int)$row['field_id'];
    $exists = one($db, "SELECT page_id FROM {$tables['text']} WHERE page_id={$page_id} AND field_id={$field_id} LIMIT 1");
    if ($exists) {
        return $db->query("UPDATE {$tables['text']} SET value=" . q($db, $value) . " WHERE page_id={$page_id} AND field_id={$field_id}");
    }
    return $db->query("INSERT INTO {$tables['text']} (page_id, field_id, value) VALUES ({$page_id}, {$field_id}, " . q($db, $value) . ")");
}
function ensure_field($db, $tables, $template_id, $page_id, $sample_field, $spec) {
    $exists = one($db, "SELECT id FROM {$tables['fields']} WHERE template_id={$template_id} AND name=" . q($db, $spec['name']) . " LIMIT 1");
    if ($exists) {
        return (int)$exists['id'];
    }
    $row = $sample_field;
    unset($row['id']);
    $row['template_id'] = (string)$template_id;
    $row['name'] = $spec['name'];
    $row['k_type'] = $spec['type'];
    $row['label'] = $spec['label'];
    $row['k_group'] = $spec['group'];
    $row['k_order'] = (string)$spec['order'];
    $row['default_data'] = $spec['default'];
    $row['data'] = '';
    $row['k_desc'] = isset($spec['desc']) ? $spec['desc'] : '';
    if (isset($spec['height'])) $row['height'] = (string)$spec['height'];
    $row['opt_values'] = '';
    $row['opt_selected'] = '';
    $cols = array_keys($row);
    $vals = array();
    foreach ($row as $v) $vals[] = q($db, $v);
    $sql = "INSERT INTO {$tables['fields']} (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $vals) . ")";
    if (!$db->query($sql)) throw new RuntimeException($db->error);
    $field_id = (int)$db->insert_id;
    if ($spec['default'] !== '') {
        $db->query("INSERT INTO {$tables['text']} (page_id, field_id, value) VALUES ({$page_id}, {$field_id}, " . q($db, $spec['default']) . ")");
    }
    return $field_id;
}

$tpl = one($db, "SELECT id FROM {$tables['templates']} WHERE name='booking-settings.php' LIMIT 1");
if (!$tpl) {
    fwrite(STDERR, "booking-settings.php template missing\n");
    exit(1);
}
$template_id = (int)$tpl['id'];
$page = one($db, "SELECT id FROM {$tables['pages']} WHERE template_id={$template_id} AND is_master='1' LIMIT 1");
if (!$page) {
    fwrite(STDERR, "master page missing\n");
    exit(1);
}
$page_id = (int)$page['id'];

$sample_text = one($db, "SELECT * FROM {$tables['fields']} WHERE template_id={$template_id} AND k_type='text' LIMIT 1");
$sample_textarea = one($db, "SELECT * FROM {$tables['fields']} WHERE template_id={$template_id} AND k_type='textarea' LIMIT 1");
if (!$sample_textarea) $sample_textarea = $sample_text;

$default_template = "🆕 Новая бронь — {branch_label}\n\n👤 Имя: {name}\n📞 Телефон: {phone}\n📅 Дата: {date}\n🕐 Время: {time}\n👥 Гости: {guests}\n🌐 Страница: {source_url}";

ensure_field($db, $tables, $template_id, $page_id, $sample_textarea, array(
    'name' => 'adm_message_template',
    'type' => 'textarea',
    'label' => 'Текст сообщения в Telegram',
    'group' => 'group_admiral_booking',
    'order' => 16,
    'height' => 180,
    'desc' => 'Переменные: {branch}, {branch_label}, {name}, {phone}, {date}, {time}, {guests}, {source_url}',
    'default' => $default_template,
));
ensure_field($db, $tables, $template_id, $page_id, $sample_textarea, array(
    'name' => 'udel_message_template',
    'type' => 'textarea',
    'label' => 'Текст сообщения в Telegram',
    'group' => 'group_udelnaya_booking',
    'order' => 26,
    'height' => 180,
    'desc' => 'Переменные: {branch}, {branch_label}, {name}, {phone}, {date}, {time}, {guests}, {source_url}',
    'default' => $default_template,
));

if (!empty($options['adm-token'])) {
    set_field_text($db, $tables, $page_id, 'adm_bot_token', $options['adm-token']);
    echo "adm_token_set\n";
}
if (!empty($options['udel-token'])) {
    set_field_text($db, $tables, $page_id, 'udel_bot_token', $options['udel-token']);
    echo "udel_token_set\n";
}

if (isset($options['test-send-admiral'])) {
    $settings = garden_booking_get_branch_settings('admiral');
    $message = garden_booking_build_message($settings, array(
        'name' => 'Тест Адмиралтейская Cursor',
        'phone_display' => '+7 (999) 123-45-67',
        'date' => date('d.m.Y'),
        'visit_time' => '19:00',
        'guests' => '2',
        'source_url' => 'https://garden-lounge.pro/admiralteyskaya/#reservation',
    ));
    list($ok, $error) = garden_booking_send_telegram($settings, $message);
    if (!$ok) {
        fwrite(STDERR, "admiral send failed: {$error}\n");
        exit(1);
    }
    echo "admiral_sent\n";
}

if (isset($options['test-send-udelnaya'])) {
    $settings = garden_booking_get_branch_settings('udelnaya');
    $message = garden_booking_build_message($settings, array(
        'name' => 'Тест Удельная Cursor',
        'phone_display' => '+7 (999) 765-43-21',
        'date' => date('d.m.Y'),
        'visit_time' => '19:00',
        'guests' => '3',
        'source_url' => 'https://garden-lounge.pro/udelnaya/#reservation',
    ));
    list($ok, $error) = garden_booking_send_telegram($settings, $message);
    if (!$ok) {
        fwrite(STDERR, "udelnaya send failed: {$error}\n");
        exit(1);
    }
    echo "udelnaya_sent\n";
}

echo "upgrade_done page_id={$page_id}\n";

