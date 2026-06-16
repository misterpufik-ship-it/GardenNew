<?php
if (PHP_SAPI !== 'cli') {
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

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ?: 3306;
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
}

mysqli_report(MYSQLI_REPORT_OFF);
$db = @new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
if ($db->connect_errno) {
    fwrite(STDERR, "DB connection failed: {$db->connect_error}\n");
    exit(1);
}
$db->set_charset('utf8');

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$text = K_DB_TABLES_PREFIX . 'couch_data_text';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';

function q($db, $value) {
    if ($value === null) {
        return 'NULL';
    }
    return "'" . $db->real_escape_string((string)$value) . "'";
}

function one($db, $sql) {
    $res = $db->query($sql);
    if (!$res) {
        throw new RuntimeException($db->error . "\n" . $sql);
    }
    $row = $res->fetch_assoc();
    return $row ?: null;
}

function all($db, $sql) {
    $res = $db->query($sql);
    if (!$res) {
        throw new RuntimeException($db->error . "\n" . $sql);
    }
    $rows = [];
    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

function insert_field($db, $table, $row) {
    unset($row['id']);
    $cols = array_keys($row);
    $vals = [];
    foreach ($row as $value) {
        $vals[] = q($db, $value);
    }
    $sql = "INSERT INTO `{$table}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $vals) . ")";
    if (!$db->query($sql)) {
        throw new RuntimeException($db->error . "\n" . $sql);
    }
    return (int)$db->insert_id;
}

function field_exists($db, $fields, $templateId, $name) {
    $row = one($db, "SELECT id FROM `{$fields}` WHERE template_id=" . (int)$templateId . " AND name=" . q($db, $name) . " LIMIT 1");
    return $row ? (int)$row['id'] : 0;
}

function next_order($db, $fields, $templateId) {
    $row = one($db, "SELECT MAX(CAST(k_order AS UNSIGNED)) AS max_order FROM `{$fields}` WHERE template_id=" . (int)$templateId);
    $maxOrder = ($row && isset($row['max_order'])) ? $row['max_order'] : 0;
    return (int)$maxOrder + 10;
}

function find_repeatable_source($db, $fields, $templates, $repeatableName) {
    $rows = all(
        $db,
        "SELECT t.name AS template_name, f.id
         FROM `{$fields}` f
         JOIN `{$templates}` t ON t.id = f.template_id
         WHERE f.name=" . q($db, $repeatableName) . "
         LIMIT 1"
    );
    if (!$rows) {
        return null;
    }
    return $rows[0];
}

function clone_repeatable($db, $fields, $templates, $sourceTemplateName, $sourceRepeatableName, $targetTemplateId, $targetRepeatableName, $targetRepeatableLabel, $groupName) {
    $sourceTemplate = one($db, "SELECT id FROM `{$templates}` WHERE name=" . q($db, $sourceTemplateName) . " LIMIT 1");
    if (!$sourceTemplate) {
        throw new RuntimeException("Source template not found: {$sourceTemplateName}");
    }

    $sourceRepeatable = one(
        $db,
        "SELECT * FROM `{$fields}` WHERE template_id=" . (int)$sourceTemplate['id'] .
        " AND name=" . q($db, $sourceRepeatableName) . " LIMIT 1"
    );
    if (!$sourceRepeatable) {
        throw new RuntimeException("Source repeatable not found: {$sourceRepeatableName}");
    }

    $existing = field_exists($db, $fields, $targetTemplateId, $targetRepeatableName);
    if ($existing) {
        echo "Repeatable already exists: {$targetRepeatableName} (field #{$existing})\n";
        $repeatableId = $existing;
    } else {
        $order = next_order($db, $fields, $targetTemplateId);
        $repeatable = $sourceRepeatable;
        $repeatable['template_id'] = (string)$targetTemplateId;
        $repeatable['name'] = $targetRepeatableName;
        $repeatable['label'] = $targetRepeatableLabel;
        $repeatable['k_group'] = $groupName;
        $repeatable['k_order'] = (string)$order;
        $repeatableId = insert_field($db, $fields, $repeatable);
        echo "Created repeatable {$targetRepeatableName} (#{$repeatableId})\n";
    }

    $children = all(
        $db,
        "SELECT * FROM `{$fields}` WHERE template_id=" . (int)$sourceTemplate['id'] .
        " AND k_group=" . q($db, $sourceRepeatableName) . " ORDER BY id"
    );

    if (!$children) {
        $children = all(
            $db,
            "SELECT * FROM `{$fields}` WHERE template_id=" . (int)$sourceTemplate['id'] .
            " AND name IN ('gallery_img','gallery_img_alt','gallery_img_title','gallery_category') ORDER BY id"
        );
    }

    foreach ($children as $child) {
        $childRow = $child;
        $childRow['template_id'] = (string)$targetTemplateId;
        $childRow['k_group'] = $targetRepeatableName;
        $childRow['k_order'] = (string)next_order($db, $fields, $targetTemplateId);

        if ($childRow['name'] === 'gallery_img') {
            $childRow['name'] = 'final_gallery_img';
            $childRow['label'] = 'Фото';
        } elseif ($childRow['name'] === 'gallery_img_alt') {
            $childRow['name'] = 'final_gallery_alt';
            $childRow['label'] = 'Alt / подпись';
        } elseif ($childRow['name'] === 'gallery_img_title') {
            continue;
        } elseif ($childRow['name'] === 'gallery_category') {
            continue;
        } else {
            continue;
        }

        if (field_exists($db, $fields, $targetTemplateId, $childRow['name'])) {
            echo "  = child {$childRow['name']} already exists\n";
            continue;
        }

        $childId = insert_field($db, $fields, $childRow);
        echo "  + child {$childRow['name']} (#{$childId})\n";
    }

    sync_repeatable_html($db, $fields, $repeatableId);

    return $repeatableId;
}

