<?php
/**
 * Copy dish descriptions from text menu into visual menu (admin fields).
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/sync-visual-menu-descriptions-web.php?token=gl-cache-clear-20260623
 */
if (!defined('GL_SYNC_LIB_ONLY')) {
    $token = isset($_GET['token']) ? (string) $_GET['token'] : '';
    if ($token !== 'gl-cache-clear-20260623') {
        http_response_code(403);
        exit("Forbidden\n");
    }

    header('Content-Type: text/plain; charset=utf-8');
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

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
        'text_sources' => array(
            array(
                'repeatable' => 'rep_hookahs_v2',
                'name' => 'i_name',
                'desc_fields' => array('i_desc'),
                'price' => 'i_price',
            ),
        ),
        'visual_repeatable' => 'menu_shisha',
        'visual_name' => 'item_title',
        'visual_desc' => 'item_desc',
        'visual_price' => 'item_price',
    ),
    'kitchen' => array(
        'text_sources' => array(
            array(
                'repeatable' => 'rep_kitchen_v2',
                'name' => 'kit_name',
                'desc_fields' => array('kit_desc'),
                'price' => 'kit_price',
            ),
        ),
        'visual_repeatable' => 'menu_kitchen',
        'visual_name' => 'item_title',
        'visual_desc' => 'item_desc',
        'visual_price' => 'item_price',
    ),
    'bar' => array(
        'text_sources' => array(
            array(
                'repeatable' => 'rep_bar_alc_v2',
                'name' => 'i_name',
                'desc_fields' => array('i_subheader', 'note_after_ru'),
                'price' => 'i_price',
            ),
            array(
                'repeatable' => 'rep_bar_non_v2',
                'name' => 'i_name',
                'desc_fields' => array('i_desc', 'note_after_ru'),
                'price' => 'i_price',
            ),
        ),
        'visual_repeatable' => 'menu_bar',
        'visual_name' => 'item_title',
        'visual_desc' => 'item_desc',
        'visual_price' => 'item_price',
    ),
);

function gl_sync_q1($db, $sql)
{
    $res = $db->query($sql);
    return $res ? $res->fetch_assoc() : null;
}

function gl_sync_prepare_name($name)
{
    $name = html_entity_decode(trim((string) $name), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $name = preg_replace('/\s+/u', ' ', $name);

    return $name;
}

function gl_sync_norm_name($name)
{
    $name = gl_sync_prepare_name($name);
    $name = mb_strtolower($name, 'UTF-8');
    $name = str_replace(array("\xe2\x80\x99", "\xe2\x80\x98", '`', '´'), "'", $name);

    return $name;
}

function gl_sync_norm_loose($name)
{
    $name = gl_sync_norm_name($name);
    $name = str_replace(array('&', '＆'), ' ', $name);
    $name = preg_replace('/\([^)]*\)/u', ' ', $name);
    $name = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $name);
    $name = preg_replace('/\s+/u', ' ', $name);

    return trim($name);
}

function gl_sync_price_key($price)
{
    $price = preg_replace('/[^\d]/u', '', (string) $price);
    if ($price === '') {
        return '';
    }

    return ltrim($price, '0');
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

function gl_sync_pick_desc($row, $descFields)
{
    $parts = array();
    foreach ($descFields as $field) {
        $value = gl_sync_row_value($row, $field);
        if ($value !== '') {
            $parts[] = $value;
        }
    }

    $parts = array_values(array_unique($parts));
    if (!$parts) {
        return '';
    }
    if (count($parts) === 1) {
        return $parts[0];
    }

    return implode("\r\n\r\n", $parts);
}

function gl_sync_collect_text_items($rows, $sourceCfg)
{
    $items = array();
    $nameField = $sourceCfg['name'];
    $descFields = $sourceCfg['desc_fields'];
    $priceField = isset($sourceCfg['price']) ? $sourceCfg['price'] : '';

    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }

        $rowType = gl_sync_row_value($row, 'row_type');
        if ($rowType !== '' && $rowType !== 'item') {
            continue;
        }

        $name = gl_sync_prepare_name(gl_sync_row_value($row, $nameField));
        $desc = gl_sync_pick_desc($row, $descFields);
        if ($name === '' || $desc === '') {
            continue;
        }

        $items[] = array(
            'name' => $name,
            'desc' => $desc,
            'price_key' => $priceField !== '' ? gl_sync_price_key(gl_sync_row_value($row, $priceField)) : '',
            'norm' => gl_sync_norm_name($name),
            'loose' => gl_sync_norm_loose($name),
        );
    }

    return $items;
}

