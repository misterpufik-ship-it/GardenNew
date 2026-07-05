<?php
/**
 * Sync SEO defaults in CouchCMS (globals, home, branch page meta).
 * ?key=<md5('garden-lounge-sync-seo-p0')>
 */
$expectedKey = md5('garden-lounge-sync-seo-p0');
if ((isset($_GET['key']) ? $_GET['key'] : '') !== $expectedKey) {
    http_response_code(403);
    exit("Forbidden\n");
}
header('Content-Type: text/plain; charset=utf-8');

$fieldUpdates = array(
    'globals.php' => array(
        'seo_title_default' => 'Garden Lounge на Адмиралтейской — кальянная и лаунж-бар в центре СПб',
        'seo_desc_default' => 'Garden Lounge на наб. реки Мойки 67-69: премиальные кальяны, кухня, VIP-комнаты и бронь столика рядом с метро Адмиралтейская. Тел. +7 995 624-68-08.',
    ),
    'udelnaya/globals.php' => array(
        'seo_title_default' => 'Garden Lounge на Удельной — кальянная и лаунж-бар у метро Удельная в СПб',
        'seo_desc_default' => 'Garden Lounge на ул. Аккуратова 13: премиальные кальяны, кухня, VIP-комнаты и бронь столика рядом с метро Удельная. Тел. +7 950 047-33-65.',
    ),
    'home.php' => array(
        'home_seo_desc' => 'Garden Lounge — лаунж-пространство с двумя филиалами в Санкт-Петербурге: Адмиралтейская в центре и Удельная на севере. Бронирование, меню, бар, VIP-комнаты и PS5.',
    ),
);

