<?php
/**
 * Sync visual item_desc + item_weight from the text menu.
 * Descriptions and gram/volume follow the text menu; titles stay as in visual.
 *
 * Dry-run: ?token=gl-cache-clear-20260623
 * Apply:   ?token=gl-cache-clear-20260623&confirm=apply
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit("Forbidden\n");
}

header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

register_shutdown_function(function () {
    $err = error_get_last();
    if (!$err) {
        return;
    }
    $fatal = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);
    if (!in_array($err['type'], $fatal, true)) {
        return;
    }
    echo "\nFATAL: {$err['message']} in {$err['file']}:{$err['line']}\n";
});

$apply = (isset($_GET['confirm']) ? (string) $_GET['confirm'] : '') === 'apply';

try {
    define('GL_SYNC_LIB_ONLY', true);
    require_once __DIR__ . '/sync-visual-menu-descriptions-web.php';
    if (!function_exists('gl_format_visual_desc')) {
        if (!defined('GL_FORMAT_LIB_ONLY')) {
            define('GL_FORMAT_LIB_ONLY', true);
        }
        $formatFile = __DIR__ . '/format-visual-descriptions-web.php';
        if (is_file($formatFile)) {
            require_once $formatFile;
        }
    }
    if (!function_exists('gl_format_visual_desc')) {
        function gl_format_visual_desc($desc)
        {
            return trim((string) $desc);
        }
    }
} catch (Throwable $e) {
    echo "BOOT ERROR: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}\n";
    exit(1);
}

function gl_weight_cleanup($text)
{
    $text = html_entity_decode(trim((string) $text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = str_replace(array("\xc2\xa0", "\xe2\x80\xaf"), ' ', $text);
    $text = preg_replace('/\s+/u', ' ', $text);

    return trim($text);
}

function gl_norm_weight($text)
{
    $text = gl_weight_cleanup($text);
    $text = preg_replace('/\s*\/\s*/u', '/', $text);
    $text = preg_replace('/[\s\-]*(г|мл|ml|л)\.?/ui', '$1', $text);

    return trim($text);
}

function gl_looks_like_weight($text)
{
    $text = gl_norm_weight($text);
    if ($text === '') {
        return false;
    }

    return (bool) preg_match('/^\d[\d.,]*(г|мл|ml|л)?(\/\d[\d.,]*(г|мл|ml|л)?)*(г|мл|ml|л)$/ui', $text);
}

function gl_pick_trailing_weight($text)
{
    $text = gl_weight_cleanup($text);
    if ($text === '') {
        return '';
    }
    if (gl_looks_like_weight($text)) {
        return $text;
    }
    if (preg_match('/(?:^|[\/,|;]\s*)(\d[\d\s\/.,]*(?:г|мл|ml|л)\.?)\s*$/ui', $text, $m) && gl_looks_like_weight($m[1])) {
        return gl_weight_cleanup($m[1]);
    }

    return '';
}

function gl_extract_weight($note, $subheader, $desc)
{
    foreach (array($note, $subheader, $desc) as $candidate) {
        $weight = gl_pick_trailing_weight($candidate);
        if ($weight !== '') {
            return $weight;
        }
    }

    return '';
}

function gl_strip_weight_from_desc($desc, $weight)
{
    $desc = trim(str_replace("\r\n", "\n", (string) $desc));
    $weight = gl_weight_cleanup($weight);
    if ($desc === '' || $weight === '') {
        return $desc;
    }

    $quoted = preg_quote($weight, '/');
    $desc = preg_replace('/(?:\s*[\/,|;]\s*|\n+)' . $quoted . '\s*$/ui', '', $desc);
    $normWeight = gl_norm_weight($weight);
    $lines = preg_split("/\n+/u", $desc);
    if ($lines) {
        $last = gl_norm_weight(end($lines));
        if ($last !== '' && strcasecmp($last, $normWeight) === 0) {
            array_pop($lines);
            $desc = implode("\n\n", array_map('trim', $lines));
        }
    }

    return trim($desc);
}

