<?php
/**
 * Sync Udelnaya philosophy + SEO defaults in CouchCMS DB.
 * ?key=<md5('garden-lounge-sync-udelnaya-philosophy-20260705')>
 */
$expectedKey = md5('garden-lounge-sync-udelnaya-philosophy-20260705');
if ((isset($_GET['key']) ? $_GET['key'] : '') !== $expectedKey) {
    http_response_code(403);
    exit("Forbidden\n");
}
header('Content-Type: text/plain; charset=utf-8');

$fieldUpdates = array(
    'udelnaya/globals.php' => array(
        'seo_title_default' => 'Garden Lounge в Приморском районе — кальянная и лаунж-бар, м. Удельная',
        'seo_desc_default' => 'Garden Lounge на ул. Аккуратова 13, Приморский район: премиальные кальяны, кухня, VIP-комнаты, PS5 и бронь столика у метро Удельная. Тел. +7 950 047-33-65.',
        'seo_keywords_default' => 'Garden Lounge Приморский район, кальянная Приморский район, кальянная в Приморском районе, кальянная у метро Удельная, лаунж бар Приморский район, кальянная на севере СПб, кальянная СПб, ул. Аккуратова 13, VIP-комнаты, PS5, кухня',
    ),
    'udelnaya/about.php' => array(
        'phil_seo_h1' => 'Garden Lounge — кальянная и лаунж-бар в Приморском районе Санкт-Петербурга',
        'phil_slogan' => 'Garden Lounge — место, где рождаются ритуалы, достойные ваших воспоминаний',
        'phil_img_alt' => 'Эстетичный лаунж-бар в Приморском районе, Санкт-Петербург',
        'phil_lsi' => 'кальянная СПб, кальянная Приморский район, кальянная в Приморском районе, кальянная у метро Удельная, лаунж бар Приморский район, лаунж бар СПб, кальянная на севере СПб, кальянная с кухней СПб, VIP кальянная СПб, премиальная кальянная, кальянная с PS5, ул. Аккуратова 13',
        'phil_content' => '<p>На ул. Аккуратова, у метро Удельная, спрятан вечнозелёный сад — эстетичная кальянная, где городская суета остаётся за дверью. Живые тропики, фонтан и камин создают пространство, в котором время замедляется само.</p><p>Премиальная кальянная с изысканной кухней в СПб: авторские кальяны, бар, VIP-комнаты и вечера с PS5. Уютная атмосфера третьего места.</p>',
    ),
);

$pageMetaUpdates = array(
    'udelnaya/index.php' => array(
        'page_title' => 'Garden Lounge в Приморском районе',
        'page_desc' => 'Garden Lounge на ул. Аккуратова 13, Приморский район: премиальные кальяны, кухня, VIP-комнаты, PS5 и бронь столика у метро Удельная. Тел. +7 950 047-33-65.',
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

function gl_udel_sync_field($db, $prefix, $templateName, $fieldName, $value)
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

function gl_udel_sync_page_meta($db, $prefix, $templateName, $pageTitle, $pageDesc)
{
    $tplEsc = $db->real_escape_string($templateName);
    $titleEsc = $db->real_escape_string($pageTitle);
    $descEsc = $db->real_escape_string($pageDesc);

    $tpl = $db->query("SELECT id FROM {$prefix}couch_templates WHERE name='{$tplEsc}' LIMIT 1");
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
}

foreach ($fieldUpdates as $templateName => $fields) {
    foreach ($fields as $fieldName => $value) {
        gl_udel_sync_field($db, $prefix, $templateName, $fieldName, $value);
    }
}

foreach ($pageMetaUpdates as $templateName => $meta) {
    gl_udel_sync_page_meta($db, $prefix, $templateName, $meta['page_title'], $meta['page_desc']);
}

$cacheDir = $root . '/couch/cache';
if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '/*');
    $removed = 0;
    if ($files) {
        foreach ($files as $file) {
            if (is_file($file) && basename($file) !== '.htaccess' && @unlink($file)) {
                $removed++;
            }
        }
    }
    echo "Cache cleared: {$removed} file(s)\n";
}

echo "Done.\n";
