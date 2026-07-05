<?php
/**
 * One-time restore clamp fields from legacy px values (user-tuned admin data).
 * Does NOT touch legacy rows. Safe to re-run only with force=1 after first restore.
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/restore-mm-from-legacy-web.php?key=<md5>&confirm=restore
 * key = md5('garden-lounge-restore-mm-from-legacy')
 */
$expectedKey = md5('garden-lounge-restore-mm-from-legacy');
if ((isset($_GET['key']) ? $_GET['key'] : '') !== $expectedKey) {
    http_response_code(403);
    exit("Forbidden\n");
}
if ((isset($_GET['confirm']) ? $_GET['confirm'] : '') !== 'restore') {
    exit("Add &confirm=restore to run\n");
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

$db = @new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int) $port);
if ($db->connect_errno) {
    exit("DB connection failed: {$db->connect_error}\n");
}
$db->set_charset('utf8');
mysqli_report(MYSQLI_REPORT_OFF);

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
    if (!$fieldId) {
        return '';
    }
    $row = gl_fetch_one(
        $db,
        "SELECT value FROM `{$dataText}` WHERE page_id={$pageId} AND field_id={$fieldId} LIMIT 1"
    );
    return $row ? (string) $row['value'] : '';
}

function gl_upsert_field_value($db, $dataText, $pageId, $fieldId, $value)
{
    if (!$fieldId) {
        return false;
    }
    $db->query("DELETE FROM `{$dataText}` WHERE page_id={$pageId} AND field_id={$fieldId}");
    return (bool) $db->query("INSERT INTO `{$dataText}` (`page_id`,`field_id`,`value`) VALUES ({$pageId},{$fieldId}," . gl_qval($db, $value) . ")");
}

function gl_legacy_triplet($legacyPx, $kind)
{
    $L = max(0, (int) $legacyPx);
    if ($L <= 0) {
        return null;
    }
    switch ($kind) {
        case 'shell_top':
        case 'contact_pad':
            return array((string) max(0, $L - 4), (string) round($L / 6.4, 1), (string) ($L + 12));
        case 'logo_gap':
            return array((string) $L, (string) round($L / 8, 1), (string) $L);
        case 'menu_contact':
        case 'contact_push':
            return array((string) max(0, $L - 4), (string) round($L / 10, 1), (string) ($L + 4));
        case 'social':
            return array((string) max(0, $L - 2), (string) $L, (string) ($L + 2));
        default:
            return null;
    }
}

$template = gl_fetch_one($db, "SELECT id FROM `{$templates}` WHERE name='layout-mobile-menu.php' LIMIT 1");
if (!$template) {
    exit("Template not found\n");
}
$templateId = (int) $template['id'];
$page = gl_fetch_one($db, "SELECT id FROM `{$pages}` WHERE template_id={$templateId} AND is_master='1' LIMIT 1");
if (!$page) {
    exit("Master page not found\n");
}
$pageId = (int) $page['id'];

$map = array(
    array('legacy' => 'shell_pad_top', 'key' => 'shell_top', 'kind' => 'shell_top'),
    array('legacy' => 'logo_menu_gap', 'key' => 'logo_gap', 'kind' => 'logo_gap'),
    array('legacy' => 'menu_contact_gap', 'key' => 'menu_contact', 'kind' => 'menu_contact'),
    array('legacy' => 'contact_pad_top', 'key' => 'contact_pad', 'kind' => 'contact_pad'),
    array('legacy' => 'contact_push', 'key' => 'contact_push', 'kind' => 'contact_push'),
);

$updated = 0;
foreach (array('mm_adm_', 'mm_udel_') as $prefix) {
    echo "== {$prefix} ==\n";
    foreach ($map as $item) {
        $legacyVal = gl_get_field_value(
            $db,
            $dataText,
            $pageId,
            gl_get_field_id($db, $fields, $templateId, $prefix . $item['legacy'])
        );
        $triplet = gl_legacy_triplet($legacyVal, $item['kind']);
        if (!$triplet) {
            echo "  skip {$item['key']}: legacy empty\n";
            continue;
        }
        $minId = gl_get_field_id($db, $fields, $templateId, $prefix . $item['key'] . '_min');
        $vhId = gl_get_field_id($db, $fields, $templateId, $prefix . $item['key'] . '_vh');
        $maxId = gl_get_field_id($db, $fields, $templateId, $prefix . $item['key'] . '_max');
        if (!$minId || !$vhId || !$maxId) {
            echo "  skip {$item['key']}: clamp fields missing\n";
            continue;
        }
        gl_upsert_field_value($db, $dataText, $pageId, $minId, $triplet[0]);
        gl_upsert_field_value($db, $dataText, $pageId, $vhId, $triplet[1]);
        gl_upsert_field_value($db, $dataText, $pageId, $maxId, $triplet[2]);
        echo "  {$item['key']}: legacy {$legacyVal}px -> clamp({$triplet[0]}px, {$triplet[1]}vh, {$triplet[2]}px)\n";
        $updated++;
    }

    $legacySocial = gl_get_field_value(
        $db,
        $dataText,
        $pageId,
        gl_get_field_id($db, $fields, $templateId, $prefix . 'social_gap')
    );
    $socialTriplet = gl_legacy_triplet($legacySocial, 'social');
    if ($socialTriplet) {
        $socialMinId = gl_get_field_id($db, $fields, $templateId, $prefix . 'social_gap_min');
        $socialMidId = gl_get_field_id($db, $fields, $templateId, $prefix . 'social_gap_mid');
        $socialMaxId = gl_get_field_id($db, $fields, $templateId, $prefix . 'social_gap_max');
        if ($socialMinId && $socialMidId && $socialMaxId) {
            gl_upsert_field_value($db, $dataText, $pageId, $socialMinId, $socialTriplet[0]);
            gl_upsert_field_value($db, $dataText, $pageId, $socialMidId, $socialTriplet[1]);
            gl_upsert_field_value($db, $dataText, $pageId, $socialMaxId, $socialTriplet[2]);
            echo "  social_gap: legacy {$legacySocial}px -> clamp({$socialTriplet[0]}px, {$socialTriplet[1]}px, {$socialTriplet[2]}px)\n";
            $updated++;
        }
    }
}

$cacheDir = $root . '/couch/cache';
$removed = 0;
if (is_dir($cacheDir)) {
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cacheDir, FilesystemIterator::SKIP_DOTS)) as $item) {
        if ($item->isDir() || basename($item->getPathname()) === '.htaccess') {
            continue;
        }
        if (@unlink($item->getPathname())) {
            $removed++;
        }
    }
}

echo "Done. Restored {$updated} group(s), cleared {$removed} cache file(s).\n";
