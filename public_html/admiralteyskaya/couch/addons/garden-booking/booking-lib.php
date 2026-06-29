<?php
if ( !defined('GARDEN_BOOKING_LIB') ) {
    define('GARDEN_BOOKING_LIB', 1);
}

function garden_booking_config_path() {
    static $path = null;
    if ( $path !== null ) return $path;

    $candidates = array(
        dirname(__DIR__, 2) . '/config.php',
    );
    foreach ( $candidates as $candidate ) {
        if ( file_exists($candidate) ) {
            $path = $candidate;
            return $path;
        }
    }
    return null;
}

function garden_booking_db() {
    static $db = null;
    if ( $db instanceof mysqli ) return $db;

    $config = garden_booking_config_path();
    if ( !$config ) {
        return null;
    }

    if ( !defined('K_COUCH_DIR') ) {
        define('K_COUCH_DIR', dirname($config) . '/');
    }
    require_once $config;

    $host = K_DB_HOST;
    $port = 3306;
    if ( strpos($host, ':') !== false ) {
        list($host, $port) = explode(':', $host, 2);
    }

    mysqli_report(MYSQLI_REPORT_OFF);
    $db = @new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
    if ( $db->connect_errno ) {
        $db = null;
    }
    return $db;
}

function garden_booking_field_map( $branch ) {
    if ( $branch === 'udelnaya' ) {
        return array(
            'enabled' => 'udel_booking_enabled',
            'branch_label' => 'udel_branch_label',
            'telegram_link' => 'udel_telegram_link',
            'bot_token' => 'udel_bot_token',
            'chat_id' => 'udel_chat_id',
            'message_template' => 'udel_message_template',
        );
    }

    return array(
        'enabled' => 'adm_booking_enabled',
        'branch_label' => 'adm_branch_label',
        'telegram_link' => 'adm_telegram_link',
        'bot_token' => 'adm_bot_token',
        'chat_id' => 'adm_chat_id',
        'message_template' => 'adm_message_template',
    );
}

function garden_booking_get_field_value( $field_name ) {
    $db = garden_booking_db();
    if ( !$db ) return '';

    $prefix = defined('K_DB_TABLES_PREFIX') ? K_DB_TABLES_PREFIX : '';
    $template = 'booking-settings.php';
    $field_name = $db->real_escape_string($field_name);
    $template = $db->real_escape_string($template);

    $sql =
        "SELECT dt.value " .
        "FROM {$prefix}couch_templates t " .
        "INNER JOIN {$prefix}couch_pages p ON p.template_id=t.id AND p.is_master='1' " .
        "INNER JOIN {$prefix}couch_fields f ON f.template_id=t.id AND f.name='{$field_name}' " .
        "INNER JOIN {$prefix}couch_data_text dt ON dt.page_id=p.id AND dt.field_id=f.id " .
        "WHERE t.name='{$template}' " .
        "LIMIT 1";

    $res = $db->query($sql);
    if ( !$res ) return '';

    $row = $res->fetch_assoc();
    return $row ? trim((string)$row['value']) : '';
}

function garden_booking_secrets_fallback() {
    static $secrets = null;
    if ( $secrets !== null ) return $secrets;

    $secrets = array();
    $path = dirname(__DIR__, 2) . '/booking-secrets.php';
    if ( file_exists($path) ) {
        $loaded = include $path;
        if ( is_array($loaded) ) {
            $secrets = $loaded;
        }
    }
    return $secrets;
}

function garden_booking_default_message_template() {
    return "🍃 Garden Lounge — {branch_label} Новое бронирование 🍃\n\n" .
        "Имя: {name}\n" .
        "Тел: {phone}\n" .
        "Дата: {date}\n" .
        "Время: {time}\n" .
        "Гостей: {guests}";
}

function garden_booking_get_branch_settings( $branch ) {
    $branch = garden_booking_normalize_branch($branch);
    if ( !$branch ) return null;

    $map = garden_booking_field_map($branch);
    $fallback = garden_booking_secrets_fallback();
    $fallback_branch = isset($fallback[$branch]) && is_array($fallback[$branch]) ? $fallback[$branch] : array();

    $enabled = garden_booking_get_field_value($map['enabled']);
    if ( $enabled === '' ) $enabled = '1';

    $bot_token = garden_booking_get_field_value($map['bot_token']);
    if ( !$bot_token && !empty($fallback_branch['bot_token']) ) {
        $bot_token = trim((string)$fallback_branch['bot_token']);
    }

    $chat_id = garden_booking_get_field_value($map['chat_id']);
    if ( !$chat_id && !empty($fallback_branch['chat_id']) ) {
        $chat_id = trim((string)$fallback_branch['chat_id']);
    }

    $branch_label = garden_booking_get_field_value($map['branch_label']);
    if ( !$branch_label && !empty($fallback_branch['branch_label']) ) {
        $branch_label = trim((string)$fallback_branch['branch_label']);
    }
    if ( !$branch_label ) {
        $branch_label = ($branch === 'udelnaya') ? 'Удельная' : 'Адмиралтейская';
    }

    $message_template = garden_booking_get_field_value($map['message_template']);
    if ( !$message_template && !empty($fallback_branch['message_template']) ) {
        $message_template = trim((string)$fallback_branch['message_template']);
    }
    if ( !$message_template ) {
        $message_template = garden_booking_default_message_template();
    }

    return array(
        'branch' => $branch,
        'enabled' => ($enabled === '1'),
        'branch_label' => $branch_label,
        'telegram_link' => garden_booking_get_field_value($map['telegram_link']),
        'bot_token' => $bot_token,
        'chat_id' => $chat_id,
        'message_template' => $message_template,
    );
}

