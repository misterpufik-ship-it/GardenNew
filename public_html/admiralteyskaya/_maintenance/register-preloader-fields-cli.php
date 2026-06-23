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

$template_name = 'index.php';
$fields = array(
    array('name' => 'group_preloader', 'label' => 'Прелоадер при загрузке', 'type' => 'group', 'order' => 1),
    array('name' => 'preloader_intro', 'label' => 'Справка', 'type' => 'message', 'group' => 'group_preloader', 'order' => 2),
    array('name' => 'preloader_enabled', 'label' => 'Включить прелоадер', 'type' => 'dropdown', 'group' => 'group_preloader', 'order' => 3, 'opt_values' => 'Нет=0 | Да=1', 'default' => '1'),
    array('name' => 'preloader_scope_mode', 'label' => 'Режим показа', 'type' => 'dropdown', 'group' => 'group_preloader', 'order' => 4, 'opt_values' => 'На всех выбранных разделах=all | Только на выбранных=include | Везде, кроме выбранных=exclude', 'default' => 'all'),
    array('name' => 'preloader_sections', 'label' => 'Разделы сайта', 'type' => 'text', 'group' => 'group_preloader', 'order' => 5, 'default' => 'home, admiral, udelnaya, admiral_udelnaya'),
    array('name' => 'preloader_video', 'label' => 'Видео (путь или URL)', 'type' => 'text', 'group' => 'group_preloader', 'order' => 6, 'default' => '/video/preloader.mp4'),
    array('name' => 'preloader_min_time', 'label' => 'Минимальное время показа (мс)', 'type' => 'text', 'group' => 'group_preloader', 'order' => 7, 'default' => '1200'),
    array('name' => 'preloader_max_time', 'label' => 'Максимальное время показа (мс)', 'type' => 'text', 'group' => 'group_preloader', 'order' => 8, 'default' => '8000'),
    array('name' => 'preloader_playback_rate', 'label' => 'Скорость воспроизведения', 'type' => 'text', 'group' => 'group_preloader', 'order' => 9, 'default' => '1.3'),
    array('name' => 'preloader_mobile_object_fit', 'label' => 'Видео на мобильных', 'type' => 'dropdown', 'group' => 'group_preloader', 'order' => 10, 'opt_values' => 'Вписать без обрезки=contain | На весь экран=cover', 'default' => 'contain'),
);

$tpl = $DB->select(K_DB_TABLES_PREFIX . 'couch_templates', array('id'), "name = '" . $DB->sanitize($template_name) . "'");
if (!count($tpl)) {
    fwrite(STDERR, "Template not found: {$template_name}\n");
    exit(1);
}
$template_id = (int)$tpl[0]['id'];

$page = $DB->select(K_DB_TABLES_PREFIX . 'couch_pages', array('id'), "template_id = {$template_id} AND is_master = '1'");
if (!count($page)) {
    fwrite(STDERR, "Master page not found for {$template_name}\n");
    exit(1);
}
$page_id = (int)$page[0]['id'];

$added = 0;
foreach ($fields as $field) {
    $name = $field['name'];
    $exists = $DB->select(K_DB_TABLES_PREFIX . 'couch_fields', array('id'), "template_id = {$template_id} AND name = '" . $DB->sanitize($name) . "'");
    if (count($exists)) {
        echo "Field exists: {$name}\n";
        continue;
    }

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

    if (!empty($field['default']) && $field['type'] !== 'group' && $field['type'] !== 'message') {
        $DB->insert(K_DB_TABLES_PREFIX . 'couch_data_text', array(
            'page_id' => $page_id,
            'field_id' => $field_id,
            'value' => $field['default'],
        ));
    }

    $added++;
    echo "Added field: {$name}\n";
}

if (isset($FUNCS) && method_exists($FUNCS, 'invalidate_cache')) {
    $FUNCS->invalidate_cache();
}

echo "Done. Added {$added} field(s).\n";
