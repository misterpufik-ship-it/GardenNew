<?php
/**
 * Add item_type / item_subtab into Couch repeatable schema (custom_params)
 * so the frontend show_repeatable exposes them.
 *
 * ?token=gl-cache-clear-20260623
 * &confirm=apply to write
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

define('GL_SYNC_LIB_ONLY', true);
require_once __DIR__ . '/sync-visual-menu-descriptions-web.php';

function gl_schema_decode($raw)
{
    $params = @unserialize($raw);
    if (!is_array($params) || empty($params['schema'])) {
        return array(null, null);
    }
    $schemaRaw = $params['schema'];
    $schema = @unserialize($schemaRaw);
    if (!is_array($schema)) {
        $decoded = base64_decode($schemaRaw, true);
        $schema = $decoded ? @unserialize($decoded) : false;
    }
    if (!is_array($schema)) {
        return array($params, null);
    }
    return array($params, $schema);
}

function gl_schema_field_name($field)
{
    if (!is_array($field) || !isset($field['name'])) {
        return '';
    }
    $name = $field['name'];
    $plain = gl_sync_decode_field_value($name);
    return $plain !== '' ? $plain : (string) $name;
}

function gl_schema_clone($sample, $name, $label, $optValues)
{
    $field = $sample;
    $encode = function ($v) use ($sample) {
        $orig = isset($sample['name']) ? (string) $sample['name'] : '';
        if ($orig !== '' && $orig === base64_encode(base64_decode($orig, true) ?: '')) {
            return base64_encode((string) $v);
        }
        return $v;
    };
    $field['name'] = $encode($name);
    $field['label'] = $encode($label);
    $field['k_type'] = $encode('dropdown');
    $field['opt_values'] = $optValues;
    $field['required'] = isset($field['required']) ? $encode('0') : '0';
    $field['hidden'] = isset($field['hidden']) ? $encode('0') : '0';
    return $field;
}

$kitchenOpts = 'Не задано= | Закуски=snacks | Салаты=salads | Роллы=rolls | Супы=soups | Поке / Боул / Wok=poke_bowl_wok | Горячее=hot | Десерты=desserts | Другое=other';
$barOpts = 'Не задано= | Пиво=beer | Вино=wine | Коктейли=cocktails | Крепкий алкоголь=spirits | Чай / Кофе=tea_coffee | Лемонады=lemonades | Другое=other';
$typeOpts = 'Товар=item | Заголовок=header';

$targets = array(
    array('menu/visual/index.php', 'menu_shisha', array('type')),
    array('menu/visual/index.php', 'menu_kitchen', array('type', 'kitchen')),
    array('menu/visual/index.php', 'menu_bar', array('type', 'bar')),
    array('udelnaya/menu/visual/index.php', 'menu_shisha', array('type')),
    array('udelnaya/menu/visual/index.php', 'menu_kitchen', array('type', 'kitchen')),
    array('udelnaya/menu/visual/index.php', 'menu_bar', array('type', 'bar')),
);

echo $apply ? "Mode: APPLY\n" : "Mode: dry-run\n";

foreach ($targets as $target) {
    list($tpl, $rep, $need) = $target;
    $fieldId = gl_sync_get_repeatable_field_id($db, $fields, $tpl, $rep);
    echo "\n{$tpl} {$rep} id={$fieldId}\n";
    if (!$fieldId) {
        echo "  missing\n";
        continue;
    }
    $row = gl_sync_q1($db, "SELECT custom_params FROM `{$fields}` WHERE id={$fieldId} LIMIT 1");
    list($params, $schema) = gl_schema_decode($row ? $row['custom_params'] : '');
    if (!is_array($schema)) {
        echo "  cannot decode schema\n";
        continue;
    }

    $names = array();
    $tagSample = null;
    foreach ($schema as $field) {
        $fname = gl_schema_field_name($field);
        $names[] = $fname;
        if ($fname === 'item_tag') {
            $tagSample = $field;
        }
    }
    echo "  current: " . implode(', ', $names) . "\n";
    if (!$tagSample) {
        $tagSample = $schema[0];
    }

    $changed = false;
    if (in_array('type', $need, true) && !in_array('item_type', $names, true)) {
        array_splice($schema, 1, 0, array(gl_schema_clone($tagSample, 'item_type', 'Тип', $typeOpts)));
        $changed = true;
        echo "  + item_type\n";
    }
    if (in_array('kitchen', $need, true) && !in_array('item_subtab', $names, true)) {
        array_splice($schema, 2, 0, array(gl_schema_clone($tagSample, 'item_subtab', 'Категория', $kitchenOpts)));
        $changed = true;
        echo "  + item_subtab (kitchen)\n";
    }
    if (in_array('bar', $need, true) && !in_array('item_subtab', $names, true)) {
        array_splice($schema, 2, 0, array(gl_schema_clone($tagSample, 'item_subtab', 'Категория', $barOpts)));
        $changed = true;
        echo "  + item_subtab (bar)\n";
    }

    if (!$changed) {
        echo "  unchanged\n";
        continue;
    }

    $schemaRaw = serialize($schema);
    if (isset($params['schema']) && $params['schema'] !== $schemaRaw && base64_decode((string) $params['schema'], true)) {
        // keep original encoding style if it was base64-wrapped serialize
        $origDecoded = @unserialize(base64_decode($params['schema'], true));
        if (is_array($origDecoded)) {
            $schemaRaw = base64_encode(serialize($schema));
        }
    }
    $params['schema'] = $schemaRaw;
    $encoded = serialize($params);
    echo "  new_schema_fields=" . count($schema) . " params_len=" . strlen($encoded) . "\n";

    if ($apply) {
        $esc = $db->real_escape_string($encoded);
        $db->query("UPDATE `{$fields}` SET custom_params='{$esc}' WHERE id={$fieldId} LIMIT 1");
        echo "  saved\n";
    }
}

echo "\nDone.\n";
if (!$apply) {
    echo "Re-run with &confirm=apply to write schema.\n";
}
