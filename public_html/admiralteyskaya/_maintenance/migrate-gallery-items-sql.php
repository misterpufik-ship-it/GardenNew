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
$text = K_DB_TABLES_PREFIX . 'couch_data_text';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';
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

function decode_repeatable_value($raw)
{
    $raw = trim((string) $raw);
    if ($raw === '') {
        return array();
    }

    $data = @unserialize($raw);
    if (is_array($data)) {
        return $data;
    }

    $data = json_decode($raw, true);
    return is_array($data) ? $data : array();
}

function normalize_row($row)
{
    if (!is_array($row)) {
        return null;
    }

    $img = '';
    $title = '';
    $alt = '';
    $category = 'interior';

    foreach ($row as $key => $value) {
        if (!is_string($key)) {
            continue;
        }
        if ($key === 'gallery_img' || substr($key, -10) === 'gallery_img') {
            $img = trim((string) $value);
        } elseif ($key === 'gallery_img_title') {
            $title = trim((string) $value);
        } elseif ($key === 'gallery_img_alt') {
            $alt = trim((string) $value);
        } elseif ($key === 'gallery_category') {
            $category = trim((string) $value);
        }
    }

    if (!$img) {
        return null;
    }

    return array(
        'gallery_img' => $img,
        'gallery_img_title' => $title,
        'gallery_img_alt' => $alt ? $alt : $title,
        'gallery_category' => $category ? $category : 'interior',
    );
}

function grouped_from_legacy($rows)
{
    $grouped = array(
        'interior' => array(),
        'menu' => array(),
        'vibe' => array(),
    );

    foreach ($rows as $row) {
        $normalized = normalize_row($row);
        if (!$normalized) {
            continue;
        }
        $category = $normalized['gallery_category'];
        unset($normalized['gallery_category']);
        if (!isset($grouped[$category])) {
            $category = 'interior';
        }
        $grouped[$category][] = $normalized;
    }

    return $grouped;
}

function menu_defaults()
{
    $menuBase = 'https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/menu-visual/';
    return array(
        array('gallery_img' => $menuBase . 'summer-spritz.webp', 'gallery_img_title' => 'Summer Spritz', 'gallery_img_alt' => 'Summer Spritz — авторский коктейль Garden Lounge'),
        array('gallery_img' => $menuBase . 'opalennyi-roll-s-lososem-tuntsom-i-grebeshkom.webp', 'gallery_img_title' => 'Опаленный ролл', 'gallery_img_alt' => 'Опаленный ролл с лососем, тунцом и гребешком'),
        array('gallery_img' => $menuBase . 'chizkeik-matcha.webp', 'gallery_img_title' => 'Чизкейк Матча', 'gallery_img_alt' => 'Чизкейк Матча — десерт Garden Lounge'),
        array('gallery_img' => $menuBase . 'elder-bloom.webp', 'gallery_img_title' => 'Elder Bloom', 'gallery_img_alt' => 'Elder Bloom — авторский коктейль Garden Lounge'),
        array('gallery_img' => $menuBase . 'ramen-katsu.webp', 'gallery_img_title' => 'Рамен Кацу', 'gallery_img_alt' => 'Рамен Кацу — горячее блюдо Garden Lounge'),
        array('gallery_img' => $menuBase . 'aperol-spritz.webp', 'gallery_img_title' => 'Aperol Spritz', 'gallery_img_alt' => 'Aperol Spritz — коктейль Garden Lounge'),
        array('gallery_img' => $menuBase . 'poke-s-lososem.webp', 'gallery_img_title' => 'Поке с лососем', 'gallery_img_alt' => 'Поке с лососем — блюдо кухни Garden Lounge'),
        array('gallery_img' => $menuBase . 'fruktovyi-roll.webp', 'gallery_img_title' => 'Фруктовый ролл', 'gallery_img_alt' => 'Фруктовый ролл — десерт Garden Lounge'),
        array('gallery_img' => $menuBase . 'cherry-smoke.webp', 'gallery_img_title' => 'Cherry Smoke', 'gallery_img_alt' => 'Cherry Smoke — авторский коктейль Garden Lounge'),
        array('gallery_img' => $menuBase . 'tom-iam-s-moreproduktami.webp', 'gallery_img_title' => 'Том Ям', 'gallery_img_alt' => 'Том Ям с морепродуктами — блюдо кухни Garden Lounge'),
    );
}

function admiral_defaults($imgBase)
{
    $interior = array(
        array('gallery_img' => $imgBase . 'garden-main.webp', 'gallery_img_title' => 'Garden Lounge', 'gallery_img_alt' => 'Интерьер Garden Lounge на Адмиралтейской'),
        array('gallery_img' => $imgBase . 'garden.webp', 'gallery_img_title' => 'Вечнозелёный сад', 'gallery_img_alt' => 'Интерьер лаунж-бара Garden Lounge'),
        array('gallery_img' => $imgBase . 'garden-2.webp', 'gallery_img_title' => 'VIP-зона', 'gallery_img_alt' => 'VIP-зона Garden Lounge на Мойке'),
    );
    for ($i = 1; $i <= 6; $i++) {
        $interior[] = array('gallery_img' => $imgBase . 'ga' . $i . '.webp', 'gallery_img_title' => 'Interior ' . $i, 'gallery_img_alt' => 'Интерьер Garden Lounge Admiralteyskaya');
    }
    $vibe = array();
    for ($i = 1; $i <= 6; $i++) {
        $vibe[] = array('gallery_img' => $imgBase . 'gf' . $i . '.webp', 'gallery_img_title' => 'Vibe ' . $i, 'gallery_img_alt' => 'Атмосфера Garden Lounge Admiralteyskaya');
    }
    return array('interior' => $interior, 'menu' => menu_defaults(), 'vibe' => $vibe);
}

