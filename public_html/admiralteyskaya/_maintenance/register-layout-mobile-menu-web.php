<?php
/**
 * Register layout-mobile-menu.php template + fields in CouchCMS DB.
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/register-layout-mobile-menu-web.php?key=<md5>
 * key = md5('garden-lounge-register-layout-mobile-menu')
 */
$expectedKey = md5('garden-lounge-register-layout-mobile-menu');
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

$templateName = 'layout-mobile-menu.php';
$title = 'Гамбургер-меню';
$order = 9;
$cloneFrom = 'layout-hero.php';

$row = gl_fetch_one($db, "SELECT id FROM `{$templates}` WHERE name='" . $db->real_escape_string($templateName) . "' LIMIT 1");
if (!$row) {
    $sample = gl_fetch_one($db, "SELECT * FROM `{$templates}` WHERE name='" . $db->real_escape_string($cloneFrom) . "' LIMIT 1");
    if (!$sample) {
        $sample = gl_fetch_one($db, "SELECT * FROM `{$templates}` WHERE name='preloader-settings.php' LIMIT 1");
    }
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

function gl_mm_register_legacy_triplet($legacyPx, $kind)
{
    $L = max(0, (int) $legacyPx);
    if ($L <= 0) {
        return null;
    }
    switch ($kind) {
        case 'shell_top':
        case 'contact_pad':
            return array((string) max(0, $L - 4), (string) round($L / 6.4, 1), (string) ($L + 12));
        case 'logo_gap':
            return array((string) $L, (string) round($L / 8, 1), (string) $L);
        case 'menu_contact':
            return array((string) max(0, $L - 4), (string) round($L / 10, 1), (string) ($L + 4));
        case 'contact_push':
            return array((string) max(0, $L - 4), (string) round($L / 8, 1), (string) ($L + 4));
        case 'social':
            return array((string) max(0, $L - 2), (string) $L, (string) ($L + 2));
        default:
            return null;
    }
}

function gl_mm_register_branch_fields($prefix, $group, $startOrder, $copyPrefix = null)
{
    $copy = function ($suffix) use ($prefix, $copyPrefix) {
        return $copyPrefix ? ($copyPrefix . $suffix) : null;
    };
    $order = (int) $startOrder;
    $fields = array(
        array('name' => $prefix . 'preset', 'label' => 'Пресет отступов', 'type' => 'dropdown', 'group' => $group, 'order' => $order++, 'opt_values' => 'standard=Стандарт|compact=Компакт|spacious=Просторно', 'default' => 'standard', 'copy_from' => $copy('preset')),
        array('name' => $prefix . 'shell_top_min', 'label' => 'Отступ сверху до лого — мин (px)', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => '28'),
        array('name' => $prefix . 'shell_top_vh', 'label' => 'Отступ сверху до лого — vh', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => '5'),
        array('name' => $prefix . 'shell_top_max', 'label' => 'Отступ сверху до лого — макс (px)', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => '44'),
        array('name' => $prefix . 'logo_gap_min', 'label' => 'Лого → пункты меню — мин (px)', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => '32'),
        array('name' => $prefix . 'logo_gap_vh', 'label' => 'Лого → пункты меню — vh', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => '4'),
        array('name' => $prefix . 'logo_gap_max', 'label' => 'Лого → пункты меню — макс (px)', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => '32'),
        array('name' => $prefix . 'menu_contact_min', 'label' => 'Меню → контакты — мин (px)', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => '10'),
        array('name' => $prefix . 'menu_contact_vh', 'label' => 'Меню → контакты — vh', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => '2'),
        array('name' => $prefix . 'menu_contact_max', 'label' => 'Меню → контакты — макс (px)', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => '20'),
        array('name' => $prefix . 'contact_pad_min', 'label' => 'Место перед контактами — мин (px)', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => '28'),
        array('name' => $prefix . 'contact_pad_vh', 'label' => 'Место перед контактами — vh', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => '4.5'),
        array('name' => $prefix . 'contact_pad_max', 'label' => 'Место перед контактами — макс (px)', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => '42'),
        array('name' => $prefix . 'contact_push_min', 'label' => 'Опустить контакты — мин (px)', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => '4'),
        array('name' => $prefix . 'contact_push_vh', 'label' => 'Опустить контакты — vh', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => '1'),
        array('name' => $prefix . 'contact_push_max', 'label' => 'Опустить контакты — макс (px)', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => '12'),
        array('name' => $prefix . 'social_gap_min', 'label' => 'Соцсети — мин (px)', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => '2'),
        array('name' => $prefix . 'social_gap_mid', 'label' => 'Соцсети — значение (px)', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => '4'),
        array('name' => $prefix . 'social_gap_max', 'label' => 'Соцсети — макс (px)', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => '6'),
        array('name' => $prefix . 'branch_label', 'label' => 'Пункт «Второй филиал»', 'type' => 'text', 'group' => $group, 'order' => $order++, 'default' => 'Второй филиал', 'copy_from' => $copy('branch_label')),
    );
    return $fields;
}

$fieldDefs = array(
    array('name' => 'mm_info', 'label' => 'Справка', 'type' => 'message', 'order' => 1),
    array('name' => 'group_mm_adm', 'label' => 'Адмиралтейская', 'type' => 'group', 'order' => 10, 'collapsed' => '1'),
);
$fieldDefs = array_merge($fieldDefs, gl_mm_register_branch_fields('mm_adm_', 'group_mm_adm', 11));
$fieldDefs[] = array('name' => 'group_mm_udel', 'label' => 'Удельная', 'type' => 'group', 'order' => 40, 'collapsed' => '1');
$fieldDefs = array_merge($fieldDefs, gl_mm_register_branch_fields('mm_udel_', 'group_mm_udel', 41, 'mm_adm_'));

$legacyFieldNames = array(
    'mm_adm_shell_pad_top', 'mm_adm_logo_menu_gap', 'mm_adm_menu_contact_gap', 'mm_adm_contact_pad_top', 'mm_adm_contact_push', 'mm_adm_social_gap',
    'mm_udel_shell_pad_top', 'mm_udel_logo_menu_gap', 'mm_udel_menu_contact_gap', 'mm_udel_contact_pad_top', 'mm_udel_contact_push', 'mm_udel_social_gap',
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

foreach ($legacyFieldNames as $legacyName) {
    $legacyFieldId = gl_get_field_id($db, $fields, $templateId, $legacyName);
    if ($legacyFieldId) {
        $db->query("UPDATE `{$fields}` SET not_active='1' WHERE id={$legacyFieldId} LIMIT 1");
        echo "Legacy field hidden: {$legacyName}\n";
    }
}

// Never overwrite existing CMS values on re-register. Legacy migration only with ?migrate_legacy=1
$clampMigration = array();
if ((isset($_GET['migrate_legacy']) ? $_GET['migrate_legacy'] : '') === '1') {
    echo "Legacy migration requested explicitly...\n";
    $clampMigration = array(
        array('legacy' => 'shell_pad_top', 'key' => 'shell_top', 'kind' => 'shell_top'),
        array('legacy' => 'logo_menu_gap', 'key' => 'logo_gap', 'kind' => 'logo_gap'),
        array('legacy' => 'menu_contact_gap', 'key' => 'menu_contact', 'kind' => 'menu_contact'),
        array('legacy' => 'contact_pad_top', 'key' => 'contact_pad', 'kind' => 'contact_pad'),
        array('legacy' => 'contact_push', 'key' => 'contact_push', 'kind' => 'contact_push'),
    );
} else {
    echo "Skipped value migration (existing admin data preserved). Use ?migrate_legacy=1 to migrate once from hidden legacy px fields.\n";
}

function gl_mm_set_field_value($db, $dataText, $pageId, $fieldId, $value)
{
    $hasValue = gl_fetch_one($db, "SELECT id FROM `{$dataText}` WHERE page_id={$pageId} AND field_id={$fieldId} LIMIT 1");
    if ($hasValue) {
        return false;
    }
    $sql = "INSERT INTO `{$dataText}` (`page_id`,`field_id`,`value`) VALUES ({$pageId},{$fieldId}," . gl_qval($db, $value) . ")";
    return (bool) $db->query($sql);
}

function gl_mm_upsert_field_value($db, $dataText, $pageId, $fieldId, $value)
{
    $row = gl_fetch_one($db, "SELECT id FROM `{$dataText}` WHERE page_id={$pageId} AND field_id={$fieldId} LIMIT 1");
    if ($row) {
        $sql = "UPDATE `{$dataText}` SET value=" . gl_qval($db, $value) . " WHERE id=" . (int) $row['id'] . " LIMIT 1";
        return (bool) $db->query($sql);
    }
    $sql = "INSERT INTO `{$dataText}` (`page_id`,`field_id`,`value`) VALUES ({$pageId},{$fieldId}," . gl_qval($db, $value) . ")";
    return (bool) $db->query($sql);
}

if (!empty($clampMigration)) {
    foreach (array('mm_adm_', 'mm_udel_') as $prefix) {
        foreach ($clampMigration as $map) {
            $legacyFieldId = gl_get_field_id($db, $fields, $templateId, $prefix . $map['legacy']);
            if (!$legacyFieldId) {
                continue;
            }
            $legacyValue = gl_get_field_value($db, $dataText, $pageId, $legacyFieldId);
            $triplet = gl_mm_register_legacy_triplet($legacyValue, $map['kind']);
            if (!$triplet) {
                continue;
            }
            $minFieldId = gl_get_field_id($db, $fields, $templateId, $prefix . $map['key'] . '_min');
            $vhFieldId = gl_get_field_id($db, $fields, $templateId, $prefix . $map['key'] . '_vh');
            $maxFieldId = gl_get_field_id($db, $fields, $templateId, $prefix . $map['key'] . '_max');
            if (!$minFieldId || !$vhFieldId || !$maxFieldId) {
                continue;
            }
            if (gl_mm_upsert_field_value($db, $dataText, $pageId, $minFieldId, $triplet[0]) &&
                gl_mm_upsert_field_value($db, $dataText, $pageId, $vhFieldId, $triplet[1]) &&
                gl_mm_upsert_field_value($db, $dataText, $pageId, $maxFieldId, $triplet[2])) {
                $migrated += 3;
                echo "Migrated {$prefix}{$map['key']} from legacy px ({$legacyValue})\n";
            }
        }

        $legacySocialId = gl_get_field_id($db, $fields, $templateId, $prefix . 'social_gap');
        if ($legacySocialId) {
            $legacySocial = gl_get_field_value($db, $dataText, $pageId, $legacySocialId);
            $socialTriplet = gl_mm_register_legacy_triplet($legacySocial, 'social');
            if ($socialTriplet) {
                $socialMinId = gl_get_field_id($db, $fields, $templateId, $prefix . 'social_gap_min');
                $socialMidId = gl_get_field_id($db, $fields, $templateId, $prefix . 'social_gap_mid');
                $socialMaxId = gl_get_field_id($db, $fields, $templateId, $prefix . 'social_gap_max');
                if ($socialMinId && $socialMidId && $socialMaxId &&
                    gl_mm_upsert_field_value($db, $dataText, $pageId, $socialMinId, $socialTriplet[0]) &&
                    gl_mm_upsert_field_value($db, $dataText, $pageId, $socialMidId, $socialTriplet[1]) &&
                    gl_mm_upsert_field_value($db, $dataText, $pageId, $socialMaxId, $socialTriplet[2])) {
                    $migrated += 3;
                    echo "Migrated {$prefix}social_gap from legacy px ({$legacySocial})\n";
                }
            }
        }

        $presetFieldId = gl_get_field_id($db, $fields, $templateId, $prefix . 'preset');
        if ($presetFieldId && gl_get_field_value($db, $dataText, $pageId, $presetFieldId) === '') {
            if (gl_mm_set_field_value($db, $dataText, $pageId, $presetFieldId, 'standard')) {
                $migrated++;
                echo "Set {$prefix}preset=standard\n";
            }
        }
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
echo "Open admin: Общие -> Гамбургер-меню\n";
