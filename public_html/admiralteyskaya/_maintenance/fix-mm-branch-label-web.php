<?php
/**
 * Normalize mobile menu branch label capitalization in CMS DB.
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/fix-mm-branch-label-web.php?key=<md5>
 * key = md5('garden-lounge-fix-mm-branch-label')
 */
$expectedKey = md5('garden-lounge-fix-mm-branch-label');
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

$newLabel = 'Второй филиал';
$oldLabels = array('ВТОРОЙ ФИЛИАЛ', 'Второй Филиал', 'ВТОРОЙ филиал');
$updated = 0;

foreach (array('mm_adm_branch_label', 'mm_udel_branch_label') as $fieldName) {
    $fieldId = gl_get_field_id($db, $fields, $templateId, $fieldName);
    if (!$fieldId) {
        echo "Field {$fieldName} not found\n";
        continue;
    }
    $current = trim(gl_get_field_value($db, $dataText, $pageId, $fieldId));
    if ($current === $newLabel) {
        echo "{$fieldName}: already {$newLabel}\n";
        continue;
    }
    if ($current !== '' && !in_array($current, $oldLabels, true)) {
        echo "{$fieldName}: kept custom value '{$current}'\n";
        continue;
    }
    if (gl_upsert_field_value($db, $dataText, $pageId, $fieldId, $newLabel)) {
        echo "{$fieldName}: updated to {$newLabel}\n";
        $updated++;
    } else {
        echo "{$fieldName}: update failed ({$db->error})\n";
    }
}

echo "Done. Updated {$updated} field(s).\n";
