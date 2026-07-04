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

function gl_get_field_value($db, $dataText, $pageId, $fieldId)
{
    $row = gl_fetch_one(
        $db,
        "SELECT value FROM `{$dataText}` WHERE page_id={$pageId} AND field_id={$fieldId} LIMIT 1"
    );
    return $row ? (string)$row['value'] : '';
}

function gl_get_field_id($db, $fields, $templateId, $name)
{
    $row = gl_fetch_one(
        $db,
        "SELECT id FROM `{$fields}` WHERE template_id={$templateId} AND name='" . $db->real_escape_string($name) . "' LIMIT 1"
    );
    return $row ? (int)$row['id'] : 0;
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

$sectionLabels = array(
    'philosophy' => 'Концепция',
    'experience' => 'Experience (Интерьер)',
    'menu' => 'Меню',
    'akzii' => 'Акции',
    'reservation' => 'Бронирование',
    'contacts' => 'Контакты',
    'filial' => 'Филиал',
);

$fieldDefs = array(
    array('name' => 'spacing_info', 'label' => 'Справка', 'type' => 'message', 'order' => 1),
    array('name' => 'group_spacing_adm', 'label' => 'Адмиралтейская', 'type' => 'group', 'order' => 10, 'collapsed' => '1'),
    array('name' => 'spacing_adm_sync_all', 'label' => 'Применить ко всем блокам', 'type' => 'checkbox', 'group' => 'group_spacing_adm', 'order' => 11, 'opt_values' => 'Да=1', 'default' => '1', 'not_active' => '', 'legacy' => 'spacing_sync_all'),
    array('name' => 'spacing_adm_all_desk', 'label' => 'Общий отступ — десктоп (px)', 'type' => 'text', 'group' => 'group_spacing_adm', 'order' => 12, 'default' => '20', 'legacy' => 'spacing_all_desk'),
    array('name' => 'spacing_adm_all_mob', 'label' => 'Общий отступ — мобильный (px)', 'type' => 'text', 'group' => 'group_spacing_adm', 'order' => 13, 'default' => '14', 'legacy' => 'spacing_all_mob'),
    array('name' => 'group_spacing_udel', 'label' => 'Удельная', 'type' => 'group', 'order' => 40, 'collapsed' => '1'),
    array('name' => 'spacing_udel_sync_all', 'label' => 'Применить ко всем блокам', 'type' => 'checkbox', 'group' => 'group_spacing_udel', 'order' => 41, 'opt_values' => 'Да=1', 'default' => '1', 'copy_from' => 'spacing_adm_sync_all', 'legacy' => 'spacing_sync_all'),
    array('name' => 'spacing_udel_all_desk', 'label' => 'Общий отступ — десктоп (px)', 'type' => 'text', 'group' => 'group_spacing_udel', 'order' => 42, 'default' => '20', 'copy_from' => 'spacing_adm_all_desk', 'legacy' => 'spacing_all_desk'),
    array('name' => 'spacing_udel_all_mob', 'label' => 'Общий отступ — мобильный (px)', 'type' => 'text', 'group' => 'group_spacing_udel', 'order' => 43, 'default' => '14', 'copy_from' => 'spacing_adm_all_mob', 'legacy' => 'spacing_all_mob'),
);

$orderAdm = 20;
$orderUdel = 50;
foreach ($sectionLabels as $section => $label) {
    $fieldDefs[] = array(
        'name' => 'spacing_adm_' . $section . '_desk',
        'label' => $label . ' — десктоп (px)',
        'type' => 'text',
        'group' => 'group_spacing_adm',
        'order' => $orderAdm++,
        'default' => '20',
        'not_active' => 'spacing_adm_sync_all=1',
        'legacy' => 'spacing_' . $section . '_desk',
    );
    $fieldDefs[] = array(
        'name' => 'spacing_adm_' . $section . '_mob',
        'label' => $label . ' — мобильный (px)',
        'type' => 'text',
        'group' => 'group_spacing_adm',
        'order' => $orderAdm++,
        'default' => '14',
        'not_active' => 'spacing_adm_sync_all=1',
        'legacy' => 'spacing_' . $section . '_mob',
    );
    $fieldDefs[] = array(
        'name' => 'spacing_udel_' . $section . '_desk',
        'label' => $label . ' — десктоп (px)',
        'type' => 'text',
        'group' => 'group_spacing_udel',
        'order' => $orderUdel++,
        'default' => '20',
        'not_active' => 'spacing_udel_sync_all=1',
        'copy_from' => 'spacing_adm_' . $section . '_desk',
        'legacy' => 'spacing_' . $section . '_desk',
    );
    $fieldDefs[] = array(
        'name' => 'spacing_udel_' . $section . '_mob',
        'label' => $label . ' — мобильный (px)',
        'type' => 'text',
        'group' => 'group_spacing_udel',
        'order' => $orderUdel++,
        'default' => '14',
        'not_active' => 'spacing_udel_sync_all=1',
        'copy_from' => 'spacing_adm_' . $section . '_mob',
        'legacy' => 'spacing_' . $section . '_mob',
    );
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
        $updates = array();
        if (!empty($field['label'])) {
            $updates[] = "label='" . $db->real_escape_string($field['label']) . "'";
        }
        if (!empty($field['group'])) {
            $updates[] = "k_group='" . $db->real_escape_string($field['group']) . "'";
        }
        if (isset($field['order'])) {
            $updates[] = "k_order='" . (int)$field['order'] . "'";
        }
        if (!empty($field['not_active'])) {
            $updates[] = "not_active='" . $db->real_escape_string($field['not_active']) . "'";
        } else {
            $updates[] = "not_active=''";
        }
        if (!empty($updates)) {
            $db->query("UPDATE `{$fields}` SET " . implode(', ', $updates) . " WHERE id={$fieldId} LIMIT 1");
        }
        echo "Field exists: {$name}\n";
    } else {
        if ($field['type'] === 'group') {
            $sample = gl_fetch_one($db, "SELECT * FROM `{$fields}` WHERE template_id={$templateId} AND k_type='group' LIMIT 1");
            if (!$sample) {
                $sample = gl_fetch_one($db, "SELECT * FROM `{$fields}` WHERE k_type='group' LIMIT 1");
            }
            if ($sample) {
                unset($sample['id']);
                $sample['template_id'] = (string)$templateId;
                $sample['name'] = $name;
                $sample['label'] = $field['label'];
                $sample['k_group'] = '';
                $sample['k_order'] = (string)(int)$field['order'];
                $sample['k_type'] = 'group';
                $sample['_html'] = '';
                $colNames = array_keys($sample);
                $vals = array();
                foreach (array_values($sample) as $v) {
                    $vals[] = gl_qval($db, $v);
                }
                $sql = "INSERT INTO `{$fields}` (`" . implode('`,`', $colNames) . "`) VALUES (" . implode(',', $vals) . ")";
                if (!$db->query($sql)) {
                    echo "Failed to insert group {$name}: {$db->error}\n";
                    continue;
                }
                $fieldId = (int)$db->insert_id;
                $added++;
                echo "Added group: {$name} (#{$fieldId})\n";
            } else {
                echo "Failed to insert group {$name}: no sample group field\n";
                continue;
            }
        } else {
            $sample = gl_fetch_one(
                $db,
                "SELECT * FROM `{$fields}` WHERE template_id={$templateId} AND k_type='" .
                $db->real_escape_string($field['type']) . "' LIMIT 1"
            );
            if (!$sample) {
                $sample = gl_fetch_one(
                    $db,
                    "SELECT * FROM `{$fields}` WHERE template_id={$templateId} AND k_type='text' LIMIT 1"
                );
            }
            if (!$sample) {
                $sample = gl_fetch_one($db, "SELECT * FROM `{$fields}` WHERE template_id={$templateId} LIMIT 1");
            }
            if (!$sample) {
                echo "Failed to insert field {$name}: no sample field\n";
                continue;
            }
            unset($sample['id']);
            $sample['template_id'] = (string)$templateId;
            $sample['name'] = $name;
            $sample['label'] = $field['label'];
            $sample['k_type'] = $field['type'];
            $sample['k_group'] = !empty($field['group']) ? $field['group'] : '';
            $sample['k_order'] = (string)(int)$field['order'];
            $sample['_html'] = '';
            if (!empty($field['opt_values'])) {
                $sample['opt_values'] = $field['opt_values'];
            }
            if (!empty($field['not_active'])) {
                $sample['not_active'] = $field['not_active'];
            } else {
                $sample['not_active'] = '';
            }
            if (!empty($field['default'])) {
                $sample['default_data'] = $field['default'];
            }
            $colNames = array_keys($sample);
            $vals = array();
            foreach (array_values($sample) as $v) {
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
    }

    if ($field['type'] === 'group' && !empty($field['collapsed'])) {
        $html = "<cms:editable name='" . $name . "' label='" . $db->real_escape_string($field['label']) .
            "' type='group' collapsed='1' order='" . (int)$field['order'] . "' />";
        $db->query("UPDATE `{$fields}` SET _html='" . $db->real_escape_string($html) . "' WHERE id={$fieldId} LIMIT 1");
    }

    if ($field['type'] === 'message' || $field['type'] === 'group') {
        continue;
    }

    $hasValue = gl_fetch_one($db, "SELECT id FROM `{$dataText}` WHERE page_id={$pageId} AND field_id={$fieldId} LIMIT 1");
    if ($hasValue) {
        continue;
    }

    $value = '';
    if (!empty($field['copy_from'])) {
        $copyFieldId = gl_get_field_id($db, $fields, $templateId, $field['copy_from']);
        if ($copyFieldId) {
            $value = gl_get_field_value($db, $dataText, $pageId, $copyFieldId);
        }
    }
    if ($value === '' && !empty($field['legacy'])) {
        $legacyFieldId = gl_get_field_id($db, $fields, $templateId, $field['legacy']);
        if ($legacyFieldId) {
            $value = gl_get_field_value($db, $dataText, $pageId, $legacyFieldId);
        }
    }
    if ($value === '' && !empty($field['default'])) {
        $value = $field['default'];
    }
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
