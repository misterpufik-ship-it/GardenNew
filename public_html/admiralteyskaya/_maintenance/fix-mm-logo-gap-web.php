<?php
/**
 * Force logo→menu gap to legacy px value (or 32px default) in CouchCMS DB.
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/fix-mm-logo-gap-web.php?key=<md5>
 * key = md5('garden-lounge-fix-mm-logo-gap')
 */
$expectedKey = md5('garden-lounge-fix-mm-logo-gap');
if ((isset($_GET['key']) ? $_GET['key'] : '') !== $expectedKey) {
    http_response_code(403);
    exit("Forbidden\n");
}
header('Content-Type: text/plain; charset=utf-8');

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
$db = @new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int) $port);
if ($db->connect_errno) {
    exit("DB connection failed: {$db->connect_error}\n");
}
$db->set_charset('utf8');

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';
$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$dataText = K_DB_TABLES_PREFIX . 'couch_data_text';

function gl_qval($db, $value)
{
    return "'" . $db->real_escape_string((string) $value) . "'";
}

function gl_fetch_one($db, $sql)
{
    $res = $db->query($sql);
    return $res ? $res->fetch_assoc() : null;
}

function gl_get_field_id($db, $fields, $templateId, $name)
{
    $row = gl_fetch_one(
        $db,
        "SELECT id FROM `{$fields}` WHERE template_id={$templateId} AND name='" . $db->real_escape_string($name) . "' LIMIT 1"
    );
    return $row ? (int) $row['id'] : 0;
}

function gl_get_field_value($db, $dataText, $pageId, $fieldId)
{
    $row = gl_fetch_one(
        $db,
        "SELECT value FROM `{$dataText}` WHERE page_id={$pageId} AND field_id={$fieldId} LIMIT 1"
    );
    return $row ? (string) $row['value'] : '';
}

function gl_upsert_field_value($db, $dataText, $pageId, $fieldId, $value)
{
    $row = gl_fetch_one($db, "SELECT id FROM `{$dataText}` WHERE page_id={$pageId} AND field_id={$fieldId} LIMIT 1");
    if ($row) {
        return (bool) $db->query("UPDATE `{$dataText}` SET value=" . gl_qval($db, $value) . " WHERE id=" . (int) $row['id'] . " LIMIT 1");
    }
    return (bool) $db->query("INSERT INTO `{$dataText}` (`page_id`,`field_id`,`value`) VALUES ({$pageId},{$fieldId}," . gl_qval($db, $value) . ")");
}

$template = gl_fetch_one($db, "SELECT id FROM `{$templates}` WHERE name='layout-mobile-menu.php' LIMIT 1");
if (!$template) {
    exit("Template layout-mobile-menu.php not found\n");
}
$templateId = (int) $template['id'];
$page = gl_fetch_one($db, "SELECT id FROM `{$pages}` WHERE template_id={$templateId} AND is_master='1' LIMIT 1");
if (!$page) {
    exit("Master page not found\n");
}
$pageId = (int) $page['id'];

$updated = 0;
foreach (array('mm_adm_', 'mm_udel_') as $prefix) {
    $legacyId = gl_get_field_id($db, $fields, $templateId, $prefix . 'logo_menu_gap');
    $legacy = $legacyId ? (int) gl_get_field_value($db, $dataText, $pageId, $legacyId) : 0;
    $px = $legacy > 0 ? $legacy : 32;
    $vh = round($px / 8, 1);

    $minId = gl_get_field_id($db, $fields, $templateId, $prefix . 'logo_gap_min');
    $vhId = gl_get_field_id($db, $fields, $templateId, $prefix . 'logo_gap_vh');
    $maxId = gl_get_field_id($db, $fields, $templateId, $prefix . 'logo_gap_max');
    if (!$minId || !$vhId || !$maxId) {
        echo "Skip {$prefix}: clamp fields missing\n";
        continue;
    }

    gl_upsert_field_value($db, $dataText, $pageId, $minId, (string) $px);
    gl_upsert_field_value($db, $dataText, $pageId, $vhId, (string) $vh);
    gl_upsert_field_value($db, $dataText, $pageId, $maxId, (string) $px);
    $updated++;
    echo "Set {$prefix}logo_gap to clamp({$px}px, {$vh}vh, {$px}px)\n";
}

$cacheDir = $root . '/couch/cache';
$removed = 0;
if (is_dir($cacheDir)) {
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cacheDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($items as $item) {
        if ($item->isDir()) {
            continue;
        }
        if (basename($item->getPathname()) === '.htaccess') {
            continue;
        }
        if (@unlink($item->getPathname())) {
            $removed++;
        }
    }
}

echo "Done. Updated {$updated} branch(es), cleared {$removed} cache file(s).\n";