function sync_repeatable_html($db, $fields, $repeatableId) {
    $html = "<cms:editable name='final_gallery_img' label='Фото' type='image' />\r\n" .
            "<cms:editable name='final_gallery_alt' label='Alt / подпись' type='text' />";
    $db->query(
        "UPDATE `{$fields}` SET _html=" . q($db, $html) . " WHERE id=" . (int)$repeatableId . " LIMIT 1"
    );
    echo "  synced repeatable _html for field #{$repeatableId}\n";
}

function ensure_text_field($db, $fields, $templateId, $name, $label, $groupName, $default = '') {
    $existing = field_exists($db, $fields, $templateId, $name);
    if ($existing) {
        $db->query(
            "UPDATE `{$fields}` SET label=" . q($db, $label) . ", k_group=" . q($db, $groupName) .
            " WHERE id={$existing} LIMIT 1"
        );
        echo "Updated field {$name} (#{$existing})\n";
        return $existing;
    }

    $sample = one($db, "SELECT * FROM `{$fields}` WHERE template_id=" . (int)$templateId . " AND name='final_subtitle' LIMIT 1");
    if (!$sample) {
        $sample = one($db, "SELECT * FROM `{$fields}` WHERE template_id=" . (int)$templateId . " AND k_type='text' LIMIT 1");
    }
    if (!$sample) {
        throw new RuntimeException("No sample text field for template {$templateId}");
    }

    $row = $sample;
    $row['name'] = $name;
    $row['label'] = $label;
    $row['k_group'] = $groupName;
    $row['k_order'] = (string)next_order($db, $fields, $templateId);
    $row['default_data'] = $default;
    $row['_html'] = '';
    $fieldId = insert_field($db, $fields, $row);
    echo "Created field {$name} (#{$fieldId})\n";
    return $fieldId;
}

function restore_empty_image($db, $fields, $text, $pages, $templateId, $defaultImage) {
    $page = one($db, "SELECT id FROM `{$pages}` WHERE template_id=" . (int)$templateId . " LIMIT 1");
    if (!$page) {
        return;
    }

    $field = one($db, "SELECT id FROM `{$fields}` WHERE template_id=" . (int)$templateId . " AND name='final_img' LIMIT 1");
    if (!$field) {
        return;
    }

    $value = one(
        $db,
        "SELECT value FROM `{$text}` WHERE page_id=" . (int)$page['id'] . " AND field_id=" . (int)$field['id'] . " LIMIT 1"
    );

    if ($value && trim($value['value']) !== '') {
        echo "final_img already set for template {$templateId}\n";
        return;
    }

    $db->query(
        "INSERT INTO `{$text}` (page_id, field_id, value) VALUES (" .
        (int)$page['id'] . "," . (int)$field['id'] . "," . q($db, $defaultImage) .
        ") ON DUPLICATE KEY UPDATE value=VALUES(value)"
    );
    echo "Restored empty final_img for template {$templateId}\n";
}

$targets = array(
    array(
        'template_name' => 'filial.php',
        'default_image' => 'https://garden-lounge.pro/img/akkuratova.webp',
    ),
    array(
        'template_name' => 'udelnaya/filial.php',
        'default_image' => ':garden-main-1.webp',
    ),
);

try {
    foreach ($targets as $target) {
        echo "\n=== {$target['template_name']} ===\n";
        $template = one($db, "SELECT id FROM `{$templates}` WHERE name=" . q($db, $target['template_name']) . " LIMIT 1");
        if (!$template) {
            echo "Template missing\n";
            continue;
        }

        $templateId = (int)$template['id'];
        $db->query(
            "UPDATE `{$fields}` SET label='Изображение филиала', k_group='final_group_info' " .
            "WHERE template_id={$templateId} AND name='final_img' LIMIT 1"
        );

        ensure_text_field($db, $fields, $templateId, 'final_img_alt', 'Alt основного фото', 'final_group_info');
        $source = find_repeatable_source($db, $fields, $templates, 'gallery_items');
        if (!$source) {
            throw new RuntimeException('gallery_items repeatable not found in any template');
        }
        clone_repeatable(
            $db,
            $fields,
            $templates,
            $source['template_name'],
            'gallery_items',
            $templateId,
            'final_gallery',
            'Дополнительные фото галереи',
            'final_group_info'
        );
        $galleryFieldId = field_exists($db, $fields, $templateId, 'final_gallery');
        if ($galleryFieldId) {
            sync_repeatable_html($db, $fields, $galleryFieldId);
        }
        restore_empty_image($db, $fields, $text, $pages, $templateId, $target['default_image']);
    }

    require __DIR__ . '/clear-couch-cache-cli.php';
} catch (Exception $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
