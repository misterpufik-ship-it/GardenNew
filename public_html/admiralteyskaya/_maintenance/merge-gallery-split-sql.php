<?php
/**
 * Merge legacy gallery_items into split repeatables, clear captions/alts, remove legacy field.
 *   php _maintenance/merge-gallery-split-sql.php
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

$db = new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int) $port);
if ($db->connect_errno) {
    fwrite(STDERR, $db->connect_error . "\n");
    exit(1);
}
$db->set_charset('utf8');

$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$text = K_DB_TABLES_PREFIX . 'couch_data_text';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';
$templates = K_DB_TABLES_PREFIX . 'couch_templates';

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

function decode_repeatable_value($raw)
{
    $raw = trim((string) $raw);
    if ($raw === '') {
        return array();
    }
    $data = @unserialize($raw);
    if (is_array($data)) {
        return $data;
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : array();
}

function img_key($img)
{
    $img = trim((string) $img);
    if ($img === '') {
        return '';
    }
    $path = parse_url($img, PHP_URL_PATH);
    if (is_string($path) && $path !== '') {
        $img = $path;
    }
    $img = str_replace('\\', '/', $img);
    return strtolower(basename($img));
}

function extract_row($row)
{
    if (!is_array($row)) {
        return null;
    }

    $img = '';
    $title = '';
    $alt = '';
    $category = '';

    foreach ($row as $key => $value) {
        if (!is_string($key)) {
            continue;
        }
        if ($key === 'gallery_img' || substr($key, -10) === 'gallery_img') {
            $img = trim((string) $value);
        } elseif ($key === 'gallery_img_title') {
            $title = trim((string) $value);
        } elseif ($key === 'gallery_img_alt') {
            $alt = trim((string) $value);
        } elseif ($key === 'gallery_category') {
            $category = trim((string) $value);
        }
    }

    if ($img === '') {
        return null;
    }

    return array(
        'gallery_img' => $img,
        'gallery_img_title' => $title,
        'gallery_img_alt' => $alt,
        'gallery_category' => $category,
    );
}

function clean_row($row)
{
    $img = isset($row['gallery_img']) ? trim((string) $row['gallery_img']) : '';
    if ($img === '') {
        return null;
    }
    return array(
        'gallery_img' => $img,
        'gallery_img_title' => '',
        'gallery_img_alt' => '',
    );
}

function read_repeatable_rows($db, $text, $pageId, $fieldId)
{
    if (!$fieldId) {
        return array();
    }
    $value = one($db, "SELECT value FROM `{$text}` WHERE page_id=" . (int) $pageId . " AND field_id=" . (int) $fieldId . " LIMIT 1");
    if (!$value) {
        return array();
    }
    $rows = decode_repeatable_value($value['value']);
    $out = array();
    foreach ($rows as $row) {
        $parsed = extract_row($row);
        if (!$parsed) {
            continue;
        }
        $clean = clean_row($parsed);
        if ($clean) {
            $out[] = $clean;
        }
    }
    return $out;
}

function merge_rows($existing, $incoming)
{
    $merged = array();
    $seen = array();

    foreach (array_merge($existing, $incoming) as $row) {
        $clean = clean_row($row);
        if (!$clean) {
            continue;
        }
        $key = img_key($clean['gallery_img']);
        if ($key === '' || isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;
        $merged[] = $clean;
    }

    return $merged;
}

function upsert_repeatable_value($db, $text, $pageId, $fieldId, $rows)
{
    $serialized = serialize($rows);
    $existing = one($db, "SELECT page_id FROM `{$text}` WHERE page_id=" . (int) $pageId . " AND field_id=" . (int) $fieldId . " LIMIT 1");
    if ($existing) {
        $db->query("UPDATE `{$text}` SET value=" . q($db, $serialized) . " WHERE page_id=" . (int) $pageId . " AND field_id=" . (int) $fieldId . " LIMIT 1");
    } else {
        $db->query("INSERT INTO `{$text}` (page_id, field_id, value) VALUES (" . (int) $pageId . "," . (int) $fieldId . "," . q($db, $serialized) . ")");
    }
}

function delete_field_tree($db, $fields, $text, $fieldId, $templateId)
{
    $parent = one($db, "SELECT name FROM `{$fields}` WHERE id=" . (int) $fieldId . " LIMIT 1");
    if (!$parent) {
        return;
    }
    $children = all(
        $db,
        "SELECT id FROM `{$fields}` WHERE template_id=" . (int) $templateId .
        " AND k_group=" . q($db, $parent['name'])
    );
    foreach ($children as $child) {
        $db->query("DELETE FROM `{$text}` WHERE field_id=" . (int) $child['id']);
        $db->query("DELETE FROM `{$fields}` WHERE id=" . (int) $child['id']);
    }
    $db->query("DELETE FROM `{$text}` WHERE field_id=" . (int) $fieldId);
    $db->query("DELETE FROM `{$fields}` WHERE id=" . (int) $fieldId);
}

function legacy_grouped($db, $text, $pageId, $fieldId)
{
    $grouped = array(
        'interior' => array(),
        'menu' => array(),
        'vibe' => array(),
    );
    if (!$fieldId) {
        return $grouped;
    }
    $value = one($db, "SELECT value FROM `{$text}` WHERE page_id=" . (int) $pageId . " AND field_id=" . (int) $fieldId . " LIMIT 1");
    if (!$value) {
        return $grouped;
    }
    foreach (decode_repeatable_value($value['value']) as $row) {
        $parsed = extract_row($row);
        if (!$parsed) {
            continue;
        }
        $category = strtolower($parsed['gallery_category'] !== '' ? $parsed['gallery_category'] : '');
        if (in_array($category, array('kitchen', 'food'), true)) {
            $category = 'menu';
        }
        if (!isset($grouped[$category]) || $category === '') {
            $path = strtolower($parsed['gallery_img']);
            if (strpos($path, 'menu-visual') !== false) {
                $category = 'menu';
            } elseif (preg_match('~vibe-(adm|udel)-~', $path) || preg_match('~gf\d+\.webp~', $path)) {
                $category = 'vibe';
            } else {
                $category = 'interior';
            }
        }
        unset($parsed['gallery_category']);
        $clean = clean_row($parsed);
        if ($clean) {
            $grouped[$category][] = $clean;
        }
    }
    return $grouped;
}

function count_non_empty_meta($rows)
{
    $n = 0;
    foreach ($rows as $row) {
        if (!empty($row['gallery_img_title']) || !empty($row['gallery_img_alt'])) {
            $n++;
        }
    }
    return $n;
}

$map = array(
    'interior' => 'gallery_interior_items',
    'menu' => 'gallery_menu_items',
    'vibe' => 'gallery_vibe_items',
);

try {
    foreach (array('gallery.php', 'udelnaya/gallery.php') as $templateName) {
        echo "\n=== {$templateName} ===\n";
        $template = one($db, "SELECT id FROM `{$templates}` WHERE name=" . q($db, $templateName) . " LIMIT 1");
        if (!$template) {
            echo "template missing\n";
            continue;
        }
        $templateId = (int) $template['id'];
        $page = one($db, "SELECT id FROM `{$pages}` WHERE template_id={$templateId} LIMIT 1");
        if (!$page) {
            echo "page missing\n";
            continue;
        }
        $pageId = (int) $page['id'];

        $legacyField = one($db, "SELECT id FROM `{$fields}` WHERE template_id={$templateId} AND name='gallery_items' LIMIT 1");
        $legacy = legacy_grouped($db, $text, $pageId, $legacyField ? (int) $legacyField['id'] : 0);
        if ($legacyField) {
            echo "legacy gallery_items rows: interior=" . count($legacy['interior']) . " menu=" . count($legacy['menu']) . " vibe=" . count($legacy['vibe']) . "\n";
        } else {
            echo "legacy gallery_items: none\n";
        }

        foreach ($map as $category => $fieldName) {
            $field = one($db, "SELECT id FROM `{$fields}` WHERE template_id={$templateId} AND name=" . q($db, $fieldName) . " LIMIT 1");
            if (!$field) {
                echo "{$fieldName}: field missing — run register-gallery-fields-sql.php first\n";
                continue;
            }
            $fieldId = (int) $field['id'];
            $current = read_repeatable_rows($db, $text, $pageId, $fieldId);
            $merged = merge_rows($current, $legacy[$category]);
            $metaBefore = count_non_empty_meta($current);
            upsert_repeatable_value($db, $text, $pageId, $fieldId, $merged);
            echo "{$fieldName}: " . count($merged) . " rows (was " . count($current) . ", merged +" . max(0, count($merged) - count($current)) . ", meta_before={$metaBefore}, meta_after=0)\n";
        }

        if ($legacyField) {
            echo "Removing legacy gallery_items (#{$legacyField['id']})\n";
            delete_field_tree($db, $fields, $text, (int) $legacyField['id'], $templateId);
        }
    }

    require __DIR__ . '/clear-couch-cache-cli.php';
    echo "merge-gallery-split complete.\n";
} catch (Exception $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
