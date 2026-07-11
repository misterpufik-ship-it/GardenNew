<?php
/**
 * Find and fix CMS menu link fields that contain server filesystem paths.
 * ?token=gl-fix-menu-links-20260705
 */
$expectedToken = 'gl-fix-menu-links-20260705';
if ((isset($_GET['token']) ? $_GET['token'] : '') !== $expectedToken) {
    http_response_code(403);
    exit("Forbidden\n");
}
header('Content-Type: text/plain; charset=utf-8');

$fixes = array(
    'menu.php' => array(
        'menu_visual_link' => 'https://garden-lounge.pro/admiralteyskaya/menu/visual',
        'menu_text_link' => 'https://garden-lounge.pro/admiralteyskaya/menu/text',
        'menu_eng_link' => 'https://garden-lounge.pro/admiralteyskaya/menu/english',
    ),
    'udelnaya/menu.php' => array(
        'menu_visual_link' => 'https://garden-lounge.pro/udelnaya/menu/visual',
        'menu_text_link' => 'https://garden-lounge.pro/udelnaya/menu/text',
        'menu_eng_link' => 'https://garden-lounge.pro/udelnaya/menu/english',
    ),
);

$root = realpath(__DIR__ . '/..');
$config = $root . '/couch/config.php';
if (!is_file($config)) {
    exit("CouchCMS config not found\n");
}

define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ?: 3306;
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
}

mysqli_report(MYSQLI_REPORT_OFF);
$db = @new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
if ($db->connect_errno) {
    exit("DB connection failed: {$db->connect_error}\n");
}
$db->set_charset('utf8mb4');

$prefix = K_DB_TABLES_PREFIX;

echo "Scan for broken paths in couch_data:\n";
$scan = $db->query(
    "SELECT d.id, d.page_id, d.field_id, d.value, t.name AS template_name, f.name AS field_name
     FROM {$prefix}couch_data d
     JOIN {$prefix}couch_fields f ON f.id = d.field_id
     JOIN {$prefix}couch_templates t ON t.id = f.template_id
     WHERE d.value LIKE '%/home/m/%'
        OR d.value LIKE '%public_html%'
        OR d.value LIKE '%mrpuffch%'
     LIMIT 50"
);
if ($scan) {
    while ($row = $scan->fetch_assoc()) {
        echo "- [{$row['template_name']}::{$row['field_name']}] page={$row['page_id']} value={$row['value']}\n";
    }
} else {
    echo "Scan query failed: {$db->error}\n";
}

function gl_fix_menu_field($db, $prefix, $templateName, $fieldName, $expectedValue, $dryRun)
{
    $tplEsc = $db->real_escape_string($templateName);
    $fieldEsc = $db->real_escape_string($fieldName);
    $valueEsc = $db->real_escape_string($expectedValue);

    $tpl = $db->query("SELECT id FROM {$prefix}couch_templates WHERE name='{$tplEsc}' LIMIT 1");
    if (!$tpl || !($tplRow = $tpl->fetch_assoc())) {
        echo "Skip template (missing): {$templateName}\n";
        return;
    }
    $templateId = (int)$tplRow['id'];

    $fieldRes = $db->query(
        "SELECT id FROM {$prefix}couch_fields WHERE template_id={$templateId} AND name='{$fieldEsc}' LIMIT 1"
    );
    if (!$fieldRes || !($fieldRow = $fieldRes->fetch_assoc())) {
        echo "Skip field (missing): {$templateName}::{$fieldName}\n";
        return;
    }
    $fieldId = (int)$fieldRow['id'];

    $dataRes = $db->query(
        "SELECT id, page_id, value FROM {$prefix}couch_data WHERE field_id={$fieldId} LIMIT 5"
    );
    if (!$dataRes || !$dataRes->num_rows) {
        echo "No data row for {$templateName}::{$fieldName}\n";
        return;
    }

    while ($dataRow = $dataRes->fetch_assoc()) {
        $current = $dataRow['value'];
        $needsFix = (
            strpos($current, '/home/m/') !== false
            || strpos($current, 'public_html') !== false
            || strpos($current, 'mrpuffch') !== false
        );
        if (!$needsFix) {
            echo "OK {$templateName}::{$fieldName} = {$current}\n";
            continue;
        }
        echo "FIX {$templateName}::{$fieldName}: {$current} -> {$expectedValue}\n";
        if (!$dryRun) {
            $id = (int)$dataRow['id'];
            $db->query("UPDATE {$prefix}couch_data SET value='{$valueEsc}' WHERE id={$id} LIMIT 1");
        }
    }
}

$dryRun = isset($_GET['dry']) && $_GET['dry'] === '1';
echo $dryRun ? "\nDry run only.\n" : "\nApplying fixes.\n";

foreach ($fixes as $templateName => $fields) {
    foreach ($fields as $fieldName => $expectedValue) {
        gl_fix_menu_field($db, $prefix, $templateName, $fieldName, $expectedValue, $dryRun);
    }
}

echo "\nDone.\n";
