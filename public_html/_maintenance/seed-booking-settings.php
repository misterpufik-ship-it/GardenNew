<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

require_once dirname(__DIR__) . '/admiralteyskaya/couch/addons/garden-booking/booking-lib.php';

$options = getopt('', array(
    'branch:',
    'bot-token:',
    'chat-id:',
    'probe-worker',
    'test-send:',
));

function seed_field($db, $prefix, $field_name, $value) {
    $field_name = $db->real_escape_string($field_name);
    $template = $db->real_escape_string('booking-settings.php');

    $sql =
        "SELECT p.id AS page_id, f.id AS field_id " .
        "FROM {$prefix}couch_templates t " .
        "INNER JOIN {$prefix}couch_pages p ON p.template_id=t.id AND p.is_master='1' " .
        "INNER JOIN {$prefix}couch_fields f ON f.template_id=t.id AND f.name='{$field_name}' " .
        "WHERE t.name='{$template}' LIMIT 1";
    $res = $db->query($sql);
    if ( !$res || !($row = $res->fetch_assoc()) ) {
        fwrite(STDERR, "Field not found in DB (register template in admin first): {$field_name}\n");
        return false;
    }

    $page_id = (int)$row['page_id'];
    $field_id = (int)$row['field_id'];
    $value_sql = "'" . $db->real_escape_string($value) . "'";

    $exists = $db->query("SELECT page_id FROM {$prefix}couch_data_text WHERE page_id={$page_id} AND field_id={$field_id} LIMIT 1");
    if ( $exists && $exists->num_rows ) {
        $sql = "UPDATE {$prefix}couch_data_text SET value={$value_sql} WHERE page_id={$page_id} AND field_id={$field_id}";
    } else {
        $sql = "INSERT INTO {$prefix}couch_data_text (page_id, field_id, value) VALUES ({$page_id}, {$field_id}, {$value_sql})";
    }
    if ( !$db->query($sql) ) {
        fwrite(STDERR, "DB error: {$db->error}\n");
        return false;
    }
    return true;
}

if ( !empty($options['probe-worker']) ) {
    $ch = curl_init('https://1.misterpufik.workers.dev');
    curl_setopt_array($ch, array(
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query(array(
            'name' => 'Probe',
            'phone' => '+79990001122',
            'visit_day' => '16',
            'visit_month' => '06',
            'guests' => '2',
            'date' => '16.06.2026',
            'visit_time' => '19:00',
        )),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
    ));
    $body = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "worker_status={$code} body={$body}\n";
    exit(0);
}

$db = garden_booking_db();
if ( !$db ) {
    fwrite(STDERR, "DB connection failed.\n");
    exit(1);
}
$prefix = defined('K_DB_TABLES_PREFIX') ? K_DB_TABLES_PREFIX : '';

if ( !empty($options['branch']) && !empty($options['bot-token']) && !empty($options['chat-id']) ) {
    $branch = garden_booking_normalize_branch($options['branch']);
    if ( !$branch ) {
        fwrite(STDERR, "Unknown branch.\n");
        exit(1);
    }
    $map = garden_booking_field_map($branch);
    seed_field($db, $prefix, $map['bot_token'], $options['bot-token']);
    seed_field($db, $prefix, $map['chat_id'], $options['chat-id']);
    seed_field($db, $prefix, $map['enabled'], '1');
    echo "Seeded {$branch} booking settings.\n";
    exit(0);
}

if ( !empty($options['test-send']) ) {
    $branch = garden_booking_normalize_branch($options['test-send']);
    if ( !$branch ) {
        fwrite(STDERR, "Unknown branch for --test-send.\n");
        exit(1);
    }
    $settings = garden_booking_get_branch_settings($branch);
    $name = ($branch === 'udelnaya') ? 'Тест Удельная Cursor' : 'Тест Адмиралтейская Cursor';
    $message = garden_booking_build_message($settings, array(
        'name' => $name,
        'phone_display' => '+7 (999) 000-11-22',
        'date' => date('d.m.Y'),
        'visit_time' => '19:00',
        'guests' => '2',
        'source_url' => ($branch === 'udelnaya')
            ? 'https://garden-lounge.pro/udelnaya#reservation'
            : 'https://garden-lounge.pro/admiralteyskaya#reservation',
    ));
    list($ok, $error) = garden_booking_send_telegram($settings, $message);
    if ( !$ok ) {
        fwrite(STDERR, "Send failed: {$error}\n");
        exit(1);
    }
    echo "Sent test message for {$branch} with name {$name}\n";
    exit(0);
}

fwrite(STDERR, "Usage:\n");
fwrite(STDERR, "  php seed-booking-settings.php --branch=admiral --bot-token=... --chat-id=...\n");
fwrite(STDERR, "  php seed-booking-settings.php --test-send=admiral\n");
exit(1);