$pageMetaUpdates = array(
    'index.php' => array(
        'page_title' => 'Garden Lounge на Адмиралтейской',
        'page_desc' => 'Garden Lounge на наб. реки Мойки 67-69: премиальные кальяны, кухня, VIP-комнаты и бронь столика рядом с метро Адмиралтейская. Тел. +7 995 624-68-08.',
    ),
    'udelnaya/index.php' => array(
        'page_title' => 'Garden Lounge на Удельной',
        'page_desc' => 'Garden Lounge на ул. Аккуратова 13: премиальные кальяны, кухня, VIP-комнаты и бронь столика рядом с метро Удельная. Тел. +7 950 047-33-65.',
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

function gl_seo_sync_field($db, $prefix, $templateName, $fieldName, $value)
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

    $rows = $db->query(
        "SELECT dt.id, dt.page_id, dt.value FROM {$prefix}couch_data_text dt " .
        "INNER JOIN {$prefix}couch_pages p ON p.id = dt.page_id " .
        "WHERE p.template_id={$templateId} AND dt.field_id={$fieldId}"
    );
    $count = 0;
    if ($rows) {
        while ($row = $rows->fetch_assoc()) {
            $old = (string)$row['value'];
            $db->query("UPDATE {$prefix}couch_data_text SET value='{$valueEsc}' WHERE id=" . (int)$row['id'] . " LIMIT 1");
            echo "Updated {$templateName}::{$fieldName} page #{$row['page_id']}\n";
            if ($old !== $value) {
                echo "  was: {$old}\n";
            }
            $count++;
        }
    }

    if ($count === 0) {
        $pageRes = $db->query(
            "SELECT id FROM {$prefix}couch_pages WHERE template_id={$templateId} ORDER BY is_master DESC, id ASC LIMIT 1"
        );
        if (!$pageRes || !($pageRow = $pageRes->fetch_assoc())) {
            echo "Skip page (missing): {$templateName}\n";
            return;
        }
        $pageId = (int)$pageRow['id'];
        $db->query(
            "INSERT INTO {$prefix}couch_data_text (page_id, field_id, value) VALUES ({$pageId}, {$fieldId}, '{$valueEsc}')"
        );
        echo "Inserted {$templateName}::{$fieldName} on page #{$pageId}\n";
    }
}

function gl_seo_sync_page_meta($db, $prefix, $templateName, $pageTitle, $pageDesc)
{
    $tplEsc = $db->real_escape_string($templateName);
    $titleEsc = $db->real_escape_string($pageTitle);
    $descEsc = $db->real_escape_string($pageDesc);

    $tpl = $db->query("SELECT id, name, title FROM {$prefix}couch_templates WHERE name='{$tplEsc}' LIMIT 1");
    if (!$tpl || !($tplRow = $tpl->fetch_assoc())) {
        echo "Skip page meta (template missing): {$templateName}\n";
        return;
    }
    $templateId = (int)$tplRow['id'];

    $pageRes = $db->query(
        "SELECT id, page_title, page_desc FROM {$prefix}couch_pages WHERE template_id={$templateId} ORDER BY is_master DESC, id ASC LIMIT 1"
    );
    if (!$pageRes || !($pageRow = $pageRes->fetch_assoc())) {
        echo "Skip page meta (page missing): {$templateName}\n";
        return;
    }

    $pageId = (int)$pageRow['id'];
    $db->query(
        "UPDATE {$prefix}couch_pages SET page_title='{$titleEsc}', page_desc='{$descEsc}' WHERE id={$pageId} LIMIT 1"
    );
    echo "Updated page meta for {$templateName} page #{$pageId}\n";
    if ((string)$pageRow['page_title'] !== $pageTitle) {
        echo "  title was: {$pageRow['page_title']}\n";
    }
    if ((string)$pageRow['page_desc'] !== $pageDesc) {
        echo "  desc was: {$pageRow['page_desc']}\n";
    }
}

function gl_seo_find_template_name($db, $prefix, $candidates)
{
    foreach ($candidates as $candidate) {
        $esc = $db->real_escape_string($candidate);
        $res = $db->query("SELECT name FROM {$prefix}couch_templates WHERE name='{$esc}' LIMIT 1");
        if ($res && $res->fetch_assoc()) {
            return $candidate;
        }
    }
    return null;
}

foreach ($fieldUpdates as $templateName => $fields) {
    foreach ($fields as $fieldName => $value) {
        gl_seo_sync_field($db, $prefix, $templateName, $fieldName, $value);
    }
}

foreach ($pageMetaUpdates as $templateName => $meta) {
    gl_seo_sync_page_meta($db, $prefix, $templateName, $meta['page_title'], $meta['page_desc']);
}

$admTpl = gl_seo_find_template_name($db, $prefix, array('index.php', 'admiralteyskaya/index.php'));
if ($admTpl && isset($pageMetaUpdates['index.php'])) {
    gl_seo_sync_page_meta($db, $prefix, $admTpl, $pageMetaUpdates['index.php']['page_title'], $pageMetaUpdates['index.php']['page_desc']);
}

$udelTpl = gl_seo_find_template_name($db, $prefix, array('udelnaya/index.php'));
if ($udelTpl && isset($pageMetaUpdates['udelnaya/index.php'])) {
    gl_seo_sync_page_meta($db, $prefix, $udelTpl, $pageMetaUpdates['udelnaya/index.php']['page_title'], $pageMetaUpdates['udelnaya/index.php']['page_desc']);
}

$brute = $db->query(
    "SELECT dt.id, dt.value, f.name AS field, t.name AS tpl " .
    "FROM {$prefix}couch_data_text dt " .
    "JOIN {$prefix}couch_fields f ON f.id = dt.field_id " .
    "JOIN {$prefix}couch_pages p ON p.id = dt.page_id " .
    "JOIN {$prefix}couch_templates t ON t.id = p.template_id " .
    "WHERE f.name IN ('seo_title_default','seo_desc_default','home_seo_desc')"
);
if ($brute) {
    while ($row = $brute->fetch_assoc()) {
        echo "Existing {$row['tpl']}::{$row['field']} id={$row['id']} value={$row['value']}\n";
    }
}

$fixes = array(
    array('like' => '%Удельной%', 'field' => 'seo_title_default', 'tpl' => 'globals.php', 'value' => $fieldUpdates['globals.php']['seo_title_default']),
    array('like' => '%лучшей кальянной%', 'field' => 'seo_desc_default', 'tpl' => 'globals.php', 'value' => $fieldUpdates['globals.php']['seo_desc_default']),
    array('like' => '%995 624%', 'field' => 'seo_desc_default', 'tpl' => 'udelnaya/globals.php', 'value' => $fieldUpdates['udelnaya/globals.php']['seo_desc_default']),
);
foreach ($fixes as $fix) {
    $likeEsc = $db->real_escape_string($fix['like']);
    $fieldEsc = $db->real_escape_string($fix['field']);
    $tplEsc = $db->real_escape_string($fix['tpl']);
    $valueEsc = $db->real_escape_string($fix['value']);
    $db->query(
        "UPDATE {$prefix}couch_data_text dt " .
        "JOIN {$prefix}couch_fields f ON f.id = dt.field_id " .
        "JOIN {$prefix}couch_pages p ON p.id = dt.page_id " .
        "JOIN {$prefix}couch_templates t ON t.id = p.template_id " .
        "SET dt.value='{$valueEsc}' " .
        "WHERE f.name='{$fieldEsc}' AND t.name='{$tplEsc}' AND dt.value LIKE '{$likeEsc}'"
    );
    if ($db->affected_rows > 0) {
        echo "Brute-fixed {$fix['tpl']}::{$fix['field']} ({$db->affected_rows} row(s))\n";
    }
}

$cacheDir = $root . '/couch/cache';
if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '/*');
    $removed = 0;
    if ($files) {
        foreach ($files as $file) {
            if (is_file($file) && @unlink($file)) {
                $removed++;
            }
        }
    }
    echo "Cache cleared: {$removed} file(s)\n";
}

echo "Done.\n";
