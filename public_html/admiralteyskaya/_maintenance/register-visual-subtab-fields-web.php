<?php
/**
 * Register additive visual-menu nested fields (item_type, item_subtab)
 * so Couch show_repeatable exposes them on the frontend.
 *
 * ?token=gl-cache-clear-20260623
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit("Forbidden\n");
}

header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

define('GL_SYNC_LIB_ONLY', true);
require_once __DIR__ . '/sync-visual-menu-descriptions-web.php';

function gl_reg_qval($db, $value)
{
    if ($value === null) {
        return 'NULL';
    }
    return "'" . $db->real_escape_string((string) $value) . "'";
}

function gl_reg_clone_child($db, $fieldsTable, $parentId, $sample, $name, $label, $optValues)
{
    $parentId = (int) $parentId;
    $exists = gl_sync_q1(
        $db,
        "SELECT id FROM `{$fieldsTable}` WHERE parent_id={$parentId} AND name='" . $db->real_escape_string($name) . "' LIMIT 1"
    );
    if ($exists) {
        echo "  exists {$name} (#{$exists['id']})\n";
        return 0;
    }

    unset($sample['id']);
    $sample['parent_id'] = (string) $parentId;
    $sample['name'] = $name;
    $sample['label'] = $label;
    $sample['k_type'] = 'dropdown';
    if (array_key_exists('opt_values', $sample)) {
        $sample['opt_values'] = $optValues;
    }
    foreach (array('required', 'hidden', 'deleted', 'searchable') as $flag) {
        if (array_key_exists($flag, $sample)) {
            $sample[$flag] = '0';
        }
    }
    if (array_key_exists('data', $sample)) {
        $sample['data'] = '';
    }
    if (array_key_exists('default_data', $sample)) {
        $sample['default_data'] = '';
    }
    if (array_key_exists('_html', $sample)) {
        $sample['_html'] = '';
    }
    if (array_key_exists('k_order', $sample)) {
        $sample['k_order'] = (string) ((int) $sample['k_order'] + 1);
    }

    $cols = array_keys($sample);
    $vals = array();
    foreach ($sample as $v) {
        $vals[] = gl_reg_qval($db, $v);
    }
    $sql = "INSERT INTO `{$fieldsTable}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $vals) . ")";
    if (!$db->query($sql)) {
        echo "  FAIL {$name}: {$db->error}\n";
        return 0;
    }
    echo "  added {$name} (#{$db->insert_id})\n";
    return 1;
}

$kitchenOpts = "Не задано= | Закуски=snacks | Салаты=salads | Роллы=rolls | Супы=soups | Поке / Боул / Wok=poke_bowl_wok | Горячее=hot | Десерты=desserts | Другое=other";
$barOpts = "Не задано= | Пиво=beer | Вино=wine | Коктейли=cocktails | Крепкий алкоголь=spirits | Чай / Кофе=tea_coffee | Лемонады=lemonades | Другое=other";
$typeOpts = "Товар=item | Заголовок=header";

$jobs = array(
    array(
        'tpl' => 'menu/visual/index.php',
        'repeatables' => array(
            'menu_shisha' => array('sub' => ''),
            'menu_kitchen' => array('sub' => $kitchenOpts),
            'menu_bar' => array('sub' => $barOpts),
        ),
    ),
    array(
        'tpl' => 'udelnaya/menu/visual/index.php',
        'repeatables' => array(
            'menu_shisha' => array('sub' => ''),
            'menu_kitchen' => array('sub' => $kitchenOpts),
            'menu_bar' => array('sub' => $barOpts),
        ),
    ),
);

$added = 0;
foreach ($jobs as $job) {
    echo "Template {$job['tpl']}\n";
    foreach ($job['repeatables'] as $repName => $cfg) {
        $parentId = gl_sync_get_repeatable_field_id($db, $fields, $job['tpl'], $repName);
        if (!$parentId) {
            echo "  missing repeatable {$repName}\n";
            continue;
        }
        echo "  {$repName} parent={$parentId}\n";
        $sample = gl_sync_q1(
            $db,
            "SELECT * FROM `{$fields}` WHERE parent_id={$parentId} AND name='item_tag' LIMIT 1"
        );
        if (!$sample) {
            $sample = gl_sync_q1(
                $db,
                "SELECT * FROM `{$fields}` WHERE parent_id={$parentId} AND k_type='dropdown' LIMIT 1"
            );
        }
        if (!$sample) {
            $sample = gl_sync_q1(
                $db,
                "SELECT * FROM `{$fields}` WHERE parent_id={$parentId} LIMIT 1"
            );
        }
        if (!$sample) {
            echo "  no sample child for {$repName}\n";
            continue;
        }

        $added += gl_reg_clone_child($db, $fields, $parentId, $sample, 'item_type', 'Тип', $typeOpts);
        if ($cfg['sub'] !== '') {
            $added += gl_reg_clone_child($db, $fields, $parentId, $sample, 'item_subtab', 'Категория', $cfg['sub']);
        }
    }
}

echo "\nAdded {$added} field(s).\n";
echo "Re-open visual menu pages after cache clear.\n";
