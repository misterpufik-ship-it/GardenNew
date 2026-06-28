<?php
if (php_sapi_name() !== 'cli' && !defined('GL_SKIP_CLI_CHECK')) {
    http_response_code(403);
    exit("CLI only\n");
}

$config = __DIR__ . '/../couch/config.php';
if (!is_file($config)) {
    fwrite(STDERR, "CouchCMS config not found: {$config}\n");
    exit(1);
}

define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;
require_once K_COUCH_DIR . 'functions.php';

global $DB, $FUNCS;

$template_name = 'preloader-settings.php';
$fields = array(
    array('name' => 'preloader_intro', 'label' => 'Справка', 'type' => 'message', 'order' => 1),
    array('name' => 'group_preloader_main', 'label' => 'Включение и показ', 'type' => 'group', 'order' => 10),
    array('name' => 'preloader_enabled', 'label' => 'Включить прелоадер', 'type' => 'dropdown', 'group' => 'group_preloader_main', 'order' => 11, 'opt_values' => 'Нет=0 | Да=1', 'default' => '1'),
    array('name' => 'preloader_scope_mode', 'label' => 'Режим показа', 'type' => 'dropdown', 'group' => 'group_preloader_main', 'order' => 12, 'opt_values' => 'На всех выбранных разделах=all | Только на выбранных=include | Везде, кроме выбранных=exclude', 'default' => 'all'),
    array('name' => 'preloader_sections', 'label' => 'Разделы сайта', 'type' => 'text', 'group' => 'group_preloader_main', 'order' => 13, 'default' => 'home, admiral, udelnaya, admiral_udelnaya'),
    array('name' => 'preloader_video', 'label' => 'Видео (общее, устар.)', 'type' => 'text', 'group' => 'group_preloader_main', 'order' => 14, 'default' => '/video/preloader.mp4'),
    array('name' => 'group_preloader_timing', 'label' => 'Скорость и тайминги', 'type' => 'group', 'order' => 20),
    array('name' => 'preloader_min_time', 'label' => 'Минимальное время показа (мс)', 'type' => 'text', 'group' => 'group_preloader_timing', 'order' => 21, 'default' => '1200'),
    array('name' => 'preloader_max_time', 'label' => 'Максимальное время показа (мс)', 'type' => 'text', 'group' => 'group_preloader_timing', 'order' => 22, 'default' => '8000'),
    array('name' => 'preloader_playback_rate', 'label' => 'Скорость воспроизведения', 'type' => 'text', 'group' => 'group_preloader_timing', 'order' => 23, 'default' => '1.3'),
    array('name' => 'group_preloader_desktop', 'label' => 'Десктоп (от 768px)', 'type' => 'group', 'order' => 30),
    array('name' => 'preloader_video_desktop', 'label' => 'Видео для десктопа', 'type' => 'text', 'group' => 'group_preloader_desktop', 'order' => 31, 'default' => '/video/preloader.mp4'),
    array('name' => 'preloader_desktop_object_fit', 'label' => 'Как вписать видео', 'type' => 'dropdown', 'group' => 'group_preloader_desktop', 'order' => 32, 'opt_values' => 'На весь экран=cover | Вписать без обрезки=contain', 'default' => 'cover'),
    array('name' => 'group_preloader_mobile', 'label' => 'Мобильные (до 767px)', 'type' => 'group', 'order' => 40),
    array('name' => 'preloader_video_mobile', 'label' => 'Видео для мобильных', 'type' => 'text', 'group' => 'group_preloader_mobile', 'order' => 41, 'default' => '/video/preloader.mp4'),
    array('name' => 'preloader_mobile_object_fit', 'label' => 'Как вписать видео', 'type' => 'dropdown', 'group' => 'group_preloader_mobile', 'order' => 42, 'opt_values' => 'Вписать без обрезки=contain | На весь экран=cover', 'default' => 'contain'),
);

