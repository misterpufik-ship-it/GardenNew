<?php
/**
 * Register layout-spacing.php template + fields in CouchCMS DB.
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/register-layout-spacing-web.php?key=<md5>
 * key = md5('garden-lounge-register-layout-spacing')
 */
$expectedKey = md5('garden-lounge-register-layout-spacing');
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

$templateName = 'layout-spacing.php';
$title = 'Отступы между блоками';
$order = 6;
$cloneFrom = 'preloader-settings.php';

$row = gl_fetch_one($db, "SELECT id FROM `{$templates}` WHERE name='" . $db->real_escape_string($templateName) . "' LIMIT 1");
if (!$row) {
    $sample = gl_fetch_one($db, "SELECT * FROM `{$templates}` WHERE name='" . $db->real_escape_string($cloneFrom) . "' LIMIT 1");
    if (!$sample) {
        exit("No clone source template found\n");
    }
    unset($sample['id']);
    $sample['name'] = $templateName;
    $sample['title'] = $title;
    $sample['executable'] = '0';
    $sample['hidden'] = '0';
    $sample['clonable'] = '0';
    if (isset($sample['order'])) {
        $sample['order'] = (string)$order;
    }
    $cols = array_keys($sample);
    $vals = array();
    foreach (array_values($sample) as $v) {
        $vals[] = gl_qval($db, $v);
    }
    $sql = "INSERT INTO `{$templates}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $vals) . ")";
    if (!$db->query($sql)) {
        exit("Template insert failed: {$db->error}\n");
    }
    $templateId = (int)$db->insert_id;
    echo "Created template {$templateName} (#{$templateId})\n";
} else {
    $templateId = (int)$row['id'];
    $db->query(
        "UPDATE `{$templates}` SET executable='0', hidden='0', title='" .
        $db->real_escape_string($title) . "', `order`='" . (int)$order . "' WHERE id={$templateId} LIMIT 1"
    );
    echo "Template {$templateName} exists (#{$templateId})\n";
}

$page = gl_fetch_one($db, "SELECT id FROM `{$pages}` WHERE template_id={$templateId} AND is_master='1' LIMIT 1");
if (!$page) {
    $ref = gl_fetch_one($db, "SELECT * FROM `{$pages}` WHERE template_id={$templateId} LIMIT 1");
    if (!$ref) {
        $ref = gl_fetch_one($db, "SELECT * FROM `{$pages}` WHERE template_id=1 LIMIT 1");
    }
    if ($ref) {
        unset($ref['id']);
        $now = date('Y-m-d H:i:s');
        $ref['template_id'] = (string)$templateId;
        $ref['page_title'] = $title;
        $ref['page_name'] = 'index';
        $ref['creation_date'] = $now;
        $ref['modification_date'] = $now;
        $ref['publish_date'] = $now;
        $ref['is_master'] = '1';
        $cols = array_keys($ref);
        $vals = array();
        foreach (array_values($ref) as $v) {
            $vals[] = gl_qval($db, $v);
        }
        $sql = "INSERT INTO `{$pages}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $vals) . ")";
        if ($db->query($sql)) {
            echo "Created master page\n";
        }
    }
} else {
    echo "Master page #" . (int)$page['id'] . "\n";
}

$page = gl_fetch_one($db, "SELECT id FROM `{$pages}` WHERE template_id={$templateId} AND is_master='1' LIMIT 1");
if (!$page) {
    exit("Master page missing\n");
}
$pageId = (int)$page['id'];

