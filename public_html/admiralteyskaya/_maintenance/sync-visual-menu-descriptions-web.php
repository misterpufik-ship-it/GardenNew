<?php
/**
 * Copy dish descriptions from text menu into visual menu (admin fields).
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/sync-visual-menu-descriptions-web.php?token=gl-cache-clear-20260623
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit("Forbidden\n");
}

header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

$config = __DIR__ . '/../couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ?: 3306;
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
}

$db = @new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int) $port);
if ($db->connect_errno) {
    exit("DB connection failed: {$db->connect_error}\n");
}
$db->set_charset('utf8');

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';
$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$dataText = K_DB_TABLES_PREFIX . 'couch_data_text';

$branches = array(
    'admiralteyskaya' => array(
        'text' => 'menu/text/index.php',
        'visual' => 'menu/visual/index.php',
    ),
    'udelnaya' => array(
        'text' => 'udelnaya/menu/text/index.php',
        'visual' => 'udelnaya/menu/visual/index.php',
    ),
);

$sections = array(
    'hookahs' => array(
        'text_repeatable' => 'rep_hookahs_v2',
        'text_name' => 'i_name',
        'text_desc' => 'i_desc',
        'visual_repeatable' => 'menu_shisha',
        'visual_name' => 'item_title',
        'visual_desc' => 'item_desc',
    ),
    'kitchen' => array(
        'text_repeatable' => 'rep_kitchen_v2',
        'text_name' => 'kit_name',
        'text_desc' => 'kit_desc',
        'visual_repeatable' => 'menu_kitchen',
        'visual_name' => 'item_title',
        'visual_desc' => 'item_desc',
    ),
    'bar' => array(
        'text_repeatable' => 'rep_bar_alc_v2',
        'text_name' => 'i_name',
        'text_desc' => 'i_subheader',
        'visual_repeatable' => 'menu_bar',
        'visual_name' => 'item_title',
        'visual_desc' => 'item_desc',
    ),
);

function gl_sync_q1($db, $sql)
{
    $res = $db->query($sql);
    return $res ? $res->fetch_assoc() : null;
}

function gl_sync_norm_name($name)
{
    $name = trim((string) $name);
    $name = preg_replace('/\s+/u', ' ', $name);
    $name = mb_strtolower($name, 'UTF-8');
    $name = str_replace(array("\xe2\x80\x99", "\xe2\x80\x98", '`', '´'), "'", $name);

    return $name;
}

function gl_sync_get_master_page_id($db, $templates, $pages, $templateName)
{
    $tplEsc = $db->real_escape_string($templateName);
    $tpl = gl_sync_q1($db, "SELECT id FROM `{$templates}` WHERE name='{$tplEsc}' LIMIT 1");
    if (!$tpl) {
        return 0;
    }
    $tplId = (int) $tpl['id'];
    $page = gl_sync_q1($db, "SELECT id FROM `{$pages}` WHERE template_id={$tplId} ORDER BY is_master DESC, id ASC LIMIT 1");
    return $page ? (int) $page['id'] : 0;
}

function gl_sync_get_repeatable_field_id($db, $fields, $templateName, $repeatableName)
{
    global $templates;
    $tplEsc = $db->real_escape_string($templateName);
    $repEsc = $db->real_escape_string($repeatableName);
    $row = gl_sync_q1(
        $db,
        "SELECT f.id FROM `{$fields}` f " .
        "JOIN `{$templates}` t ON t.id=f.template_id " .
        "WHERE t.name='{$tplEsc}' AND f.name='{$repEsc}' LIMIT 1"
    );
    return $row ? (int) $row['id'] : 0;
}

function gl_sync_decode_rows($raw)
{
    $raw = (string) $raw;
    if ($raw === '') {
        return array('format' => 'json', 'rows' => array());
    }

    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        return array('format' => 'json', 'rows' => $decoded);
    }

    $un = @unserialize($raw);
    if (is_array($un)) {
        return array('format' => 'serialize', 'rows' => $un);
    }

    return array('format' => 'unknown', 'rows' => array());
}

function gl_sync_encode_rows($rows, $format)
{
    if ($format === 'serialize') {
        return serialize($rows);
    }

    return json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function gl_sync_read_repeatable_rows($db, $dataText, $pageId, $fieldId)
{
    $row = gl_sync_q1(
        $db,
        "SELECT value FROM `{$dataText}` WHERE page_id={$pageId} AND field_id={$fieldId} LIMIT 1"
    );
    if (!$row) {
        return array('json', array());
    }

    $parsed = gl_sync_decode_rows($row['value']);
    return array($parsed['format'], $parsed['rows']);
}

function gl_sync_write_repeatable_rows($db, $dataText, $pageId, $fieldId, $rows, $format)
{
    $encoded = gl_sync_encode_rows($rows, $format);
    if ($encoded === false || $encoded === null) {
        throw new RuntimeException('Failed to encode repeatable data');
    }
    $encodedEsc = $db->real_escape_string($encoded);

    $exists = gl_sync_q1(
        $db,
        "SELECT 1 AS ok FROM `{$dataText}` WHERE page_id={$pageId} AND field_id={$fieldId} LIMIT 1"
    );
    if ($exists) {
        $db->query("UPDATE `{$dataText}` SET value='{$encodedEsc}' WHERE page_id={$pageId} AND field_id={$fieldId} LIMIT 1");
        return;
    }

    $db->query("INSERT INTO `{$dataText}` (page_id, field_id, value) VALUES ({$pageId}, {$fieldId}, '{$encodedEsc}')");
}

function gl_sync_decode_field_value($value)
{
    if (is_array($value)) {
        return '';
    }

    $value = (string) $value;
    if ($value === '') {
        return '';
    }

    $decoded = base64_decode($value, true);
    if ($decoded === false) {
        return $value;
    }

    if (function_exists('mb_check_encoding') && !mb_check_encoding($decoded, 'UTF-8')) {
        return $value;
    }

    return $decoded;
}

function gl_sync_encode_field_value($value)
{
    return base64_encode((string) $value);
}

function gl_sync_row_value($row, $key)
{
    if (!is_array($row) || !isset($row[$key])) {
        return '';
    }

    return trim(gl_sync_decode_field_value($row[$key]));
}

function gl_sync_set_row_value(&$row, $key, $value)
{
    if (!is_array($row)) {
        return;
    }

    $row[$key] = gl_sync_encode_field_value($value);
}

function gl_sync_build_desc_map($rows, $nameField, $descField)
{
    $map = array();

    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }

        $rowType = gl_sync_row_value($row, 'row_type');
        if ($rowType !== '' && $rowType !== 'item') {
            continue;
        }

        $name = gl_sync_row_value($row, $nameField);
        $desc = gl_sync_row_value($row, $descField);
        if ($name === '' || $desc === '') {
            continue;
        }

        $key = gl_sync_norm_name($name);
        if ($key === '') {
            continue;
        }

        $map[$key] = $desc;
    }

    return $map;
}

function gl_sync_apply_descriptions($visualRows, $descMap, $nameField, $descField)
{
    $updated = 0;
    $matched = 0;
    $unchanged = 0;
    $noMatch = 0;

    foreach ($visualRows as $idx => $row) {
        if (!is_array($row)) {
            continue;
        }

        $name = gl_sync_row_value($row, $nameField);
        if ($name === '') {
            continue;
        }

        $key = gl_sync_norm_name($name);
        if (!isset($descMap[$key])) {
            $noMatch++;
            continue;
        }

        $matched++;
        $newDesc = $descMap[$key];
        $oldDesc = gl_sync_row_value($row, $descField);

        if ($oldDesc === $newDesc) {
            $unchanged++;
            continue;
        }

        gl_sync_set_row_value($visualRows[$idx], $descField, $newDesc);
        $updated++;
    }

    return array($visualRows, $updated, $matched, $unchanged, $noMatch);
}

$totals = array(
    'updated' => 0,
    'matched' => 0,
    'unchanged' => 0,
    'no_match' => 0,
);

echo "Sync visual menu descriptions from text menu (DB JSON)\n";
echo str_repeat('=', 60) . "\n";

foreach ($branches as $branch => $tpls) {
    echo "\nBranch: {$branch}\n";

    $textPageId = gl_sync_get_master_page_id($db, $templates, $pages, $tpls['text']);
    $visualPageId = gl_sync_get_master_page_id($db, $templates, $pages, $tpls['visual']);
    if (!$textPageId || !$visualPageId) {
        echo "  ERROR missing page (text={$textPageId}, visual={$visualPageId})\n";
        continue;
    }

    foreach ($sections as $section => $cfg) {
        $textFieldId = gl_sync_get_repeatable_field_id($db, $fields, $tpls['text'], $cfg['text_repeatable']);
        $visualFieldId = gl_sync_get_repeatable_field_id($db, $fields, $tpls['visual'], $cfg['visual_repeatable']);

        if (!$textFieldId || !$visualFieldId) {
            echo "  {$section}: missing field (text={$textFieldId}, visual={$visualFieldId})\n";
            continue;
        }

        list($textFormat, $textRows) = gl_sync_read_repeatable_rows($db, $dataText, $textPageId, $textFieldId);
        list($visualFormat, $visualRows) = gl_sync_read_repeatable_rows($db, $dataText, $visualPageId, $visualFieldId);

        $descMap = gl_sync_build_desc_map($textRows, $cfg['text_name'], $cfg['text_desc']);
        list($visualRows, $updated, $matched, $unchanged, $noMatch) = gl_sync_apply_descriptions(
            $visualRows,
            $descMap,
            $cfg['visual_name'],
            $cfg['visual_desc']
        );

        echo "  {$section}: text_items_with_desc=" . count($descMap)
            . ", visual_items=" . count($visualRows)
            . ", matched={$matched}, updated={$updated}, unchanged={$unchanged}, no_match={$noMatch}\n";

        if ($updated > 0) {
            gl_sync_write_repeatable_rows($db, $dataText, $visualPageId, $visualFieldId, $visualRows, $visualFormat);
            echo "    saved {$updated} update(s) to {$cfg['visual_repeatable']}\n";
        }

        $totals['updated'] += $updated;
        $totals['matched'] += $matched;
        $totals['unchanged'] += $unchanged;
        $totals['no_match'] += $noMatch;
    }
}

$cacheDir = dirname($config) . '/cache';
$removed = 0;
if (is_dir($cacheDir)) {
    foreach (glob($cacheDir . '/*') as $file) {
        if (is_file($file) && basename($file) !== '.htaccess') {
            if (@unlink($file)) {
                $removed++;
            }
        }
    }
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "Done. Total updated={$totals['updated']}, matched={$totals['matched']}, unchanged={$totals['unchanged']}, no_match={$totals['no_match']}\n";
echo "Cache files removed: {$removed}\n";
