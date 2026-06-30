<?php
/**
 * Remove legacy visual menu admin fields (desserts, logo settings group).
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/cleanup-visual-menu-admin-web.php?token=gl-cache-clear-20260623
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit('Forbidden');
}

header('Content-Type: text/plain; charset=utf-8');

$config = __DIR__ . '/../couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;

$db = @new mysqli(K_DB_HOST, K_DB_USER, K_DB_PASSWORD, K_DB_NAME);
if ($db->connect_errno) {
    exit('DB error: ' . $db->connect_error . "\n");
}
$db->set_charset('utf8');

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$text = K_DB_TABLES_PREFIX . 'couch_data_text';

$removeNames = array(
    'group_main_settings',
    'group_desserts',
    'menu_desserts',
);

function delete_field_by_id($db, $fields, $text, $fieldId)
{
    $fieldId = (int) $fieldId;
    $res = $db->query("SELECT id, name, k_type FROM `{$fields}` WHERE k_group=(SELECT name FROM `{$fields}` WHERE id={$fieldId} LIMIT 1) AND template_id=(SELECT template_id FROM `{$fields}` WHERE id={$fieldId} LIMIT 1)");
    if ($res) {
        while ($child = $res->fetch_assoc()) {
            delete_field_by_id($db, $fields, $text, (int) $child['id']);
        }
    }
    $db->query("DELETE FROM `{$text}` WHERE field_id={$fieldId}");
    $db->query("DELETE FROM `{$fields}` WHERE id={$fieldId}");
}

$templateNames = array('menu/visual/index.php', 'udelnaya/menu/visual/index.php');
$deleted = 0;

foreach ($templateNames as $tplName) {
    $tplNameEsc = $db->real_escape_string($tplName);
    $tplRes = $db->query("SELECT id FROM `{$templates}` WHERE name='{$tplNameEsc}' LIMIT 1");
    if (!$tplRes || !($tpl = $tplRes->fetch_assoc())) {
        echo "Template not found: {$tplName}\n";
        continue;
    }
    $tplId = (int) $tpl['id'];
    echo "Template {$tplName} (#{$tplId})\n";

    foreach ($removeNames as $fieldName) {
        $fieldNameEsc = $db->real_escape_string($fieldName);
        $fieldRes = $db->query("SELECT id FROM `{$fields}` WHERE template_id={$tplId} AND name='{$fieldNameEsc}' LIMIT 1");
        if ($fieldRes && ($field = $fieldRes->fetch_assoc())) {
            delete_field_by_id($db, $fields, $text, (int) $field['id']);
            echo "  removed field {$fieldName}\n";
            $deleted++;
        }
    }
}

echo "Done. Removed {$deleted} legacy field trees.\n";
