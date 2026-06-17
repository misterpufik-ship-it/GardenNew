<?php
/**
 * Register split gallery repeatables in CouchCMS DB.
 * Run on BeGet:
 *   php _maintenance/register-gallery-fields-sql.php
 */
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

function q($db, $value)
{
    if ($value === null) {
        return 'NULL';
    }
    return "'" . $db->real_escape_string((string) $value) . "'";
}

function one($db, $sql)
{
    $res = $db->query($sql);
    if (!$res) {
        throw new RuntimeException($db->error . "\n" . $sql);
    }
    $row = $res->fetch_assoc();
    return $row ?: null;
}

function all($db, $sql)
{
    $res = $db->query($sql);
    if (!$res) {
        throw new RuntimeException($db->error . "\n" . $sql);
    }
    $rows = array();
    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

function insert_field($db, $table, $row)
{
    unset($row['id']);
    $cols = array_keys($row);
    $vals = array();
    foreach ($row as $value) {
        $vals[] = q($db, $value);
    }
    $sql = "INSERT INTO `{$table}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $vals) . ")";
    if (!$db->query($sql)) {
        throw new RuntimeException($db->error . "\n" . $sql);
    }
    return (int) $db->insert_id;
}

function field_exists($db, $fields, $templateId, $name)
{
    $row = one($db, "SELECT id FROM `{$fields}` WHERE template_id=" . (int) $templateId . " AND name=" . q($db, $name) . " LIMIT 1");
    return $row ? (int) $row['id'] : 0;
}

function next_order($db, $fields, $templateId)
{
    $row = one($db, "SELECT MAX(CAST(k_order AS UNSIGNED)) AS max_order FROM `{$fields}` WHERE template_id=" . (int) $templateId);
    $maxOrder = ($row && isset($row['max_order'])) ? $row['max_order'] : 0;
    return (int) $maxOrder + 10;
}

function ensure_group_field($db, $fields, $templateId, $name, $label, $order)
{
    $existing = field_exists($db, $fields, $templateId, $name);
    if ($existing) {
        $db->query(
            "UPDATE `{$fields}` SET label=" . q($db, $label) . ", k_order=" . q($db, (string) $order) .
            ", k_type='group' WHERE id={$existing} LIMIT 1"
        );
        echo "Updated group {$name} (#{$existing})\n";
        return $existing;
    }

    $sample = one($db, "SELECT * FROM `{$fields}` WHERE template_id=" . (int) $templateId . " AND k_type='group' LIMIT 1");
    if (!$sample) {
        $sample = one($db, "SELECT * FROM `{$fields}` WHERE k_type='group' LIMIT 1");
    }
    if (!$sample) {
        throw new RuntimeException("No sample group field for template {$templateId}");
    }

    $row = $sample;
    $row['template_id'] = (string) $templateId;
    $row['name'] = $name;
    $row['label'] = $label;
    $row['k_group'] = '';
    $row['k_order'] = (string) $order;
    $row['k_type'] = 'group';
    $row['_html'] = '';
    $fieldId = insert_field($db, $fields, $row);
    echo "Created group {$name} (#{$fieldId})\n";
    return $fieldId;
}

function sync_repeatable_html($db, $fields, $repeatableId)
{
    $html = "<cms:editable name='gallery_img' label='Фото' type='image' />\r\n" .
            "<cms:editable name='gallery_img_title' label='Подпись к фото' type='text' />\r\n" .
            "<cms:editable name='gallery_img_alt' label='ALT для SEO' type='text' />";
    $db->query("UPDATE `{$fields}` SET _html=" . q($db, $html) . " WHERE id=" . (int) $repeatableId . " LIMIT 1");
}

