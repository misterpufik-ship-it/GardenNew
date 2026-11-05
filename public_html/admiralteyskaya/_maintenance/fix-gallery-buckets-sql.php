<?php
/**
 * Re-bucket gallery photos by filename heuristics (menu-visual, vibe-*, etc.).
 */
if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}

$config = __DIR__ . '/../couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;

$host = K_DB_HOST;
$port = 3306;
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
}
$db = new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int) $port);
$db->set_charset('utf8');

$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$text = K_DB_TABLES_PREFIX . 'couch_data_text';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';
$templates = K_DB_TABLES_PREFIX . 'couch_templates';

function decode_rows($raw)
{
    $data = @unserialize((string) $raw);
    return is_array($data) ? $data : array();
}

function couch_img_path($token)
{
    $token = trim((string) $token);
    if ($token === '') {
        return '';
    }
    if ($token[0] === ':' || strpos($token, '/') !== false || strpos($token, '.webp') !== false) {
        return strtolower($token);
    }
    $decoded = base64_decode($token, true);
    if (is_string($decoded) && $decoded !== '') {
        return strtolower($decoded);
    }
    return strtolower($token);
}

function img_key($token)
{
    $path = couch_img_path($token);
    return strtolower(basename(str_replace('\\', '/', $path)));
}

function clean_row($token)
{
    return array(
        'gallery_img' => $token,
        'gallery_img_title' => '',
        'gallery_img_alt' => '',
    );
}

function bucket_for($token, $templateName)
{
    $path = couch_img_path($token);
    if (strpos($path, 'menu-visual') !== false) {
        return 'menu';
    }
    if (preg_match('~(?:^|[:/])vibe-(adm|udel)-~', $path)) {
        return 'vibe';
    }
    if (preg_match('~(?:^|[:/])gf\d+\.webp~', $path)) {
        return 'vibe';
    }
    if (strpos($path, 'safonovleonid') !== false) {
        return 'vibe';
    }
    if (strpos($templateName, 'udelnaya') !== false && preg_match('~(?:^|[:/])garden\.webp~', $path)) {
        return 'vibe';
    }
    if (strpos($path, 'chatgpt-image') !== false && strpos($templateName, 'udelnaya') !== false) {
        return 'vibe';
    }
    return 'interior';
}

function merge_unique($rows)
{
    $out = array();
    $seen = array();
    foreach ($rows as $row) {
        if (!is_array($row) || empty($row['gallery_img'])) {
            continue;
        }
        $key = img_key($row['gallery_img']);
        if ($key === '' || isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;
        $out[] = clean_row($row['gallery_img']);
    }
    return $out;
}

foreach (array('gallery.php', 'udelnaya/gallery.php') as $tn) {
    echo "\n=== {$tn} ===\n";
    $tid = (int) $db->query("SELECT id FROM {$templates} WHERE name='" . $db->real_escape_string($tn) . "' LIMIT 1")->fetch_assoc()['id'];
    $pid = (int) $db->query("SELECT id FROM {$pages} WHERE template_id={$tid} LIMIT 1")->fetch_assoc()['id'];

    $all = array();
    foreach (array('gallery_interior_items', 'gallery_menu_items', 'gallery_vibe_items') as $fn) {
        $fid = (int) $db->query("SELECT id FROM {$fields} WHERE template_id={$tid} AND name='" . $db->real_escape_string($fn) . "' LIMIT 1")->fetch_assoc()['id'];
        $val = $db->query("SELECT value FROM {$text} WHERE page_id={$pid} AND field_id={$fid} LIMIT 1")->fetch_assoc();
        foreach (decode_rows($val ? $val['value'] : '') as $row) {
            if (!is_array($row) || empty($row['gallery_img'])) {
                continue;
            }
            $all[] = $row['gallery_img'];
        }
    }

    $buckets = array('interior' => array(), 'menu' => array(), 'vibe' => array());
    foreach ($all as $token) {
        $bucket = bucket_for($token, $tn);
        $buckets[$bucket][] = clean_row($token);
    }

    $map = array(
        'interior' => 'gallery_interior_items',
        'menu' => 'gallery_menu_items',
        'vibe' => 'gallery_vibe_items',
    );

    foreach ($map as $bucket => $fn) {
        $rows = merge_unique($buckets[$bucket]);
        $fid = (int) $db->query("SELECT id FROM {$fields} WHERE template_id={$tid} AND name='" . $db->real_escape_string($fn) . "' LIMIT 1")->fetch_assoc()['id'];
        $db->query(
            "UPDATE {$text} SET value='" . $db->real_escape_string(serialize($rows)) .
            "' WHERE page_id={$pid} AND field_id={$fid} LIMIT 1"
        );
        echo "{$fn}: " . count($rows) . " photos\n";
    }
}

if (!defined('GL_SKIP_CLI_CHECK')) {
    require __DIR__ . '/clear-couch-cache-cli.php';
}
