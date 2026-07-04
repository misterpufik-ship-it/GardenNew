<?php
/**
 * Sync social link values in CouchCMS globals + header templates.
 * ?key=<md5('garden-lounge-sync-social-links')>
 */
$expectedKey = md5('garden-lounge-sync-social-links');
if ((isset($_GET['key']) ? $_GET['key'] : '') !== $expectedKey) {
    http_response_code(403);
    exit("Forbidden\n");
}
header('Content-Type: text/plain; charset=utf-8');

$updates = array(
    'link_instagram' => 'https://www.instagram.com/garden_lounge_spb/',
    'link_youtube' => 'https://www.youtube.com/@garden.lounge',
    'link_vk' => 'https://vk.com/loungegarden',
);

$templates = array(
    'globals.php',
    'udelnaya/globals.php',
    'header.php',
    'udelnaya/header.php',
    'home.php',
);

$headerFieldMap = array(
    'link_instagram' => 'link_inst',
    'link_youtube' => 'link_yt',
    'link_vk' => 'link_vk',
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
$db->set_charset('utf8');

$prefix = K_DB_TABLES_PREFIX;

function gl_sync_field($db, $prefix, $templateName, $fieldName, $value)
{
    $tplEsc = $db->real_escape_string($templateName);
    $fieldEsc = $db->real_escape_string($fieldName);
    $valueEsc = $db->real_escape_string($value);

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

    $pageRes = $db->query(
        "SELECT id FROM {$prefix}couch_pages WHERE template_id={$templateId} ORDER BY is_master DESC, id ASC LIMIT 1"
    );
    if (!$pageRes || !($pageRow = $pageRes->fetch_assoc())) {
        echo "Skip page (missing): {$templateName}\n";
        return;
    }
    $pageId = (int)$pageRow['id'];

    $dataRes = $db->query(
        "SELECT id, value FROM {$prefix}couch_data_text WHERE page_id={$pageId} AND field_id={$fieldId} LIMIT 1"
    );
    if ($dataRes && ($dataRow = $dataRes->fetch_assoc())) {
        $old = (string)$dataRow['value'];
        $db->query("UPDATE {$prefix}couch_data_text SET value='{$valueEsc}' WHERE id=" . (int)$dataRow['id'] . " LIMIT 1");
        echo "Updated {$templateName}::{$fieldName} (was: {$old})\n";
        return;
    }

    $db->query(
        "INSERT INTO {$prefix}couch_data_text (page_id, field_id, value) VALUES ({$pageId}, {$fieldId}, '{$valueEsc}')"
    );
    echo "Inserted {$templateName}::{$fieldName}\n";
}

foreach ($templates as $templateName) {
    $isHeader = (strpos($templateName, 'header.php') !== false);
    foreach ($updates as $fieldName => $value) {
        $targetField = $fieldName;
        if ($isHeader) {
            if (!isset($headerFieldMap[$fieldName])) {
                continue;
            }
            $targetField = $headerFieldMap[$fieldName];
        } elseif ($templateName === 'home.php') {
            if ($fieldName === 'link_instagram') {
                $targetField = 'home_instagram';
            } elseif ($fieldName === 'link_youtube') {
                $targetField = 'home_youtube';
            } elseif ($fieldName === 'link_vk') {
                $targetField = 'home_vk';
            } else {
                continue;
            }
        }
        gl_sync_field($db, $prefix, $templateName, $targetField, $value);
    }
}

echo "Done.\n";
