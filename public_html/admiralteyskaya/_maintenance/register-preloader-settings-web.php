<?php
/**
 * Register preloader-settings.php fields and default values in CouchCMS DB.
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/register-preloader-settings-web.php?key=<md5>
 * key = md5('garden-lounge-register-preloader-fields')
 */
$expectedKey = md5('garden-lounge-register-preloader-fields');
if ((isset($_GET['key']) ? $_GET['key'] : '') !== $expectedKey) {
    http_response_code(403);
    exit("Forbidden\n");
}
header('Content-Type: text/plain; charset=utf-8');

$root = realpath(__DIR__ . '/..');
$config = $root . '/couch/config.php';
if (!is_file($config)) {
    exit("CouchCMS config not found\n");
}

define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ?: 3306;
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
}

mysqli_report(MYSQLI_REPORT_OFF);
$db = @new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
if ($db->connect_errno) {
    exit("DB connection failed: {$db->connect_error}\n");
}
$db->set_charset('utf8');

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';
$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$dataText = K_DB_TABLES_PREFIX . 'couch_data_text';

function gl_qval($db, $value)
{
    if ($value === null) {
        return 'NULL';
    }
    return "'" . $db->real_escape_string((string)$value) . "'";
}

function gl_fetch_one($db, $sql)
{
    $res = $db->query($sql);
    return $res ? $res->fetch_assoc() : null;
}

$templateName = 'preloader-settings.php';
$fieldDefs = array(
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

$tpl = gl_fetch_one($db, "SELECT id FROM `{$templates}` WHERE name='" . $db->real_escape_string($templateName) . "' LIMIT 1");
if (!$tpl) {
    exit("Template not found: {$templateName}. Run register-preloader-template-web.php first.\n");
}
$templateId = (int)$tpl['id'];
echo "Template {$templateName} (#{$templateId})\n";

$page = gl_fetch_one($db, "SELECT id FROM `{$pages}` WHERE template_id={$templateId} AND is_master='1' LIMIT 1");
if (!$page) {
    exit("Master page not found for {$templateName}\n");
}
$pageId = (int)$page['id'];
echo "Master page #{$pageId}\n";

$legacyTpl = gl_fetch_one($db, "SELECT id FROM `{$templates}` WHERE name='index.php' LIMIT 1");
$legacyPageId = 0;
if ($legacyTpl) {
    $legacyPage = gl_fetch_one($db, "SELECT id FROM `{$pages}` WHERE template_id=" . (int)$legacyTpl['id'] . " AND is_master='1' LIMIT 1");
    if ($legacyPage) {
        $legacyPageId = (int)$legacyPage['id'];
    }
}

$added = 0;
$migrated = 0;
foreach ($fieldDefs as $field) {
    $name = $field['name'];
    $row = gl_fetch_one(
        $db,
        "SELECT id FROM `{$fields}` WHERE template_id={$templateId} AND name='" . $db->real_escape_string($name) . "' LIMIT 1"
    );
    if ($row) {
        $fieldId = (int)$row['id'];
        echo "Field exists: {$name}\n";
    } else {
        $cols = array(
            'template_id' => (string)$templateId,
            'name' => $name,
            'label' => $field['label'],
            'k_type' => $field['type'],
            'hidden' => '0',
            'search_type' => 'text',
            'k_order' => (string)(int)$field['order'],
        );
        if (!empty($field['group'])) {
            $cols['group'] = $field['group'];
        }
        if (!empty($field['opt_values'])) {
            $cols['opt_values'] = $field['opt_values'];
        }
        $colNames = array_keys($cols);
        $vals = array();
        foreach (array_values($cols) as $v) {
            $vals[] = gl_qval($db, $v);
        }
        $sql = "INSERT INTO `{$fields}` (`" . implode('`,`', $colNames) . "`) VALUES (" . implode(',', $vals) . ")";
        if (!$db->query($sql)) {
            echo "Failed to insert field {$name}: {$db->error}\n";
            continue;
        }
        $fieldId = (int)$db->insert_id;
        $added++;
        echo "Added field: {$name} (#{$fieldId})\n";
    }

    if ($field['type'] === 'group' || $field['type'] === 'message') {
        continue;
    }

    $hasValue = gl_fetch_one($db, "SELECT id FROM `{$dataText}` WHERE page_id={$pageId} AND field_id={$fieldId} LIMIT 1");
    if ($hasValue) {
        continue;
    }

    $value = !empty($field['default']) ? $field['default'] : '';
    if ($legacyPageId) {
        $legacyField = gl_fetch_one(
            $db,
            "SELECT id FROM `{$fields}` WHERE template_id=" . (int)$legacyTpl['id'] . " AND name='" . $db->real_escape_string($name) . "' LIMIT 1"
        );
        if ($legacyField) {
            $legacyData = gl_fetch_one(
                $db,
                "SELECT value FROM `{$dataText}` WHERE page_id={$legacyPageId} AND field_id=" . (int)$legacyField['id'] . " LIMIT 1"
            );
            if ($legacyData && trim((string)$legacyData['value']) !== '') {
                $value = trim((string)$legacyData['value']);
            }
        }
    }

    if ($value === '') {
        continue;
    }

    $sql = "INSERT INTO `{$dataText}` (`page_id`,`field_id`,`value`) VALUES ({$pageId},{$fieldId}," . gl_qval($db, $value) . ")";
    if ($db->query($sql)) {
        $migrated++;
        echo "Set value: {$name}\n";
    } else {
        echo "Failed value {$name}: {$db->error}\n";
    }
}

$cacheDir = $root . '/couch/cache';
if (is_dir($cacheDir)) {
    foreach (glob($cacheDir . '/*.dat') as $file) {
        @unlink($file);
    }
    echo "Cleared couch cache\n";
}

echo "Done. Added {$added} field(s), set {$migrated} value(s).\n";
