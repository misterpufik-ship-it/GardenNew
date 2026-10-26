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
$barHeaderMap = array(
    'signature coctails' => 'cocktails',
    'classic coctails' => 'cocktails',
    'shot line' => 'cocktails',
    'shot drink' => 'cocktails',
    'non-alcohol coctail' => 'cocktails',
    'homemade lemonade 400ml/1000ml' => 'lemonades',
    'lemonade/juice' => 'other',
    'still/sparkling water' => 'other',
    'beer' => 'beer',
    'draft beer' => 'beer',
    'honey mead' => 'beer',
    'vodka' => 'spirits',
    'rum' => 'spirits',
    'whiskey/burbon' => 'spirits',
    'gin' => 'spirits',
    'brandy / cognac' => 'spirits',
    'tequila / mescal' => 'spirits',
    'sparkling wine' => 'wine',
    'red wine' => 'wine',
    'white wine' => 'wine',
);
$barItemOverride = array(
    'royal gold' => 'spirits',
    'lemon pie' => 'cocktails',
    'лесные ягоды' => 'tea_coffee',
    'молочный улун & белый шоколад' => 'tea_coffee',
    'молочный чай с пряностями' => 'tea_coffee',
    'облепиха&имбирь' => 'tea_coffee',
    'облепиха & имбирь' => 'tea_coffee',
    'пуэр на вишневом соке' => 'tea_coffee',
    'травяной чай' => 'tea_coffee',
    'мятная малина' => 'tea_coffee',
);
$kitchenItemOverride = array(
    'говядина сырокопченая' => 'snacks',
    'курица сырокопченая' => 'snacks',
    'арахис копченый' => 'snacks',
    'фисташки' => 'snacks',
    'крем-суп из лосося' => 'soups',
    'крем суп из лосося' => 'soups',
    'крем-суп с лососем, брокколи и маслом' => 'soups',
    'крем суп с лососем, брокколи и маслом' => 'soups',
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

function gl_subtab_apply_section(&$visualRows, $textItems, $allowed, $force, $headerMap = array(), $itemOverride = array())
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

        $norm = gl_sync_norm_name($name);
        $current = trim(gl_sync_row_value($row, 'item_subtab'));
        $currentType = trim(gl_sync_row_value($row, 'item_type'));

        if (!empty($headerMap[$norm])) {
            $newSub = $headerMap[$norm];
            $changed = false;
            if ($currentType !== 'header') {
                gl_sync_set_row_value($visualRows[$idx], 'item_type', 'header');
                $changed = true;
            }
            if ($current !== $newSub) {
                gl_sync_set_row_value($visualRows[$idx], 'item_subtab', $newSub);
                $changed = true;
            }
            $matched++;
            if ($changed) {
                $updated++;
                if (count($examples) < 12) {
                    $examples[] = gl_sync_prepare_name($name) . " => header/{$newSub}";
                }
            } else {
                $unchanged++;
            }
            continue;
        }

        if ($currentType === 'header') {
            continue;
        }

        if (!empty($itemOverride[$norm])) {
            $newSub = $itemOverride[$norm];
            $matched++;
            if ($current === $newSub) {
                $unchanged++;
            } else {
                gl_sync_set_row_value($visualRows[$idx], 'item_subtab', $newSub);
                $updated++;
                if (count($examples) < 12) {
                    $from = $current === '' ? '(empty)' : $current;
                    $examples[] = gl_sync_prepare_name($name) . " {$from} => {$newSub} (override)";
                }
            }
            continue;
        }

        $loose = gl_sync_norm_loose($name);
        if (
            mb_strpos($loose, 'крем') !== false
            && mb_strpos($loose, 'суп') !== false
            && mb_strpos($loose, 'лосос') !== false
        ) {
            $newSub = 'soups';
            $matched++;
            if ($current === $newSub) {
                $unchanged++;
            } else {
                gl_sync_set_row_value($visualRows[$idx], 'item_subtab', $newSub);
                $updated++;
                if (count($examples) < 12) {
                    $from = $current === '' ? '(empty)' : $current;
                    $examples[] = gl_sync_prepare_name($name) . " {$from} => {$newSub} (soup heuristic)";
                }
            }
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
        $force,
        $section === 'bar' ? $barHeaderMap : array(),
        $section === 'bar' ? $barItemOverride : $kitchenItemOverride
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