function gl_is_prose_desc($text)
{
    $text = trim((string) $text);
    if ($text === '') {
        return false;
    }
    if (gl_looks_like_weight($text)) {
        return false;
    }

    return true;
}

function gl_collect_text_rich($rows, $nameField, $descField, $noteField, $subField, $priceField)
{
    $items = array();
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $rowType = gl_sync_row_value($row, 'row_type');
        if ($rowType !== '' && $rowType !== 'item') {
            continue;
        }
        $name = gl_sync_prepare_name(gl_sync_row_value($row, $nameField));
        if ($name === '') {
            continue;
        }
        $desc = $descField !== '' ? gl_sync_row_value($row, $descField) : '';
        $note = $noteField !== '' ? gl_sync_row_value($row, $noteField) : '';
        $sub = $subField !== '' ? gl_sync_row_value($row, $subField) : '';
        $items[] = array(
            'name' => $name,
            'desc' => $desc,
            'note' => $note,
            'sub' => $sub,
            'price_key' => $priceField !== '' ? gl_sync_price_key(gl_sync_row_value($row, $priceField)) : '',
            'norm' => gl_sync_norm_name($name),
            'loose' => gl_sync_norm_loose($name),
        );
    }

    return $items;
}

function gl_preview($text, $limit = 80)
{
    $text = preg_replace('/\s+/u', ' ', trim((string) $text));
    if (mb_strlen($text, 'UTF-8') <= $limit) {
        return $text;
    }

    return mb_substr($text, 0, $limit, 'UTF-8') . '…';
}

function gl_alias_titles($title)
{
    $norm = gl_sync_norm_name($title);
    $map = array(
        'grapefruit' => array('Грейпфрут'),
        'garnet' => array('Гранат'),
        'dragon fruit' => array('Питахайа', 'Питахайя'),
        'крем-суп из лосося' => array(
            'Крем суп с лососем,брокколи и зелёным маслом',
            'Крем-суп с лососем, брокколи и маслом',
        ),
        'крем суп из лосося' => array(
            'Крем суп с лососем,брокколи и зелёным маслом',
            'Крем-суп с лососем, брокколи и маслом',
        ),
        'крем-суп с лососем, брокколи и маслом' => array(
            'Крем суп с лососем,брокколи и зелёным маслом',
            'Крем-суп из лосося',
        ),
        'говядина сырокопченая' => array('Сырокопченое мясо'),
        'курица сырокопченая' => array('Сырокопченое мясо'),
        'сырокопченое мясо' => array('Говядина сырокопченая', 'Курица сырокопченая'),
    );

    $titles = array($title);
    if (isset($map[$norm])) {
        $titles = array_merge($titles, $map[$norm]);
    }

    return $titles;
}

function gl_find_text_item($title, $price, $items)
{
    $loose = gl_sync_norm_loose($title);
    if (preg_match('/сырокопч/u', $loose)) {
        $want = '';
        if (preg_match('/говядин/u', $loose)) {
            $want = 'говядин';
        } elseif (preg_match('/куриц/u', $loose)) {
            $want = 'куриц';
        }
        if ($want !== '') {
            foreach ($items as $item) {
                $blob = $item['loose'] . ' ' . gl_sync_norm_loose($item['desc'] . ' ' . $item['note']);
                if (!preg_match('/сырокопч/u', $blob) && !preg_match('/сырокопч/u', $item['loose'])) {
                    continue;
                }
                if (preg_match('/' . $want . '/u', $blob)) {
                    return $item;
                }
            }
        }
    }

    foreach (gl_alias_titles($title) as $candidate) {
        $found = gl_sync_find_desc($candidate, $price, $items);
        if ($found) {
            return $found;
        }
    }

    return null;
}