function udelnaya_defaults($imgBase)
{
    return array(
        'interior' => array(
            array('gallery_img' => $imgBase . 'kalyannaya-garden-lounge-udelnaya-interer-spb.webp', 'gallery_img_title' => 'Вечнозелёный сад', 'gallery_img_alt' => 'Интерьер Garden Lounge на Удельной'),
            array('gallery_img' => $imgBase . 'garden.webp', 'gallery_img_title' => 'Интерьер лаунжа', 'gallery_img_alt' => 'Интерьер лаунж-бара Garden Lounge Удельная'),
            array('gallery_img' => $imgBase . 'safonovleonid_green_65.webp', 'gallery_img_title' => 'VIP-зона', 'gallery_img_alt' => 'VIP-зона Garden Lounge на Удельной'),
        ),
        'menu' => menu_defaults(),
        'vibe' => array(
            array('gallery_img' => $imgBase . 'safonovleonid_green_65.webp', 'gallery_img_title' => 'Atmosphere', 'gallery_img_alt' => 'Атмосфера Garden Lounge на Удельной'),
            array('gallery_img' => $imgBase . 'garden.webp', 'gallery_img_title' => 'Evening Vibe', 'gallery_img_alt' => 'Вечер в Garden Lounge Удельная'),
        ),
    );
}

function read_legacy_grouped($db, $fields, $text, $pages, $templates, $templateName)
{
    $template = one($db, "SELECT id FROM `{$templates}` WHERE name=" . q($db, $templateName) . " LIMIT 1");
    if (!$template) {
        return array('interior' => array(), 'menu' => array(), 'vibe' => array());
    }

    $page = one($db, "SELECT id FROM `{$pages}` WHERE template_id=" . (int) $template['id'] . " LIMIT 1");
    $field = one($db, "SELECT id FROM `{$fields}` WHERE template_id=" . (int) $template['id'] . " AND name='gallery_items' LIMIT 1");
    if (!$page || !$field) {
        return array('interior' => array(), 'menu' => array(), 'vibe' => array());
    }

    $value = one($db, "SELECT value FROM `{$text}` WHERE page_id=" . (int) $page['id'] . " AND field_id=" . (int) $field['id'] . " LIMIT 1");
    if (!$value) {
        return array('interior' => array(), 'menu' => array(), 'vibe' => array());
    }

    return grouped_from_legacy(decode_repeatable_value($value['value']));
}

function upsert_repeatable_value($db, $text, $pageId, $fieldId, $rows)
{
    $serialized = serialize($rows);
    $existing = one($db, "SELECT page_id FROM `{$text}` WHERE page_id=" . (int) $pageId . " AND field_id=" . (int) $fieldId . " LIMIT 1");
    if ($existing) {
        $db->query("UPDATE `{$text}` SET value=" . q($db, $serialized) . " WHERE page_id=" . (int) $pageId . " AND field_id=" . (int) $fieldId . " LIMIT 1");
    } else {
        $db->query("INSERT INTO `{$text}` (page_id, field_id, value) VALUES (" . (int) $pageId . "," . (int) $fieldId . "," . q($db, $serialized) . ")");
    }
}

function migrate_template($db, $fields, $text, $pages, $templates, $templateName, $defaults)
{
    $template = one($db, "SELECT id FROM `{$templates}` WHERE name=" . q($db, $templateName) . " LIMIT 1");
    if (!$template) {
        throw new RuntimeException('Template missing: ' . $templateName);
    }

    $page = one($db, "SELECT id FROM `{$pages}` WHERE template_id=" . (int) $template['id'] . " LIMIT 1");
    if (!$page) {
        throw new RuntimeException('Page missing for ' . $templateName);
    }

    $legacy = read_legacy_grouped($db, $fields, $text, $pages, $templates, $templateName);
    $map = array(
        'interior' => 'gallery_interior_items',
        'menu' => 'gallery_menu_items',
        'vibe' => 'gallery_vibe_items',
    );

    foreach ($map as $category => $fieldName) {
        $rows = $legacy[$category];
        if (!$rows) {
            $rows = $defaults[$category];
        }

        $field = one($db, "SELECT id FROM `{$fields}` WHERE template_id=" . (int) $template['id'] . " AND name=" . q($db, $fieldName) . " LIMIT 1");
        if (!$field) {
            throw new RuntimeException('Field missing: ' . $fieldName);
        }

        upsert_repeatable_value($db, $text, (int) $page['id'], (int) $field['id'], $rows);
        echo "{$templateName} {$fieldName}: " . count($rows) . " rows\n";
    }
}

$imgBase = 'https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/';

try {
    migrate_template($db, $fields, $text, $pages, $templates, 'gallery.php', admiral_defaults($imgBase));
    migrate_template($db, $fields, $text, $pages, $templates, 'udelnaya/gallery.php', udelnaya_defaults($imgBase));

    require __DIR__ . '/clear-couch-cache-cli.php';
    echo "Gallery SQL migration complete.\n";
} catch (Exception $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