$tpl = $DB->select(K_DB_TABLES_PREFIX . 'couch_templates', array('id'), "name = '" . $DB->sanitize($template_name) . "'");
if (!count($tpl)) {
    fwrite(STDERR, "Template not found: {$template_name}. Open it once in admin to register.\n");
    exit(1);
}
$template_id = (int)$tpl[0]['id'];

$page = $DB->select(K_DB_TABLES_PREFIX . 'couch_pages', array('id'), "template_id = {$template_id} AND is_master = '1'");
if (!count($page)) {
    fwrite(STDERR, "Master page not found for {$template_name}\n");
    exit(1);
}
$page_id = (int)$page[0]['id'];

$legacy_tpl = $DB->select(K_DB_TABLES_PREFIX . 'couch_templates', array('id'), "name = 'index.php'");
$legacy_page_id = 0;
if (count($legacy_tpl)) {
    $legacy_page = $DB->select(K_DB_TABLES_PREFIX . 'couch_pages', array('id'), "template_id = " . (int)$legacy_tpl[0]['id'] . " AND is_master = '1'");
    if (count($legacy_page)) {
        $legacy_page_id = (int)$legacy_page[0]['id'];
    }
}

$added = 0;
$migrated = 0;
foreach ($fields as $field) {
    $name = $field['name'];
    $exists = $DB->select(K_DB_TABLES_PREFIX . 'couch_fields', array('id'), "template_id = {$template_id} AND name = '" . $DB->sanitize($name) . "'");
    if (count($exists)) {
        $field_id = (int)$exists[0]['id'];
        echo "Field exists: {$name}\n";
    } else {
        $cols = array(
            'template_id' => $template_id,
            'name' => $name,
            'label' => $field['label'],
            'k_type' => $field['type'],
            'hidden' => '0',
            'search_type' => 'text',
            'k_order' => (int)$field['order'],
        );
        if (!empty($field['group'])) {
            $cols['group'] = $field['group'];
        }
        if (!empty($field['opt_values'])) {
            $cols['opt_values'] = $field['opt_values'];
        }

        $field_id = $DB->insert(K_DB_TABLES_PREFIX . 'couch_fields', $cols);
        if (!$field_id) {
            fwrite(STDERR, "Failed to insert field: {$name}\n");
            continue;
        }
        $added++;
        echo "Added field: {$name}\n";
    }

    if ($field['type'] === 'group' || $field['type'] === 'message') {
        continue;
    }

    $has_value = $DB->select(K_DB_TABLES_PREFIX . 'couch_data_text', array('id'), "page_id = {$page_id} AND field_id = {$field_id}");
    if (count($has_value)) {
        continue;
    }

    $value = !empty($field['default']) ? $field['default'] : '';
    if ($legacy_page_id) {
        $legacy_field = $DB->select(
            K_DB_TABLES_PREFIX . 'couch_fields',
            array('id'),
            "template_id = " . (int)$legacy_tpl[0]['id'] . " AND name = '" . $DB->sanitize($name) . "'"
        );
        if (count($legacy_field)) {
            $legacy_data = $DB->select(
                K_DB_TABLES_PREFIX . 'couch_data_text',
                array('value'),
                "page_id = {$legacy_page_id} AND field_id = " . (int)$legacy_field[0]['id']
            );
            if (count($legacy_data) && trim((string)$legacy_data[0]['value']) !== '') {
                $value = trim((string)$legacy_data[0]['value']);
            }
        }
    }

    if ($value !== '') {
        $DB->insert(K_DB_TABLES_PREFIX . 'couch_data_text', array(
            'page_id' => $page_id,
            'field_id' => $field_id,
            'value' => $value,
        ));
        $migrated++;
        echo "Migrated value: {$name}\n";
    }
}

if (isset($FUNCS) && method_exists($FUNCS, 'invalidate_cache')) {
    $FUNCS->invalidate_cache();
}

echo "Done. Added {$added} field(s), migrated {$migrated} value(s).\n";
