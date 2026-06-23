<?php
if ( !defined('GARDEN_AGE_GATE_LIB') ) {
    define('GARDEN_AGE_GATE_LIB', 1);
}

function garden_age_gate_config_path() {
    static $path = null;
    if ( $path !== null ) return $path;

    $candidates = array(
        dirname(__DIR__) . '/admiralteyskaya/config.php',
        dirname(__DIR__, 2) . '/admiralteyskaya/config.php',
    );
    foreach ( $candidates as $candidate ) {
        if ( file_exists($candidate) ) {
            $path = $candidate;
            return $path;
        }
    }
    return null;
}

function garden_age_gate_db() {
    static $db = null;
    if ( $db instanceof mysqli ) return $db;

    $config = garden_age_gate_config_path();
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

function garden_age_gate_get_field_value( $field_name ) {
    $db = garden_age_gate_db();
    if ( !$db ) return '';

    $prefix = defined('K_DB_TABLES_PREFIX') ? K_DB_TABLES_PREFIX : '';
    $template = 'age-gate-settings.php';
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

function garden_age_gate_default_settings() {
    return array(
        'enabled' => true,
        'scope_mode' => 'all',
        'sections' => array('home', 'admiral', 'udelnaya', 'menu'),
        'storage_mode' => 'both',
        'remember_days' => 365,
        'logo' => '/admiralteyskaya/couch/uploads/image/logo3.webp',
        'badge' => '18+',
        'color_gold' => '#C5A059',
        'color_gold_dark' => '#8e7037',
        'color_gold_light' => '#FFEebb',
        'overlay_opacity' => 92,
        'title' => 'Вам уже исполнилось 18 лет?',
        'welcome_admiral' => 'Добро пожаловать в Garden Lounge.',
        'welcome_udelnaya' => 'Добро пожаловать в Garden Lounge на Удельной.',
        'description' => 'Перед входом подтвердите возраст, чтобы продолжить знакомство с нашим садом. Сайт содержит информацию о заведении, где представлены кальяны и табачная продукция.',
        'btn_yes' => 'Да, войти',
        'btn_no' => 'Нет, покинуть сайт',
        'denied_text' => 'Дальнейшее отображение материалов сайта невозможно',
    );
}

function garden_age_gate_resolve_image_url( $value ) {
    $value = trim((string)$value);
    if ( $value === '' ) {
        return '';
    }
    if ( preg_match('#^https?://#i', $value) ) {
        return $value;
    }
    if ( $value[0] === ':' ) {
        return '/admiralteyskaya/couch/uploads/image/' . ltrim($value, ':');
    }
    if ( $value[0] === '/' ) {
        return $value;
    }
    return '/admiralteyskaya/couch/uploads/image/' . $value;
}

function garden_age_gate_parse_sections( $raw ) {
    $sections = array();
    foreach ( preg_split('/\s*,\s*/', (string)$raw) as $part ) {
        $part = strtolower(trim($part));
        if ( $part !== '' ) {
            $sections[] = $part;
        }
    }
    return $sections;
}

function garden_age_gate_get_settings() {
    static $settings = null;
    if ( $settings !== null ) {
        return $settings;
    }

    $defaults = garden_age_gate_default_settings();
    $enabled_raw = garden_age_gate_get_field_value('ag_enabled');
    $enabled = ($enabled_raw === '' || $enabled_raw === '1');

    $scope_mode = garden_age_gate_get_field_value('ag_scope_mode');
    if ( !in_array($scope_mode, array('all', 'include', 'exclude'), true) ) {
        $scope_mode = $defaults['scope_mode'];
    }

    $sections_raw = garden_age_gate_get_field_value('ag_sections');
    $sections = garden_age_gate_parse_sections($sections_raw);
    if ( !$sections ) {
        $sections = $defaults['sections'];
    }

    $storage_mode = garden_age_gate_get_field_value('ag_storage_mode');
    if ( !in_array($storage_mode, array('both', 'cookie', 'session'), true) ) {
        $storage_mode = $defaults['storage_mode'];
    }

    $remember_days = (int)garden_age_gate_get_field_value('ag_remember_days');
    if ( $remember_days < 0 ) {
        $remember_days = $defaults['remember_days'];
    }

    $logo_raw = garden_age_gate_get_field_value('ag_logo');
    $logo = garden_age_gate_resolve_image_url($logo_raw);
    if ( $logo === '' ) {
        $logo = $defaults['logo'];
    }

    $overlay_opacity = (int)garden_age_gate_get_field_value('ag_overlay_opacity');
    if ( $overlay_opacity < 0 || $overlay_opacity > 100 ) {
        $overlay_opacity = $defaults['overlay_opacity'];
    }

    $text_fields = array(
        'badge' => 'ag_badge',
        'color_gold' => 'ag_color_gold',
        'color_gold_dark' => 'ag_color_gold_dark',
        'color_gold_light' => 'ag_color_gold_light',
        'title' => 'ag_title',
        'welcome_admiral' => 'ag_welcome_admiral',
        'welcome_udelnaya' => 'ag_welcome_udelnaya',
        'description' => 'ag_description',
        'btn_yes' => 'ag_btn_yes',
        'btn_no' => 'ag_btn_no',
        'denied_text' => 'ag_denied_text',
    );

    $settings = array(
        'enabled' => $enabled,
        'scope_mode' => $scope_mode,
        'sections' => $sections,
        'storage_mode' => $storage_mode,
        'remember_days' => $remember_days,
        'logo' => $logo,
        'overlay_opacity' => $overlay_opacity,
    );

    foreach ( $text_fields as $key => $field ) {
        $value = garden_age_gate_get_field_value($field);
        $settings[$key] = ($value !== '') ? $value : $defaults[$key];
    }

    return $settings;
}

function garden_age_gate_current_path() {
    $uri = isset($_SERVER['REQUEST_URI']) ? (string)$_SERVER['REQUEST_URI'] : '/';
    $path = parse_url($uri, PHP_URL_PATH);
    if ( !is_string($path) || $path === '' ) {
        return '/';
    }
    return $path;
}

function garden_age_gate_detect_section( $path ) {
    $path = strtolower($path);

    if ( preg_match('#^/admiralteyskaya/menu(?:/|$)#', $path) ) {
        return 'menu';
    }
    if ( preg_match('#^/udelnaya/menu(?:/|$)#', $path) ) {
        return 'menu';
    }
    if ( preg_match('#^/menu(?:/|$)#', $path) ) {
        return 'menu';
    }
    if ( preg_match('#^/admiralteyskaya/udelnaya(?:/|$)#', $path) ) {
        return 'udelnaya';
    }
    if ( preg_match('#^/udelnaya(?:/|$)#', $path) ) {
        return 'udelnaya';
    }
    if ( preg_match('#^/admiralteyskaya(?:/|$)#', $path) ) {
        return 'admiral';
    }
    if ( $path === '/' || preg_match('#^/index\.php$#', $path) ) {
        return 'home';
    }

    return 'other';
}

function garden_age_gate_section_allowed( $settings, $section ) {
    if ( $settings['scope_mode'] === 'all' ) {
        return true;
    }

    $selected = $settings['sections'];
    $in_list = in_array($section, $selected, true);

    if ( $settings['scope_mode'] === 'include' ) {
        return $in_list;
    }
    if ( $settings['scope_mode'] === 'exclude' ) {
        return !$in_list;
    }

    return true;
}

function garden_age_gate_should_show( $path = null ) {
    $settings = garden_age_gate_get_settings();
    if ( !$settings['enabled'] ) {
        return false;
    }

    if ( $path === null ) {
        $path = garden_age_gate_current_path();
    }

    $section = garden_age_gate_detect_section($path);
    if ( $section === 'other' ) {
        return $settings['scope_mode'] !== 'include';
    }

    return garden_age_gate_section_allowed($settings, $section);
}

function garden_age_gate_js_config( $path = null ) {
    $settings = garden_age_gate_get_settings();
    $section = garden_age_gate_detect_section($path !== null ? $path : garden_age_gate_current_path());

    $welcome = $settings['welcome_admiral'];
    if ( $section === 'udelnaya' ) {
        $welcome = $settings['welcome_udelnaya'];
    }

    return array(
        'storageMode' => $settings['storage_mode'],
        'rememberDays' => (int)$settings['remember_days'],
        'logo' => $settings['logo'],
        'badge' => $settings['badge'],
        'title' => $settings['title'],
        'welcome' => $welcome,
        'description' => $settings['description'],
        'btnYes' => $settings['btn_yes'],
        'btnNo' => $settings['btn_no'],
        'deniedText' => $settings['denied_text'],
        'colors' => array(
            'gold' => $settings['color_gold'],
            'goldDark' => $settings['color_gold_dark'],
            'goldLight' => $settings['color_gold_light'],
            'overlayOpacity' => (int)$settings['overlay_opacity'],
        ),
    );
}
