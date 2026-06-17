<?php
/**
 * Remove legacy gallery_items admin table and category dropdowns from split repeatables.
 * Run on BeGet:
 *   php _maintenance/cleanup-gallery-admin-sql.php
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

$config = __DIR__ . '/../couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ?: 3306;
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
}

$db = new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
if ($db->connect_errno) {
    fwrite(STDERR, $db->connect_error . "\n");
    exit(1);
}
$db->set_charset('utf8');

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$text = K_DB_TABLES_PREFIX . 'couch_data_text';

function q($db, $value)
{
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

function clean_repeatable_html($repeatableName, $repeatableLabel)
{
    return "<cms:editable name='gallery_img' label='Фото' type='image' />\r\n" .
           "<cms:editable name='gallery_img_title' label='Подпись к фото' type='text' />\r\n" .
           "<cms:editable name='gallery_img_alt' label='ALT для SEO' type='text' />";
}

function delete_field_tree($db, $fields, $text, $fieldId)
{
    $children = all($db, "SELECT id FROM `{$fields}` WHERE k_group=(SELECT name FROM `{$fields}` WHERE id=" . (int) $fieldId . " LIMIT 1) AND template_id=(SELECT template_id FROM `{$fields}` WHERE id=" . (int) $fieldId . " LIMIT 1)");
    foreach ($children as $child) {
        $db->query("DELETE FROM `{$text}` WHERE field_id=" . (int) $child['id']);
        $db->query("DELETE FROM `{$fields}` WHERE id=" . (int) $child['id']);
        echo "  deleted child field #{$child['id']}\n";
    }

    $db->query("DELETE FROM `{$text}` WHERE field_id=" . (int) $fieldId);
    $db->query("DELETE FROM `{$fields}` WHERE id=" . (int) $fieldId);
}

$templateNames = array('gallery.php', 'udelnaya/gallery.php');
$repeatables = array(
    'gallery_interior_items' => 'Фото интерьера',
    'gallery_menu_items' => 'Фото меню',
    'gallery_vibe_items' => 'Фото атмосферы',
);

try {
    foreach ($templateNames as $templateName) {
        echo "\n=== {$templateName} ===\n";
        $template = one($db, "SELECT id FROM `{$templates}` WHERE name=" . q($db, $templateName) . " LIMIT 1");
        if (!$template) {
            echo "template missing\n";
            continue;
        }
        $templateId = (int) $template['id'];

        $legacy = one($db, "SELECT id FROM `{$fields}` WHERE template_id={$templateId} AND name='gallery_items' LIMIT 1");
        if ($legacy) {
            echo "Removing legacy gallery_items (#{$legacy['id']})\n";
            delete_field_tree($db, $fields, $text, (int) $legacy['id']);
        } else {
            echo "gallery_items already removed\n";
        }

        foreach ($repeatables as $repeatableName => $repeatableLabel) {
            $field = one($db, "SELECT id FROM `{$fields}` WHERE template_id={$templateId} AND name=" . q($db, $repeatableName) . " LIMIT 1");
            if (!$field) {
                echo "{$repeatableName}: missing\n";
                continue;
            }

            $categoryChildren = all(
                $db,
                "SELECT id, name FROM `{$fields}` WHERE template_id={$templateId} AND k_group=" . q($db, $repeatableName) .
                " AND name='gallery_category'"
            );
            foreach ($categoryChildren as $child) {
                $db->query("DELETE FROM `{$text}` WHERE field_id=" . (int) $child['id']);
                $db->query("DELETE FROM `{$fields}` WHERE id=" . (int) $child['id']);
                echo "  removed {$repeatableName}::gallery_category (#{$child['id']})\n";
            }

            $html = clean_repeatable_html($repeatableName, $repeatableLabel);
            $db->query(
                "UPDATE `{$fields}` SET _html=" . q($db, $html) . ", label=" . q($db, $repeatableLabel) .
                " WHERE id=" . (int) $field['id'] . " LIMIT 1"
            );
            echo "  cleaned {$repeatableName} (#{$field['id']})\n";
        }
    }

    require __DIR__ . '/clear-couch-cache-cli.php';
    echo "Gallery admin cleanup complete.\n";
} catch (Exception $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
