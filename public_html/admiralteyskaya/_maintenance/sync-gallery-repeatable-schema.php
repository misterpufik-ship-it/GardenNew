<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

$config = __DIR__ . '/../couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ?: 3306;
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
}

$db = new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
$db->set_charset('utf8');

$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$templates = K_DB_TABLES_PREFIX . 'couch_templates';

function q($db, $value)
{
    return "'" . $db->real_escape_string((string) $value) . "'";
}

function one($db, $sql)
{
    $res = $db->query($sql);
    if (!$res) {
        throw new RuntimeException($db->error . "\n" . $sql);
    }
    $row = $res->fetch_assoc();
    return $row ?: null;
}

function clean_repeatable_html()
{
    return "<cms:editable name='gallery_img' label='Фото' type='image' />\r\n" .
           "<cms:editable name='gallery_img_title' label='Подпись к фото' type='text' />\r\n" .
           "<cms:editable name='gallery_img_alt' label='ALT для SEO' type='text' />";
}

$targets = array(
    array('gallery.php', 'gallery_interior_items', 'Фото интерьера'),
    array('gallery.php', 'gallery_menu_items', 'Фото меню'),
    array('gallery.php', 'gallery_vibe_items', 'Фото атмосферы'),
    array('udelnaya/gallery.php', 'gallery_interior_items', 'Фото интерьера'),
    array('udelnaya/gallery.php', 'gallery_menu_items', 'Фото меню'),
    array('udelnaya/gallery.php', 'gallery_vibe_items', 'Фото атмосферы'),
);

try {
    foreach ($targets as $target) {
        list($templateName, $repeatableName, $repeatableLabel) = $target;
        $template = one($db, "SELECT id FROM `{$templates}` WHERE name=" . q($db, $templateName) . " LIMIT 1");
        $field = one(
            $db,
            "SELECT id FROM `{$fields}` WHERE template_id=" . (int) $template['id'] .
            " AND name=" . q($db, $repeatableName) . " LIMIT 1"
        );
        if (!$template || !$field) {
            echo "Skip {$templateName}::{$repeatableName}\n";
            continue;
        }

        $html = clean_repeatable_html();
        $db->query(
            "UPDATE `{$fields}` SET _html=" . q($db, $html) . ", label=" . q($db, $repeatableLabel) .
            " WHERE id=" . (int) $field['id'] . " LIMIT 1"
        );
        echo "Synced {$templateName}::{$repeatableName}\n";
    }

    require __DIR__ . '/clear-couch-cache-cli.php';
} catch (Exception $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