function ensure_gallery_repeatable($db, $fields, $templateId, $sourceRepeatableName, $repeatableName, $repeatableLabel, $groupName)
{
    $existing = field_exists($db, $fields, $templateId, $repeatableName);
    if ($existing) {
        sync_repeatable_html($db, $fields, $existing);
        echo "Repeatable exists: {$repeatableName} (#{$existing})\n";
        $repeatableId = $existing;
    } else {
        $sourceRepeatable = one(
            $db,
            "SELECT * FROM `{$fields}` WHERE template_id=" . (int) $templateId .
            " AND name=" . q($db, $sourceRepeatableName) . " LIMIT 1"
        );
        if (!$sourceRepeatable) {
            throw new RuntimeException("Source repeatable {$sourceRepeatableName} missing for template {$templateId}");
        }

        $row = $sourceRepeatable;
        $row['name'] = $repeatableName;
        $row['label'] = $repeatableLabel;
        $row['k_group'] = $groupName;
        $row['k_order'] = (string) next_order($db, $fields, $templateId);
        $repeatableId = insert_field($db, $fields, $row);
        sync_repeatable_html($db, $fields, $repeatableId);
        echo "Created repeatable {$repeatableName} (#{$repeatableId})\n";
    }

    $childNames = array(
        'gallery_img' => 'Фото',
        'gallery_img_title' => 'Подпись к фото',
        'gallery_img_alt' => 'ALT для SEO',
    );

    foreach ($childNames as $childName => $childLabel) {
        $existingChild = one(
            $db,
            "SELECT id FROM `{$fields}` WHERE template_id=" . (int) $templateId .
            " AND k_group=" . q($db, $repeatableName) . " AND name=" . q($db, $childName) . " LIMIT 1"
        );
        if ($existingChild) {
            echo "  = child {$childName} already exists\n";
            continue;
        }

        $sourceChild = one(
            $db,
            "SELECT * FROM `{$fields}` WHERE template_id=" . (int) $templateId .
            " AND k_group=" . q($db, $sourceRepeatableName) . " AND name=" . q($db, $childName) . " LIMIT 1"
        );
        if (!$sourceChild) {
            $sourceChild = one(
                $db,
                "SELECT * FROM `{$fields}` WHERE name=" . q($db, $childName) .
                " AND k_type IN ('text','textarea') LIMIT 1"
            );
        }
        if (!$sourceChild && $childName === 'gallery_img') {
            $sourceChild = one($db, "SELECT * FROM `{$fields}` WHERE name='gallery_img' LIMIT 1");
        }
        if (!$sourceChild) {
            throw new RuntimeException("Cannot find sample child field {$childName}");
        }

        $childRow = $sourceChild;
        $childRow['template_id'] = (string) $templateId;
        $childRow['name'] = $childName;
        $childRow['label'] = $childLabel;
        $childRow['k_group'] = $repeatableName;
        $childRow['k_order'] = (string) next_order($db, $fields, $templateId);
        if ($childName === 'gallery_img_alt') {
            $childRow['k_type'] = 'text';
        }
        $childId = insert_field($db, $fields, $childRow);
        echo "  + child {$childName} (#{$childId})\n";
    }

    return $repeatableId;
}

$targets = array('gallery.php', 'udelnaya/gallery.php');

try {
    foreach ($targets as $templateName) {
        echo "\n=== {$templateName} ===\n";
        $template = one($db, "SELECT id FROM `{$templates}` WHERE name=" . q($db, $templateName) . " LIMIT 1");
        if (!$template) {
            echo "Template missing\n";
            continue;
        }

        $templateId = (int) $template['id'];
        ensure_group_field($db, $fields, $templateId, 'gallery_grp_interior', 'Interior — интерьер', 10);
        ensure_group_field($db, $fields, $templateId, 'gallery_grp_menu', 'Menu — меню', 20);
        ensure_group_field($db, $fields, $templateId, 'gallery_grp_vibe', 'Vibe — атмосфера', 30);

        ensure_gallery_repeatable($db, $fields, $templateId, 'gallery_items', 'gallery_interior_items', 'Фото интерьера', 'gallery_grp_interior');
        ensure_gallery_repeatable($db, $fields, $templateId, 'gallery_items', 'gallery_menu_items', 'Фото меню', 'gallery_grp_menu');
        ensure_gallery_repeatable($db, $fields, $templateId, 'gallery_items', 'gallery_vibe_items', 'Фото атмосферы', 'gallery_grp_vibe');
    }

    require __DIR__ . '/clear-couch-cache-cli.php';
    echo "Gallery field registration complete.\n";
} catch (Exception $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
