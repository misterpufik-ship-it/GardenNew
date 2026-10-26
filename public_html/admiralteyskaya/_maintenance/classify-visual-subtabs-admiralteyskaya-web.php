<?php
/**
 * Assign visual menu item_subtab on Admiralteyskaya from the text menu (name/price match).
 * Does not add, delete, or move rows. Drinks stay in menu_bar.
 *
 * Dry-run by default:
 *   ?token=gl-cache-clear-20260623
 * Apply:
 *   ?token=gl-cache-clear-20260623&confirm=apply
 * Overwrite already filled subtabs:
 *   &force=1
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit("Forbidden\n");
}

header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

$apply = (isset($_GET['confirm']) ? (string) $_GET['confirm'] : '') === 'apply';
$force = (isset($_GET['force']) ? (string) $_GET['force'] : '') === '1';

define('GL_SYNC_LIB_ONLY', true);
require_once __DIR__ . '/sync-visual-menu-descriptions-web.php';

$visualTpl = 'menu/visual/index.php';
$textTpl = 'menu/text/index.php';

$kitchenSlugs = array('snacks', 'salads', 'rolls', 'soups', 'poke_bowl_wok', 'hot', 'desserts', 'other');
$barSlugs = array('beer', 'wine', 'cocktails', 'spirits', 'tea_coffee', 'lemonades', 'other');
$barHeaderNorms = array(
    'signature coctails' => true,
    'classic coctails' => true,
    'shot line' => true,
    'shot drink' => true,
    'homemade lemonade 400ml/1000ml' => true,
    'lemonade/juice' => true,
    'still/sparkling water' => true,
    'beer' => true,
    'draft beer' => true,
    'honey mead' => true,
    'non-alcohol coctail' => true,
    'vodka' => true,
    'rum' => true,
    'whiskey/burbon' => true,
    'gin' => true,
    'brandy / cognac' => true,
    'tequila / mescal' => true,
    'sparkling wine' => true,
    'red wine' => true,
    'white wine' => true,
);

function gl_subtab_collect_text_items($rows, $nameField, $subtabField, $priceField, $allowed)
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
        $sub = trim(gl_sync_row_value($row, $subtabField));
        if ($name === '' || $sub === '' || !in_array($sub, $allowed, true)) {
            continue;
        }
        $items[] = array(
            'name' => $name,
            'sub' => $sub,
            'price_key' => gl_sync_price_key(gl_sync_row_value($row, $priceField)),
            'norm' => gl_sync_norm_name($name),
            'loose' => gl_sync_norm_loose($name),
        );
    }

    return $items;
}

function gl_subtab_load_text($db, $dataText, $fields, $textTpl, $textPageId, $repeatable, $nameField, $subtabField, $priceField, $allowed)
{
    $fieldId = gl_sync_get_repeatable_field_id($db, $fields, $textTpl, $repeatable);
    if (!$fieldId) {
        return array();
    }
    list(, $rows) = gl_sync_read_repeatable_rows($db, $dataText, $textPageId, $fieldId);

    return gl_subtab_collect_text_items($rows, $nameField, $subtabField, $priceField, $allowed);
}

function gl_subtab_apply_section(&$visualRows, $textItems, $allowed, $force)
{
    $matched = 0;
    $updated = 0;
    $unchanged = 0;
    $skippedFilled = 0;
    $noMatch = 0;
    $examples = array();

    foreach ($visualRows as $idx => $row) {
        if (!is_array($row)) {
            continue;
        }
        $name = gl_sync_row_value($row, 'item_title');
        if ($name === '') {
            continue;
        }
        if (trim(gl_sync_row_value($row, 'item_type')) === 'header') {
            continue;
        }

        $current = trim(gl_sync_row_value($row, 'item_subtab'));
        global $barHeaderNorms;
        if (!empty($barHeaderNorms[gl_sync_norm_name($name)])) {
            $noMatch++;
            continue;
        }
        $match = gl_sync_find_desc($name, gl_sync_row_value($row, 'item_price'), $textItems);
        if (!$match || empty($match['sub']) || !in_array($match['sub'], $allowed, true)) {
            $noMatch++;
            continue;
        }

        $matched++;
        $newSub = $match['sub'];
        if ($current === $newSub) {
            $unchanged++;
            continue;
        }
        if ($current !== '' && !$force) {
            $skippedFilled++;
            if (count($examples) < 12) {
                $examples[] = gl_sync_prepare_name($name) . " keep={$current} (text={$newSub})";
            }
            continue;
        }

        gl_sync_set_row_value($visualRows[$idx], 'item_subtab', $newSub);
        $updated++;
        if (count($examples) < 12) {
            $from = $current === '' ? '(empty)' : $current;
            $examples[] = gl_sync_prepare_name($name) . " {$from} => {$newSub} (text: {$match['name']})";
        }
    }

    return array($matched, $updated, $unchanged, $skippedFilled, $noMatch, $examples);
}

$visualPageId = gl_sync_get_master_page_id($db, $templates, $pages, $visualTpl);
$textPageId = gl_sync_get_master_page_id($db, $templates, $pages, $textTpl);
if (!$visualPageId || !$textPageId) {
    exit("Missing page (visual={$visualPageId}, text={$textPageId})\n");
}

echo "Classify Admiralteyskaya visual item_subtab from text menu\n";
echo str_repeat('=', 60) . "\n";
echo $apply ? "Mode: APPLY\n" : "Mode: dry-run (add &confirm=apply to write)\n";
echo $force ? "Force overwrite: yes\n" : "Force overwrite: no (only empty visual subtabs)\n";
echo "visual_page_id={$visualPageId} text_page_id={$textPageId}\n";

$kitchenText = gl_subtab_load_text(
    $db,
    $dataText,
    $fields,
    $textTpl,
    $textPageId,
    'rep_kitchen_v2',
    'kit_name',
    'kitchen_subtab',
    'kit_price',
    $kitchenSlugs
);
$barText = array_merge(
    gl_subtab_load_text(
        $db,
        $dataText,
        $fields,
        $textTpl,
        $textPageId,
        'rep_bar_alc_v2',
        'i_name',
        'bar_alc_subtab',
        'i_price',
        $barSlugs
    ),
    gl_subtab_load_text(
        $db,
        $dataText,
        $fields,
        $textTpl,
        $textPageId,
        'rep_bar_non_v2',
        'i_name',
        'drinks_subtab',
        'i_price',
        $barSlugs
    )
);

$jobs = array(
    'kitchen' => array(
        'visual' => 'menu_kitchen',
        'text_items' => $kitchenText,
        'allowed' => $kitchenSlugs,
    ),
    'bar' => array(
        'visual' => 'menu_bar',
        'text_items' => $barText,
        'allowed' => $barSlugs,
    ),
);

$totals = array(
    'updated' => 0,
    'matched' => 0,
    'unchanged' => 0,
    'skipped_filled' => 0,
    'no_match' => 0,
);

foreach ($jobs as $section => $job) {
    $fieldId = gl_sync_get_repeatable_field_id($db, $fields, $visualTpl, $job['visual']);
    if (!$fieldId) {
        echo "\n{$section}: missing visual field {$job['visual']}\n";
        continue;
    }

    list($format, $visualRows) = gl_sync_read_repeatable_rows($db, $dataText, $visualPageId, $fieldId);
    list($matched, $updated, $unchanged, $skippedFilled, $noMatch, $examples) = gl_subtab_apply_section(
        $visualRows,
        $job['text_items'],
        $job['allowed'],
        $force
    );

    echo "\n{$section} ({$job['visual']}):\n";
    echo "  text_items=" . count($job['text_items'])
        . ", visual_items=" . count($visualRows)
        . ", matched={$matched}, would_update={$updated}, unchanged={$unchanged}"
        . ", skipped_filled={$skippedFilled}, no_match={$noMatch}\n";
    if ($examples) {
        echo "  examples:\n";
        foreach ($examples as $example) {
            echo "    - {$example}\n";
        }
    }

    if ($apply && $updated > 0) {
        gl_sync_write_repeatable_rows($db, $dataText, $visualPageId, $fieldId, $visualRows, $format);
        echo "  saved {$updated} update(s)\n";
    }

    $totals['updated'] += $updated;
    $totals['matched'] += $matched;
    $totals['unchanged'] += $unchanged;
    $totals['skipped_filled'] += $skippedFilled;
    $totals['no_match'] += $noMatch;
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "Done. matched={$totals['matched']}, "
    . ($apply ? 'updated' : 'would_update') . "={$totals['updated']}, "
    . "unchanged={$totals['unchanged']}, skipped_filled={$totals['skipped_filled']}, no_match={$totals['no_match']}\n";
if (!$apply && $totals['updated'] > 0) {
    echo "Re-run with &confirm=apply to write item_subtab. Lists are not moved or deleted.\n";
}