function garden_booking_normalize_branch( $branch ) {
    $branch = strtolower(trim((string)$branch));
    if ( in_array($branch, array('admiral', 'admiralteyskaya', 'adm'), true) ) return 'admiral';
    if ( in_array($branch, array('udelnaya', 'udel', 'udelka'), true) ) return 'udelnaya';
    return '';
}

function garden_booking_json_response( $status, $payload, $http_code = 200 ) {
    if ( !headers_sent() ) {
        http_response_code($http_code);
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode(array_merge(array('ok' => ($status === 'ok')), $payload), JSON_UNESCAPED_UNICODE);
    exit;
}

function garden_booking_cors_headers() {
    $origin = isset($_SERVER['HTTP_ORIGIN']) ? trim($_SERVER['HTTP_ORIGIN']) : '';
    $allowed = array(
        'https://garden-lounge.pro',
        'http://garden-lounge.pro',
        'https://www.garden-lounge.pro',
        'http://www.garden-lounge.pro',
    );
    if ( $origin && in_array($origin, $allowed, true) ) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Vary: Origin');
    }
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Max-Age: 86400');
}

function garden_booking_client_ip() {
    $ip = '';
    if ( !empty($_SERVER['HTTP_CF_CONNECTING_IP']) ) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    elseif ( !empty($_SERVER['REMOTE_ADDR']) ) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return trim($ip);
}

function garden_booking_rate_limit_ok( $ip ) {
    if ( !$ip ) return true;

    $dir = dirname(__DIR__, 2) . '/cache/booking-throttle';
    if ( !is_dir($dir) ) {
        @mkdir($dir, 0755, true);
    }
    $file = $dir . '/' . hash('sha256', $ip) . '.txt';
    $now = time();
    $window = 600;
    $max_requests = 5;

    $timestamps = array();
    if ( file_exists($file) ) {
        $raw = @file_get_contents($file);
        if ( $raw ) {
            foreach ( explode("\n", $raw) as $line ) {
                $ts = (int)$line;
                if ( $ts > ($now - $window) ) $timestamps[] = $ts;
            }
        }
    }

    if ( count($timestamps) >= $max_requests ) {
        return false;
    }

    $timestamps[] = $now;
    @file_put_contents($file, implode("\n", $timestamps));
    return true;
}

function garden_booking_clean_phone( $phone ) {
    $digits = preg_replace('/\D+/', '', (string)$phone);
    if ( strlen($digits) === 11 && $digits[0] === '8' ) {
        $digits = '7' . substr($digits, 1);
    }
    if ( strlen($digits) === 10 ) {
        $digits = '7' . $digits;
    }
    return $digits;
}

function garden_booking_format_phone( $digits ) {
    if ( strlen($digits) !== 11 ) return (string)$digits;
    return '+7 (' . substr($digits, 1, 3) . ') ' . substr($digits, 4, 3) . '-' . substr($digits, 7, 2) . '-' . substr($digits, 9, 2);
}

function garden_booking_render_template( $template, $vars ) {
    $search = array();
    $replace = array();
    foreach ( $vars as $key => $value ) {
        $search[] = '{' . $key . '}';
        $replace[] = (string)$value;
    }
    return str_replace($search, $replace, (string)$template);
}

function garden_booking_build_message( $settings, $data ) {
    $vars = array(
        'branch' => $settings['branch'],
        'branch_label' => $settings['branch_label'],
        'name' => $data['name'],
        'phone' => $data['phone_display'],
        'date' => $data['date'],
        'time' => $data['visit_time'],
        'guests' => $data['guests'],
        'source_url' => $data['source_url'],
    );

    return garden_booking_render_template($settings['message_template'], $vars);
}

