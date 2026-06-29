<?php
if (!defined('GARDEN_PRELOADER_LIB')) {
    define('GARDEN_PRELOADER_LIB', 1);
}

function garden_preloader_config_path()
{
    static $path = null;
    if ($path !== null) {
        return $path;
    }

    $candidates = array(
        dirname(__DIR__) . '/admiralteyskaya/couch/config.php',
        dirname(__DIR__, 2) . '/admiralteyskaya/couch/config.php',
        dirname(__DIR__) . '/admiralteyskaya/config.php',
        dirname(__DIR__, 2) . '/admiralteyskaya/config.php',
    );

    foreach ($candidates as $candidate) {
        if (file_exists($candidate)) {
            $path = $candidate;
            return $path;
        }
    }

    return null;
}

function garden_preloader_db()
{
    static $db = null;
    if ($db instanceof mysqli) {
        return $db;
    }

    $config = garden_preloader_config_path();
    if (!$config) {
        return null;
    }

    if (!defined('K_COUCH_DIR')) {
        define('K_COUCH_DIR', dirname($config) . '/');
    }
    require_once $config;

    $host = K_DB_HOST;
    $port = 3306;
    if (strpos($host, ':') !== false) {
        list($host, $port) = explode(':', $host, 2);
    }

    mysqli_report(MYSQLI_REPORT_OFF);
    $db = @new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
    if ($db->connect_errno) {
        $db = null;
    }

    return $db;
}

function garden_preloader_get_field_value($field_name)
{
    static $cache = array();
    if (array_key_exists($field_name, $cache)) {
        return $cache[$field_name];
    }

    $db = garden_preloader_db();
    if (!$db) {
        $cache[$field_name] = '';
        return '';
    }

    $prefix = defined('K_DB_TABLES_PREFIX') ? K_DB_TABLES_PREFIX : '';
    $field_name_esc = $db->real_escape_string($field_name);

    foreach (array('preloader-settings.php', 'index.php') as $template) {
        $template_esc = $db->real_escape_string($template);
        $sql =
            "SELECT dt.value " .
            "FROM {$prefix}couch_templates t " .
            "INNER JOIN {$prefix}couch_pages p ON p.template_id=t.id AND p.is_master='1' " .
            "INNER JOIN {$prefix}couch_fields f ON f.template_id=t.id AND f.name='{$field_name_esc}' " .
            "INNER JOIN {$prefix}couch_data_text dt ON dt.page_id=p.id AND dt.field_id=f.id " .
            "WHERE t.name='{$template_esc}' " .
            "LIMIT 1";

        $res = $db->query($sql);
        if ($res) {
            $row = $res->fetch_assoc();
            if ($row && trim((string)$row['value']) !== '') {
                $cache[$field_name] = trim((string)$row['value']);
                return $cache[$field_name];
            }
        }
    }

    $cache[$field_name] = '';
    return '';
}

function garden_preloader_default_settings()
{
    return array(
        'enabled' => true,
        'scope_mode' => 'all',
        'sections' => array('home', 'admiral', 'udelnaya', 'admiral_udelnaya'),
        'video' => '/video/preloader.mp4',
        'video_desktop' => '/video/preloader.mp4',
        'video_mobile' => '/video/preloader-mobile.mp4',
        'min_time' => 1200,
        'max_time' => 8000,
        'playback_rate' => 1.3,
        'desktop_object_fit' => 'cover',
        'mobile_object_fit' => 'contain',
    );
}

function garden_preloader_parse_sections($raw)
{
    $sections = array();
    foreach (preg_split('/\s*,\s*/', (string)$raw) as $part) {
        $part = strtolower(trim($part));
        if ($part !== '') {
            $sections[] = $part;
        }
    }
    return $sections;
}

function garden_preloader_resolve_video_url($value)
{
    $value = trim((string)$value);
    if ($value === '') {
        return garden_preloader_default_settings()['video'];
    }
    if (preg_match('#^https?://#i', $value)) {
        return $value;
    }
    if ($value[0] === '/') {
        return $value;
    }
    return '/' . $value;
}