function gl_sync_load_section_items($db, $dataText, $fields, $textTpl, $textPageId, $sectionCfg)
{
    $items = array();

    foreach ($sectionCfg['text_sources'] as $sourceCfg) {
        $fieldId = gl_sync_get_repeatable_field_id($db, $fields, $textTpl, $sourceCfg['repeatable']);
        if (!$fieldId) {
            continue;
        }

        list(, $rows) = gl_sync_read_repeatable_rows($db, $dataText, $textPageId, $fieldId);
        $items = array_merge($items, gl_sync_collect_text_items($rows, $sourceCfg));
    }

    return $items;
}

function gl_sync_name_tokens($name)
{
    $name = gl_sync_norm_loose($name);
    if ($name === '') {
        return array();
    }

    $parts = preg_split('/\s+/u', $name);
    $tokens = array();
    foreach ($parts as $part) {
        if (mb_strlen($part, 'UTF-8') >= 2) {
            $tokens[] = $part;
        }
    }

    return array_values(array_unique($tokens));
}

function gl_sync_tokens_subset($needleTokens, $haystackTokens)
{
    if (!$needleTokens) {
        return false;
    }

    $haystack = array_flip($haystackTokens);
    foreach ($needleTokens as $token) {
        if (!isset($haystack[$token])) {
            return false;
        }
    }

    return true;
}

function gl_sync_find_desc($visualName, $visualPrice, $items)
{
    $visualName = gl_sync_prepare_name($visualName);
    if ($visualName === '' || !$items) {
        return null;
    }

    $norm = gl_sync_norm_name($visualName);
    $loose = gl_sync_norm_loose($visualName);
    $priceKey = gl_sync_price_key($visualPrice);
    $tokens = gl_sync_name_tokens($visualName);

    foreach ($items as $item) {
        if ($item['norm'] === $norm || $item['loose'] === $loose) {
            return $item;
        }
    }

    foreach ($items as $item) {
        if ($loose !== '' && $item['loose'] !== '' && (
            mb_stripos($item['loose'], $loose) !== false
            || mb_stripos($loose, $item['loose']) !== false
        )) {
            return $item;
        }
    }

    if ($tokens) {
        $best = null;
        $bestScore = 0;
        foreach ($items as $item) {
            $itemTokens = gl_sync_name_tokens($item['name']);
            if (!$itemTokens) {
                continue;
            }

            $short = count($tokens) <= count($itemTokens) ? $tokens : $itemTokens;
            $long = count($tokens) <= count($itemTokens) ? $itemTokens : $tokens;
            if (!gl_sync_tokens_subset($short, $long)) {
                continue;
            }

            $score = count($short);
            if ($priceKey !== '' && $priceKey === $item['price_key']) {
                $score += 3;
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $item;
            }
        }
        if ($best) {
            return $best;
        }
    }

    if ($priceKey !== '') {
        $best = null;
        $bestPct = 0;
        foreach ($items as $item) {
            if ($item['price_key'] === '' || $item['price_key'] !== $priceKey) {
                continue;
            }
            similar_text($loose, $item['loose'], $pct);
            if ($pct > $bestPct) {
                $bestPct = $pct;
                $best = $item;
            }
        }
        if ($best && $bestPct >= 55) {
            return $best;
        }
    }

    $best = null;
    $bestPct = 0;
    foreach ($items as $item) {
        similar_text($loose, $item['loose'], $pct);
        if ($pct > $bestPct) {
            $bestPct = $pct;
            $best = $item;
        }
    }
    if ($best && $bestPct >= 88) {
        return $best;
    }

    return null;
}

