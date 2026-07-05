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

$root = realpath(__DIR__ . '/..');
require_once $root . '/menu/text/menu-copy-lib.php';

chdir($root);
require_once $root . '/couch/cms.php';

global $AUTH, $FUNCS;

if (!isset($AUTH->user) || !is_object($AUTH->user)) {
    exit("Couch auth not initialized\n");
}

$AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;

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

function gl_sync_norm_name($name)
{
    $name = trim((string) $name);
    $name = preg_replace('/\s+/u', ' ', $name);
    $name = mb_strtolower($name, 'UTF-8');
    $name = str_replace(array("\xe2\x80\x99", "\xe2\x80\x98", '`', '´'), "'", $name);

    return $name;
}

function gl_sync_build_desc_map($rows, $nameField, $descField)
{
    $map = array();

    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }

        $rowType = isset($row['row_type']) ? trim((string) $row['row_type']) : 'item';
        if ($rowType !== '' && $rowType !== 'item') {
            continue;
        }

        $name = isset($row[$nameField]) ? trim((string) $row[$nameField]) : '';
        $desc = isset($row[$descField]) ? trim((string) $row[$descField]) : '';
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

        $name = isset($row[$nameField]) ? trim((string) $row[$nameField]) : '';
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
        $oldDesc = isset($row[$descField]) ? trim((string) $row[$descField]) : '';

        if ($oldDesc === $newDesc) {
            $unchanged++;
            continue;
        }

        $visualRows[$idx][$descField] = $newDesc;
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

echo "Sync visual menu descriptions from text menu\n";
echo str_repeat('=', 60) . "\n";

foreach ($branches as $branch => $templates) {
    echo "\nBranch: {$branch}\n";

    $textPg = new KWebpage($templates['text']);
    if (!empty($textPg->error)) {
        echo "  ERROR loading text menu: {$textPg->err_msg}\n";
        continue;
    }

    $visualPg = new KWebpage($templates['visual']);
    if (!empty($visualPg->error)) {
        echo "  ERROR loading visual menu: {$visualPg->err_msg}\n";
        continue;
    }

    $branchUpdated = 0;

    foreach ($sections as $section => $cfg) {
        $textField = garden_menu_copy_get_page_field($textPg, $cfg['text_repeatable']);
        $visualField = garden_menu_copy_get_page_field($visualPg, $cfg['visual_repeatable']);

        if (!$textField) {
            echo "  {$section}: text repeatable missing ({$cfg['text_repeatable']})\n";
            continue;
        }
        if (!$visualField) {
            echo "  {$section}: visual repeatable missing ({$cfg['visual_repeatable']})\n";
            continue;
        }

        $textRows = garden_menu_copy_read_rows($textField);
        $visualRows = garden_menu_copy_read_rows($visualField);
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
            garden_menu_copy_save_rows($visualPg, $cfg['visual_repeatable'], $visualRows);
            $branchUpdated += $updated;
        }

        $totals['updated'] += $updated;
        $totals['matched'] += $matched;
        $totals['unchanged'] += $unchanged;
        $totals['no_match'] += $noMatch;
    }

    if ($branchUpdated > 0) {
        $errors = $visualPg->save('db_persist');
        if ($errors) {
            echo "  SAVE FAILED with {$errors} error(s)\n";
            foreach ($visualPg->fields as $field) {
                if (!empty($field->err_msg)) {
                    echo "    - {$field->name}: {$field->err_msg}\n";
                }
            }
            exit(1);
        }
        echo "  Saved {$branchUpdated} description update(s)\n";
    } else {
        echo "  No changes to save\n";
    }
}

if (isset($FUNCS) && method_exists($FUNCS, 'invalidate_cache')) {
    $FUNCS->invalidate_cache();
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "Done. Total updated={$totals['updated']}, matched={$totals['matched']}, unchanged={$totals['unchanged']}, no_match={$totals['no_match']}\n";