function garden_preloader_get_settings()
{
    static $settings = null;
    if ($settings !== null) {
        return $settings;
    }

    $defaults = garden_preloader_default_settings();
    $enabled_raw = garden_preloader_get_field_value('preloader_enabled');
    $enabled = ($enabled_raw === '' || $enabled_raw === '1');

    $scope_mode = garden_preloader_get_field_value('preloader_scope_mode');
    if (!in_array($scope_mode, array('all', 'include', 'exclude'), true)) {
        $scope_mode = $defaults['scope_mode'];
    }

    $sections_raw = garden_preloader_get_field_value('preloader_sections');
    $sections = garden_preloader_parse_sections($sections_raw);
    if (!$sections) {
        $sections = $defaults['sections'];
    }

    $video_raw = garden_preloader_get_field_value('preloader_video');
    $video = garden_preloader_resolve_video_url($video_raw);

    $video_desktop_raw = garden_preloader_get_field_value('preloader_video_desktop');
    $video_desktop = garden_preloader_resolve_video_url($video_desktop_raw !== '' ? $video_desktop_raw : $video);

    $video_mobile_raw = garden_preloader_get_field_value('preloader_video_mobile');
    if ($video_mobile_raw !== '') {
        $video_mobile = garden_preloader_resolve_video_url($video_mobile_raw);
    } else {
        $video_mobile = garden_preloader_resolve_video_url($defaults['video_mobile']);
    }

    $min_time = (int)garden_preloader_get_field_value('preloader_min_time');
    if ($min_time < 0) {
        $min_time = $defaults['min_time'];
    }

    $max_time = (int)garden_preloader_get_field_value('preloader_max_time');
    if ($max_time < 1000) {
        $max_time = $defaults['max_time'];
    }

    $playback_rate = (float)str_replace(',', '.', garden_preloader_get_field_value('preloader_playback_rate'));
    if ($playback_rate < 0.5 || $playback_rate > 3) {
        $playback_rate = $defaults['playback_rate'];
    }

    $mobile_object_fit = garden_preloader_get_field_value('preloader_mobile_object_fit');
    if (!in_array($mobile_object_fit, array('contain', 'cover'), true)) {
        $mobile_object_fit = $defaults['mobile_object_fit'];
    }

    $desktop_object_fit = garden_preloader_get_field_value('preloader_desktop_object_fit');
    if (!in_array($desktop_object_fit, array('contain', 'cover'), true)) {
        $desktop_object_fit = $defaults['desktop_object_fit'];
    }

    $settings = array(
        'enabled' => $enabled,
        'scope_mode' => $scope_mode,
        'sections' => $sections,
        'video' => $video,
        'video_desktop' => $video_desktop,
        'video_mobile' => $video_mobile,
        'min_time' => $min_time,
        'max_time' => $max_time,
        'playback_rate' => $playback_rate,
        'desktop_object_fit' => $desktop_object_fit,
        'mobile_object_fit' => $mobile_object_fit,
    );

    return $settings;
}

function garden_preloader_current_path()
{
    $uri = isset($_SERVER['REQUEST_URI']) ? (string)$_SERVER['REQUEST_URI'] : '/';
    $path = parse_url($uri, PHP_URL_PATH);
    if (!is_string($path) || $path === '') {
        return '/';
    }
    return $path;
}

function garden_preloader_detect_section($path)
{
    $path = strtolower($path);

    if (preg_match('#^/admiralteyskaya/udelnaya(?:/|$)#', $path)) {
        return 'admiral_udelnaya';
    }
    if (preg_match('#^/udelnaya(?:/|$)#', $path)) {
        return 'udelnaya';
    }
    if (preg_match('#^/admiralteyskaya(?:/|$)#', $path)) {
        return 'admiral';
    }
    if ($path === '/' || preg_match('#^/index\.php$#', $path)) {
        return 'home';
    }

    return 'other';
}

function garden_preloader_section_allowed($settings, $section)
{
    if ($settings['scope_mode'] === 'all') {
        return true;
    }

    $selected = $settings['sections'];
    $in_list = in_array($section, $selected, true);

    if ($settings['scope_mode'] === 'include') {
        return $in_list;
    }
    if ($settings['scope_mode'] === 'exclude') {
        return !$in_list;
    }

    return true;
}

function garden_preloader_should_show($path = null)
{
    $settings = garden_preloader_get_settings();
    if (!$settings['enabled']) {
        return false;
    }

    if ($path === null) {
        $path = garden_preloader_current_path();
    }

    $section = garden_preloader_detect_section($path);
    if ($section === 'other') {
        return $settings['scope_mode'] !== 'include';
    }

    return garden_preloader_section_allowed($settings, $section);
}
