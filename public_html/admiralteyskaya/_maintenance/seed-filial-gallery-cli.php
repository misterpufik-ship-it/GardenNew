<?php
/**
 * Seed filial tabbed galleries from branch Experience sections.
 * Run on server:
 *   php _maintenance/seed-filial-gallery-cli.php
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

$root = realpath(__DIR__ . '/..');
chdir($root);
require_once $root . '/couch/cms.php';

global $AUTH, $FUNCS;

if (!isset($AUTH->user) || !is_object($AUTH->user)) {
    fwrite(STDERR, "Couch auth not initialized\n");
    exit(1);
}

$AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;

$imgBase = 'https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/';
$menuBase = 'https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/menu-visual/';

function filial_row($img, $title, $alt, $category)
{
    return array(
        'final_gallery_img' => $img,
        'final_gallery_title' => $title,
        'final_gallery_alt' => $alt,
        'final_gallery_category' => $category,
    );
}

function menu_gallery_rows($menuBase)
{
    return array(
        filial_row($menuBase . 'summer-spritz.webp', 'Summer Spritz', 'Summer Spritz — авторский коктейль Garden Lounge', 'menu'),
        filial_row($menuBase . 'opalennyi-roll-s-lososem-tuntsom-i-grebeshkom.webp', 'Опаленный ролл', 'Опаленный ролл с лососем, тунцом и гребешком', 'menu'),
        filial_row($menuBase . 'chizkeik-matcha.webp', 'Чизкейк Матча', 'Чизкейк Матча — десерт Garden Lounge', 'menu'),
        filial_row($menuBase . 'elder-bloom.webp', 'Elder Bloom', 'Elder Bloom — авторский коктейль Garden Lounge', 'menu'),
        filial_row($menuBase . 'ramen-katsu.webp', 'Рамен Кацу', 'Рамен Кацу — горячее блюдо Garden Lounge', 'menu'),
        filial_row($menuBase . 'aperol-spritz.webp', 'Aperol Spritz', 'Aperol Spritz — коктейль Garden Lounge', 'menu'),
        filial_row($menuBase . 'poke-s-lososem.webp', 'Поке с лососем', 'Поке с лососем — блюдо кухни Garden Lounge', 'menu'),
        filial_row($menuBase . 'fruktovyi-roll.webp', 'Фруктовый ролл', 'Фруктовый ролл — десерт Garden Lounge', 'menu'),
        filial_row($menuBase . 'cherry-smoke.webp', 'Cherry Smoke', 'Cherry Smoke — авторский коктейль Garden Lounge', 'menu'),
        filial_row($menuBase . 'tom-iam-s-moreproduktami.webp', 'Том Ям', 'Том Ям с морепродуктами — блюдо кухни Garden Lounge', 'menu'),
    );
}

function udelnaya_gallery_rows($imgBase, $menuBase)
{
    $rows = array(
        filial_row($imgBase . 'kalyannaya-garden-lounge-udelnaya-interer-spb.webp', 'Вечнозелёный сад', 'Интерьер Garden Lounge на Удельной', 'interior'),
        filial_row($imgBase . 'garden.webp', 'Интерьер лаунжа', 'Интерьер лаунж-бара Garden Lounge Удельная', 'interior'),
        filial_row($imgBase . 'safonovleonid_green_65.webp', 'VIP-зона', 'VIP-зона Garden Lounge на Удельной', 'interior'),
    );
    $rows = array_merge($rows, menu_gallery_rows($menuBase));
    $rows[] = filial_row($imgBase . 'safonovleonid_green_65.webp', 'Atmosphere', 'Атмосфера Garden Lounge на Удельной', 'vibe');
    $rows[] = filial_row($imgBase . 'garden.webp', 'Evening Vibe', 'Вечер в Garden Lounge Удельная', 'vibe');
    return $rows;
}

function read_gallery_items($templateName)
{
    $pg = new KWebpage($templateName);
    if (!empty($pg->error)) {
        throw new RuntimeException('Failed to load ' . $templateName . ': ' . $pg->err_msg);
    }
    if (!isset($pg->fields['gallery_items'])) {
        return array();
    }

    $field = $pg->fields['gallery_items'];
    $rows = array();

    if (method_exists($field, 'get_rows')) {
        $sourceRows = $field->get_rows(true);
    } elseif (isset($field->rows) && is_array($field->rows)) {
        $sourceRows = $field->rows;
    } else {
        return array();
    }

    foreach ($sourceRows as $row) {
        $img = '';
        $title = '';
        $alt = '';
        $category = 'interior';

        if (is_array($row)) {
            if (isset($row['gallery_img'])) {
                $img = is_object($row['gallery_img']) ? $row['gallery_img']->get_data() : $row['gallery_img'];
            }
            if (isset($row['gallery_img_title'])) {
                $title = is_object($row['gallery_img_title']) ? $row['gallery_img_title']->get_data() : $row['gallery_img_title'];
            }
            if (isset($row['gallery_img_alt'])) {
                $alt = is_object($row['gallery_img_alt']) ? $row['gallery_img_alt']->get_data() : $row['gallery_img_alt'];
            }
            if (isset($row['gallery_category'])) {
                $category = is_object($row['gallery_category']) ? $row['gallery_category']->get_data() : $row['gallery_category'];
            }
        }

        if ($img) {
            $rows[] = filial_row($img, $title, $alt ? $alt : $title, $category ? $category : 'interior');
        }
    }

    return $rows;
}

function save_filial_gallery($templateName, $rows)
{
    global $FUNCS;

    $FUNCS->invalidate_cache();
    $pg = new KWebpage($templateName);
    if (!empty($pg->error)) {
        throw new RuntimeException('Failed to load ' . $templateName . ': ' . $pg->err_msg);
    }

    $field = null;
    if (isset($pg->_fields['final_gallery_items'])) {
        $field = $pg->_fields['final_gallery_items'];
    } elseif (isset($pg->fields['final_gallery_items'])) {
        $field = $pg->fields['final_gallery_items'];
    }

    if (!$field) {
        throw new RuntimeException('Field final_gallery_items missing in ' . $templateName);
    }

    $field->store_posted_changes($rows, 'db_persist');
    $errors = $pg->save('db_persist');
    if ($errors) {
        throw new RuntimeException('Save failed for ' . $templateName . ' (' . $errors . ' errors)');
    }

    echo "Saved " . count($rows) . " photos to {$templateName}\n";
}

function read_gallery_items_sql()
{
    $config = __DIR__ . '/../couch/config.php';
    if (!is_file($config)) {
        return array();
    }

    require_once $config;
    $host = K_DB_HOST;
    $port = 3306;
    if (strpos($host, ':') !== false) {
        list($host, $port) = explode(':', $host, 2);
    }

    $db = @new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
    if ($db->connect_errno) {
        return array();
    }
    $db->set_charset('utf8');

    $templates = K_DB_TABLES_PREFIX . 'couch_templates';
    $fields = K_DB_TABLES_PREFIX . 'couch_fields';
    $pages = K_DB_TABLES_PREFIX . 'couch_pages';
    $text = K_DB_TABLES_PREFIX . 'couch_data_text';

    $tpl = $db->query("SELECT id FROM `{$templates}` WHERE name='gallery.php' LIMIT 1");
    $tplRow = $tpl ? $tpl->fetch_assoc() : null;
    if (!$tplRow) {
        return array();
    }

    $page = $db->query("SELECT id FROM `{$pages}` WHERE template_id=" . (int)$tplRow['id'] . " LIMIT 1");
    $pageRow = $page ? $page->fetch_assoc() : null;
    if (!$pageRow) {
        return array();
    }

    $fieldMap = array();
    $res = $db->query("SELECT id, name FROM `{$fields}` WHERE template_id=" . (int)$tplRow['id'] . " AND name IN ('gallery_img','gallery_img_title','gallery_img_alt','gallery_category')");
    while ($res && ($row = $res->fetch_assoc())) {
        $fieldMap[$row['name']] = (int)$row['id'];
    }

    if (!$fieldMap) {
        return array();
    }

    $rows = array();
    $res = $db->query(
        "SELECT field_id, value FROM `{$text}` WHERE page_id=" . (int)$pageRow['id'] .
        " AND field_id IN (" . implode(',', array_values($fieldMap)) . ") ORDER BY id"
    );

    $buffer = array();
    while ($res && ($row = $res->fetch_assoc())) {
        $name = array_search((int)$row['field_id'], $fieldMap, true);
        if ($name === false) {
            continue;
        }
        if ($name === 'gallery_img') {
            if ($buffer) {
                $rows[] = $buffer;
            }
            $buffer = array('gallery_img' => $row['value']);
            continue;
        }
        $buffer[$name] = $row['value'];
    }
    if ($buffer) {
        $rows[] = $buffer;
    }

    $out = array();
    foreach ($rows as $row) {
        if (empty($row['gallery_img'])) {
            continue;
        }
        $out[] = filial_row(
            $row['gallery_img'],
            isset($row['gallery_img_title']) ? $row['gallery_img_title'] : '',
            isset($row['gallery_img_alt']) ? $row['gallery_img_alt'] : '',
            isset($row['gallery_category']) ? $row['gallery_category'] : 'interior'
        );
    }

    return $out;
}

try {
    $FUNCS->invalidate_cache();

    $admiralRows = read_gallery_items('gallery.php');
    if (!$admiralRows) {
        $admiralRows = read_gallery_items_sql();
    }
    if (!$admiralRows) {
        echo "Warning: gallery.php has no CMS rows, using menu-only fallback for admiral\n";
    }
    $admiralFilialRows = array_merge($admiralRows, menu_gallery_rows($menuBase));

    save_filial_gallery('filial.php', udelnaya_gallery_rows($imgBase, $menuBase));
    save_filial_gallery('udelnaya/filial.php', $admiralFilialRows);

    $FUNCS->invalidate_cache();
    echo "Filial gallery seed complete.\n";
} catch (Exception $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