function garden_booking_send_telegram( $settings, $message ) {
    if ( !$settings['enabled'] ) {
        return array(false, 'Отправка для этого филиала отключена в настройках.');
    }
    if ( !$settings['bot_token'] || !$settings['chat_id'] ) {
        return array(false, 'Не настроены Bot Token или Chat ID в CouchCMS → Общие → Бронирование Telegram.');
    }

    $url = 'https://api.telegram.org/bot' . $settings['bot_token'] . '/sendMessage';
    $payload = array(
        'chat_id' => $settings['chat_id'],
        'text' => $message,
        'disable_web_page_preview' => true,
    );

    $body = http_build_query($payload);
    $response = false;
    $http_code = 0;

    if ( function_exists('curl_init') ) {
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
        ));
        $response = curl_exec($ch);
        $http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    }
    else {
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $body,
                'timeout' => 15,
            ),
        ));
        $response = @file_get_contents($url, false, $context);
        if ( isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m) ) {
            $http_code = (int)$m[1];
        }
    }

    if ( $response === false ) {
        return array(false, 'Не удалось связаться с Telegram API.');
    }

    $json = json_decode($response, true);
    if ( $http_code >= 200 && $http_code < 300 && is_array($json) && !empty($json['ok']) ) {
        return array(true, '');
    }

    $description = '';
    if ( is_array($json) && !empty($json['description']) ) {
        $description = (string)$json['description'];
    }
    if ( !$description ) {
        $description = 'Telegram API вернул ошибку.';
    }
    return array(false, $description);
}

function garden_booking_handle_request() {
    garden_booking_cors_headers();

    if ( $_SERVER['REQUEST_METHOD'] === 'OPTIONS' ) {
        http_response_code(204);
        exit;
    }

    if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
        garden_booking_json_response('error', array('message' => 'Метод не поддерживается.'), 405);
    }

    $ip = garden_booking_client_ip();
    if ( !garden_booking_rate_limit_ok($ip) ) {
        garden_booking_json_response('error', array('message' => 'Слишком много заявок. Попробуйте позже.'), 429);
    }

    $honeypot = isset($_POST['website']) ? trim((string)$_POST['website']) : '';
    if ( $honeypot !== '' ) {
        garden_booking_json_response('ok', array('message' => 'OK'));
    }

    $branch = garden_booking_normalize_branch(isset($_POST['branch']) ? $_POST['branch'] : '');
    if ( !$branch ) {
        garden_booking_json_response('error', array('message' => 'Не указан филиал.'), 400);
    }

    $name = trim((string)(isset($_POST['name']) ? $_POST['name'] : ''));
    $phone_raw = trim((string)(isset($_POST['phone']) ? $_POST['phone'] : ''));
    $visit_day = trim((string)(isset($_POST['visit_day']) ? $_POST['visit_day'] : ''));
    $visit_month = trim((string)(isset($_POST['visit_month']) ? $_POST['visit_month'] : ''));
    $guests = trim((string)(isset($_POST['guests']) ? $_POST['guests'] : ''));
    $date = trim((string)(isset($_POST['date']) ? $_POST['date'] : ''));
    $visit_time = trim((string)(isset($_POST['visit_time']) ? $_POST['visit_time'] : '19:00'));

    if ( $name === '' || mb_strlen($name) > 80 ) {
        garden_booking_json_response('error', array('message' => 'Укажите корректное имя.'), 400);
    }

    $phone_digits = garden_booking_clean_phone($phone_raw);
    if ( strlen($phone_digits) !== 11 || $phone_digits[0] !== '7' ) {
        garden_booking_json_response('error', array('message' => 'Введите верный номер телефона.'), 400);
    }

    if ( !preg_match('/^\d{2}$/', $visit_day) || (int)$visit_day < 1 || (int)$visit_day > 31 ) {
        garden_booking_json_response('error', array('message' => 'Укажите корректный день.'), 400);
    }
    if ( !preg_match('/^\d{2}$/', $visit_month) || (int)$visit_month < 1 || (int)$visit_month > 12 ) {
        garden_booking_json_response('error', array('message' => 'Укажите корректный месяц.'), 400);
    }
    if ( !ctype_digit($guests) || (int)$guests < 1 || (int)$guests > 20 ) {
        garden_booking_json_response('error', array('message' => 'Укажите количество гостей.'), 400);
    }

    if ( $date === '' ) {
        $year = (int)date('Y');
        $date = $visit_day . '.' . $visit_month . '.' . $year;
    }
    if ( !preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $date) ) {
        garden_booking_json_response('error', array('message' => 'Укажите корректную дату.'), 400);
    }
    if ( !preg_match('/^\d{2}:\d{2}$/', $visit_time) ) {
        $visit_time = '19:00';
    }

    $settings = garden_booking_get_branch_settings($branch);
    if ( !$settings ) {
        garden_booking_json_response('error', array('message' => 'Неизвестный филиал.'), 400);
    }

    $source_url = ($branch === 'udelnaya')
        ? 'https://garden-lounge.pro/udelnaya/#reservation'
        : 'https://garden-lounge.pro/admiralteyskaya/#reservation';

    $message = garden_booking_build_message($settings, array(
        'name' => $name,
        'phone_display' => garden_booking_format_phone($phone_digits),
        'date' => $date,
        'visit_time' => $visit_time,
        'guests' => (string)(int)$guests,
        'source_url' => $source_url,
    ));

    list($sent, $error) = garden_booking_send_telegram($settings, $message);
    if ( !$sent ) {
        garden_booking_json_response('error', array('message' => $error), 502);
    }

    garden_booking_json_response('ok', array(
        'message' => 'Бронирование отправлено.',
        'branch' => $branch,
    ));
}