$branchMap = array(
    'admiralteyskaya' => array(
        'text' => 'menu/text/index.php',
        'visual' => 'menu/visual/index.php',
    ),
    'udelnaya' => array(
        'text' => 'udelnaya/menu/text/index.php',
        'visual' => 'udelnaya/menu/visual/index.php',
    ),
);

$jobs = array(
    'kitchen' => array(
        'visual' => 'menu_kitchen',
        'text' => array(
            array('rep_kitchen_v2', 'kit_name', 'kit_desc', 'note_after_ru', '', 'kit_price'),
        ),
        'prefer_text_desc' => true,
        'sub_as_desc' => false,
    ),
    'hookahs' => array(
        'visual' => 'menu_shisha',
        'text' => array(
            array('rep_hookahs_v2', 'i_name', 'i_desc', 'note_after_ru', '', 'i_price'),
        ),
        'prefer_text_desc' => true,
        'sub_as_desc' => false,
    ),
    'bar' => array(
        'visual' => 'menu_bar',
        'text' => array(
            array('rep_bar_alc_v2', 'i_name', 'i_desc', 'note_after_ru', 'i_subheader', 'i_price'),
            array('rep_bar_non_v2', 'i_name', 'i_desc', 'note_after_ru', 'i_subheader', 'i_price'),
        ),
        'prefer_text_desc' => false,
        'sub_as_desc' => true,
    ),
);

echo "Sync visual descriptions + weights from text menu\n";
echo str_repeat('=', 60) . "\n";
echo $apply ? "Mode: APPLY\n\n" : "Mode: dry-run (add &confirm=apply to write)\n\n";

$totals = array('desc' => 0, 'weight' => 0, 'matched' => 0, 'unmatched' => 0);