function gl_sync_apply_descriptions_fuzzy($visualRows, $textItems, $nameField, $descField, $priceField, $fillEmptyOnly = false)
{
    $updated = 0;
    $matched = 0;
    $unchanged = 0;
    $noMatch = 0;
    $examples = array();

    foreach ($visualRows as $idx => $row) {
        if (!is_array($row)) {
            continue;
        }

        $name = gl_sync_row_value($row, $nameField);
        if ($name === '') {
            continue;
        }

        $match = gl_sync_find_desc($name, gl_sync_row_value($row, $priceField), $textItems);
        if (!$match) {
            $noMatch++;
            continue;
        }

        $matched++;
        $newDesc = $match['desc'];
        $oldDesc = gl_sync_row_value($row, $descField);

        if ($oldDesc === $newDesc) {
            $unchanged++;
            continue;
        }

        if ($fillEmptyOnly && $oldDesc !== '') {
            $unchanged++;
            continue;
        }

        gl_sync_set_row_value($visualRows[$idx], $descField, $newDesc);
        $updated++;
        if (count($examples) < 8) {
            $examples[] = gl_sync_prepare_name($name) . ' <= ' . $match['name'];
        }
    }

    return array($visualRows, $updated, $matched, $unchanged, $noMatch, $examples);
}

if (defined('GL_SYNC_LIB_ONLY')) {
    return;
}

$totals = array(
    'updated' => 0,
    'matched' => 0,
    'unchanged' => 0,
    'no_match' => 0,
);

$before = array(
    'updated' => 44,
    'matched' => 44,
    'unchanged' => 0,
    'no_match' => 61,
);

$fillEmptyOnly = (isset($_GET['fill_empty']) ? $_GET['fill_empty'] : '') === '1';

echo "Sync visual menu descriptions from text menu (DB JSON, fuzzy)\n";
echo str_repeat('=', 60) . "\n";
if ($fillEmptyOnly) {
    echo "Mode: fill_empty=1 (only blank visual descriptions)\n";
}
echo "Previous run: matched={$before['matched']}, updated={$before['updated']}, unchanged={$before['unchanged']}, no_match={$before['no_match']}\n";

$admiralTextItems = array();
foreach ($sections as $section => $cfg) {
    $admiralTextItems[$section] = gl_sync_load_section_items(
        $db,
        $dataText,
        $fields,
        $branches['admiralteyskaya']['text'],
        gl_sync_get_master_page_id($db, $templates, $pages, $branches['admiralteyskaya']['text']),
        $cfg
    );
}

foreach ($branches as $branch => $tpls) {
    echo "\nBranch: {$branch}\n";

    $textPageId = gl_sync_get_master_page_id($db, $templates, $pages, $tpls['text']);
    $visualPageId = gl_sync_get_master_page_id($db, $templates, $pages, $tpls['visual']);
    if (!$textPageId || !$visualPageId) {
        echo "  ERROR missing page (text={$textPageId}, visual={$visualPageId})\n";
        continue;
    }

    foreach ($sections as $section => $cfg) {
        $visualFieldId = gl_sync_get_repeatable_field_id($db, $fields, $tpls['visual'], $cfg['visual_repeatable']);
        if (!$visualFieldId) {
            echo "  {$section}: missing visual field\n";
            continue;
        }

        $textItems = gl_sync_load_section_items($db, $dataText, $fields, $tpls['text'], $textPageId, $cfg);
        if ($branch === 'udelnaya') {
            $textItems = array_merge($textItems, $admiralTextItems[$section]);
        }

        list($visualFormat, $visualRows) = gl_sync_read_repeatable_rows($db, $dataText, $visualPageId, $visualFieldId);
        list($visualRows, $updated, $matched, $unchanged, $noMatch, $examples) = gl_sync_apply_descriptions_fuzzy(
            $visualRows,
            $textItems,
            $cfg['visual_name'],
            $cfg['visual_desc'],
            $cfg['visual_price'],
            $fillEmptyOnly
        );

        echo "  {$section}: text_items_with_desc=" . count($textItems)
            . ", visual_items=" . count($visualRows)
            . ", matched={$matched}, updated={$updated}, unchanged={$unchanged}, no_match={$noMatch}\n";
        if ($examples) {
            echo "    examples: " . implode(' | ', $examples) . "\n";
        }

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
echo "Delta vs previous: matched +" . ($totals['matched'] - $before['matched'])
    . ", no_match " . ($totals['no_match'] - $before['no_match']) . "\n";
echo "Cache files removed: {$removed}\n";