$fieldDefs = array(
    array('name' => 'spacing_info', 'label' => 'Справка', 'type' => 'message', 'order' => 1),
    array('name' => 'spacing_sync_all', 'label' => 'Применить ко всем блокам', 'type' => 'checkbox', 'order' => 2, 'opt_values' => 'Да=1', 'default' => '1'),
    array('name' => 'spacing_all_desk', 'label' => 'Общий отступ — десктоп (px)', 'type' => 'text', 'order' => 3, 'default' => '20'),
    array('name' => 'spacing_all_mob', 'label' => 'Общий отступ — мобильный (px)', 'type' => 'text', 'order' => 4, 'default' => '14'),
    array('name' => 'spacing_philosophy_desk', 'label' => 'Концепция — десктоп (px)', 'type' => 'text', 'order' => 10, 'default' => '20', 'not_active' => 'spacing_sync_all=1'),
    array('name' => 'spacing_philosophy_mob', 'label' => 'Концепция — мобильный (px)', 'type' => 'text', 'order' => 11, 'default' => '14', 'not_active' => 'spacing_sync_all=1'),
    array('name' => 'spacing_experience_desk', 'label' => 'Experience (Интерьер) — десктоп (px)', 'type' => 'text', 'order' => 12, 'default' => '20', 'not_active' => 'spacing_sync_all=1'),
    array('name' => 'spacing_experience_mob', 'label' => 'Experience (Интерьер) — мобильный (px)', 'type' => 'text', 'order' => 13, 'default' => '14', 'not_active' => 'spacing_sync_all=1'),
    array('name' => 'spacing_menu_desk', 'label' => 'Меню — десктоп (px)', 'type' => 'text', 'order' => 14, 'default' => '20', 'not_active' => 'spacing_sync_all=1'),
    array('name' => 'spacing_menu_mob', 'label' => 'Меню — мобильный (px)', 'type' => 'text', 'order' => 15, 'default' => '14', 'not_active' => 'spacing_sync_all=1'),
    array('name' => 'spacing_akzii_desk', 'label' => 'Акции — десктоп (px)', 'type' => 'text', 'order' => 16, 'default' => '20', 'not_active' => 'spacing_sync_all=1'),
    array('name' => 'spacing_akzii_mob', 'label' => 'Акции — мобильный (px)', 'type' => 'text', 'order' => 17, 'default' => '14', 'not_active' => 'spacing_sync_all=1'),
    array('name' => 'spacing_reservation_desk', 'label' => 'Бронирование — десктоп (px)', 'type' => 'text', 'order' => 18, 'default' => '20', 'not_active' => 'spacing_sync_all=1'),
    array('name' => 'spacing_reservation_mob', 'label' => 'Бронирование — мобильный (px)', 'type' => 'text', 'order' => 19, 'default' => '14', 'not_active' => 'spacing_sync_all=1'),
    array('name' => 'spacing_contacts_desk', 'label' => 'Контакты — десктоп (px)', 'type' => 'text', 'order' => 20, 'default' => '20', 'not_active' => 'spacing_sync_all=1'),
    array('name' => 'spacing_contacts_mob', 'label' => 'Контакты — мобильный (px)', 'type' => 'text', 'order' => 21, 'default' => '14', 'not_active' => 'spacing_sync_all=1'),
    array('name' => 'spacing_filial_desk', 'label' => 'Филиал — десктоп (px)', 'type' => 'text', 'order' => 22, 'default' => '20', 'not_active' => 'spacing_sync_all=1'),
    array('name' => 'spacing_filial_mob', 'label' => 'Филиал — мобильный (px)', 'type' => 'text', 'order' => 23, 'default' => '14', 'not_active' => 'spacing_sync_all=1'),
);

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
        if (!empty($field['opt_values'])) {
            $cols['opt_values'] = $field['opt_values'];
        }
        if (!empty($field['not_active'])) {
            $cols['not_active'] = $field['not_active'];
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

    if ($field['type'] === 'message') {
        continue;
    }

    $hasValue = gl_fetch_one($db, "SELECT id FROM `{$dataText}` WHERE page_id={$pageId} AND field_id={$fieldId} LIMIT 1");
    if ($hasValue) {
        continue;
    }

    $value = !empty($field['default']) ? $field['default'] : '';
    if ($value === '') {
        continue;
    }

    $sql = "INSERT INTO `{$dataText}` (`page_id`,`field_id`,`value`) VALUES ({$pageId},{$fieldId}," . gl_qval($db, $value) . ")";
    if ($db->query($sql)) {
        $migrated++;
        echo "Set value: {$name}\n";
    }
}

$cacheDir = $root . '/couch/cache';
$removed = 0;
if (is_dir($cacheDir)) {
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cacheDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($items as $item) {
        $path = $item->getPathname();
        if ($item->isDir()) {
            if ($path !== $cacheDir && basename($path) !== 'booking-throttle') {
                @rmdir($path);
            }
            continue;
        }
        if (basename($path) === '.htaccess') {
            continue;
        }
        if (@unlink($path)) {
            $removed++;
        }
    }
}

echo "Done. Added {$added} field(s), set {$migrated} value(s), cleared {$removed} cache file(s).\n";
echo "Open admin: Общие -> Отступы между блоками\n";