try {
    foreach ($branchMap as $branch => $tpls) {
        $textPageId = gl_sync_get_master_page_id($db, $templates, $pages, $tpls['text']);
        $visualPageId = gl_sync_get_master_page_id($db, $templates, $pages, $tpls['visual']);
        echo "== {$branch} text={$textPageId} visual={$visualPageId} ==\n";

        foreach ($jobs as $section => $job) {
            $textItems = array();
            foreach ($job['text'] as $src) {
                list($rep, $nameField, $descField, $noteField, $subField, $priceField) = $src;
                $fieldId = gl_sync_get_repeatable_field_id($db, $fields, $tpls['text'], $rep);
                if (!$fieldId) {
                    continue;
                }
                list(, $rows) = gl_sync_read_repeatable_rows($db, $dataText, $textPageId, $fieldId);
                $textItems = array_merge(
                    $textItems,
                    gl_collect_text_rich($rows, $nameField, $descField, $noteField, $subField, $priceField)
                );
            }

            $visualFieldId = gl_sync_get_repeatable_field_id($db, $fields, $tpls['visual'], $job['visual']);
            if (!$visualFieldId) {
                echo "  {$section}: missing visual field\n";
                continue;
            }
            list($format, $visualRows) = gl_sync_read_repeatable_rows($db, $dataText, $visualPageId, $visualFieldId);

            $descUpdated = 0;
            $weightUpdated = 0;
            $matched = 0;
            $unmatched = array();
            $examples = array();
            foreach ($visualRows as $idx => $row) {
                if (!is_array($row)) {
                    continue;
                }
                if (trim(gl_sync_row_value($row, 'item_type')) === 'header') {
                    continue;
                }
                $title = gl_sync_row_value($row, 'item_title');
                if ($title === '') {
                    continue;
                }
                $match = gl_find_text_item($title, gl_sync_row_value($row, 'item_price'), $textItems);
                if (!$match) {
                    $unmatched[] = gl_sync_prepare_name($title);
                    continue;
                }
                $matched++;

                $changed = array();
                $textDesc = trim((string) $match['desc']);
                $textSub = isset($match['sub']) ? trim((string) $match['sub']) : '';
                $currentDesc = gl_sync_row_value($row, 'item_desc');
                $weight = gl_extract_weight(
                    isset($match['note']) ? $match['note'] : '',
                    $textSub,
                    $textDesc !== '' ? $textDesc : $currentDesc
                );

                $candidateDesc = '';
                if ($textDesc !== '' && gl_is_prose_desc($textDesc)) {
                    $candidateDesc = $textDesc;
                } elseif (!empty($job['sub_as_desc']) && $textSub !== '' && !gl_looks_like_weight($textSub)) {
                    $candidateDesc = $textSub;
                }

                $newDesc = $currentDesc;
                if ($candidateDesc !== '') {
                    if (!empty($job['prefer_text_desc']) || $currentDesc === '' || !gl_is_prose_desc($currentDesc)) {
                        $newDesc = $candidateDesc;
                    }
                }
                if ($weight !== '') {
                    $newDesc = gl_strip_weight_from_desc($newDesc, $weight);
                }
                if ($newDesc !== '') {
                    $formatted = gl_format_visual_desc($newDesc);
                    if ($formatted !== '') {
                        $newDesc = $formatted;
                    }
                }
                if ($newDesc !== $currentDesc) {
                    gl_sync_set_row_value($visualRows[$idx], 'item_desc', $newDesc);
                    $descUpdated++;
                    $changed[] = 'desc:' . gl_preview($currentDesc, 24) . ' => ' . gl_preview($newDesc, 36);
                }

                $currentWeight = gl_weight_cleanup(gl_sync_row_value($row, 'item_weight'));
                if ($weight !== '' && gl_norm_weight($weight) !== gl_norm_weight($currentWeight)) {
                    gl_sync_set_row_value($visualRows[$idx], 'item_weight', $weight);
                    $weightUpdated++;
                    $changed[] = 'weight:' . ($currentWeight !== '' ? $currentWeight : '∅') . ' => ' . $weight;
                }

                if ($changed && count($examples) < 12) {
                    $examples[] = gl_sync_prepare_name($title) . ' [' . implode('; ', $changed) . ']';
                }
            }

            echo "  {$section}: text=" . count($textItems)
                . " visual=" . count($visualRows)
                . " matched={$matched} unmatched=" . count($unmatched)
                . " desc={$descUpdated} weight={$weightUpdated}\n";
            foreach ($examples as $example) {
                echo "    - {$example}\n";
            }
            if ($unmatched) {
                echo "    unmatched: " . implode(' | ', array_slice($unmatched, 0, 12))
                    . (count($unmatched) > 12 ? ' …' : '') . "\n";
            }
            if ($apply && ($descUpdated > 0 || $weightUpdated > 0)) {
                gl_sync_write_repeatable_rows($db, $dataText, $visualPageId, $visualFieldId, $visualRows, $format);
                echo "    saved\n";
            }
            $totals['desc'] += $descUpdated;
            $totals['weight'] += $weightUpdated;
            $totals['matched'] += $matched;
            $totals['unmatched'] += count($unmatched);
        }
        echo "\n";
    }
} catch (Throwable $e) {
    echo "RUN ERROR: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}\n";
    exit(1);
}

echo str_repeat('=', 60) . "\n";
echo "Done. desc=" . $totals['desc'] . " weight=" . $totals['weight']
    . " matched=" . $totals['matched'] . " unmatched=" . $totals['unmatched'] . "\n";
if (!$apply && ($totals['desc'] + $totals['weight']) > 0) {
    echo "Re-run with &confirm=apply to write.\n";
}

$cacheBase = (isset($config) && is_string($config)) ? $config : (__DIR__ . '/../couch/config.php');
$cacheDir = dirname($cacheBase) . '/cache';
$removed = 0;
if ($apply && is_dir($cacheDir)) {
    foreach (glob($cacheDir . '/*') as $file) {
        if (is_file($file) && basename($file) !== '.htaccess') {
            if (@unlink($file)) {
                $removed++;
            }
        }
    }
    echo "Cache files removed: {$removed}\n";
}
