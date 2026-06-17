<?php
/**
 * Seed / migrate branch gallery sections (Interior, Menu, Vibe).
 * Run on server after deploying updated gallery.php templates:
 *   php _maintenance/seed-gallery-cli.php
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
$menuBase = $imgBase . 'menu-visual/';

function gallery_row($img, $title, $alt = '')
{
    return array(
        'gallery_img' => $img,
        'gallery_img_title' => $title,
        'gallery_img_alt' => $alt ? $alt : $title,
    );
}

function menu_gallery_defaults($menuBase)
{
    return array(
        gallery_row($menuBase . 'summer-spritz.webp', 'Summer Spritz', 'Summer Spritz — авторский коктейль Garden Lounge'),
        gallery_row($menuBase . 'opalennyi-roll-s-lososem-tuntsom-i-grebeshkom.webp', 'Опаленный ролл', 'Опаленный ролл с лососем, тунцом и гребешком'),
        gallery_row($menuBase . 'chizkeik-matcha.webp', 'Чизкейк Матча', 'Чизкейк Матча — десерт Garden Lounge'),
        gallery_row($menuBase . 'elder-bloom.webp', 'Elder Bloom', 'Elder Bloom — авторский коктейль Garden Lounge'),
        gallery_row($menuBase . 'ramen-katsu.webp', 'Рамен Кацу', 'Рамен Кацу — горячее блюдо Garden Lounge'),
        gallery_row($menuBase . 'aperol-spritz.webp', 'Aperol Spritz', 'Aperol Spritz — коктейль Garden Lounge'),
        gallery_row($menuBase . 'poke-s-lososem.webp', 'Поке с лососем', 'Поке с лососем — блюдо кухни Garden Lounge'),
        gallery_row($menuBase . 'fruktovyi-roll.webp', 'Фруктовый ролл', 'Фруктовый ролл — десерт Garden Lounge'),
        gallery_row($menuBase . 'cherry-smoke.webp', 'Cherry Smoke', 'Cherry Smoke — авторский коктейль Garden Lounge'),
        gallery_row($menuBase . 'tom-iam-s-moreproduktami.webp', 'Том Ям', 'Том Ям с морепродуктами — блюдо кухни Garden Lounge'),
    );
}

function admiral_interior_defaults($imgBase)
{
    $rows = array(
        gallery_row($imgBase . 'garden-main.webp', 'Garden Lounge', 'Интерьер Garden Lounge на Адмиралтейской'),
        gallery_row($imgBase . 'garden.webp', 'Вечнозелёный сад', 'Интерьер лаунж-бара Garden Lounge'),
        gallery_row($imgBase . 'garden-2.webp', 'VIP-зона', 'VIP-зона Garden Lounge на Мойке'),
    );

    for ($i = 1; $i <= 6; $i++) {
        $rows[] = gallery_row($imgBase . 'ga' . $i . '.webp', 'Interior ' . $i, 'Интерьер Garden Lounge Admiralteyskaya');
    }

    return $rows;
}

function admiral_vibe_defaults($imgBase)
{
    $rows = array();
    for ($i = 1; $i <= 6; $i++) {
        $rows[] = gallery_row($imgBase . 'gf' . $i . '.webp', 'Vibe ' . $i, 'Атмосфера Garden Lounge Admiralteyskaya');
    }
    return $rows;
}

function udelnaya_interior_defaults($imgBase)
{
    return array(
        gallery_row($imgBase . 'kalyannaya-garden-lounge-udelnaya-interer-spb.webp', 'Вечнозелёный сад', 'Интерьер Garden Lounge на Удельной'),
        gallery_row($imgBase . 'garden.webp', 'Интерьер лаунжа', 'Интерьер лаунж-бара Garden Lounge Удельная'),
        gallery_row($imgBase . 'safonovleonid_green_65.webp', 'VIP-зона', 'VIP-зона Garden Lounge на Удельной'),
    );
}

function udelnaya_vibe_defaults($imgBase)
{
    return array(
        gallery_row($imgBase . 'safonovleonid_green_65.webp', 'Atmosphere', 'Атмосфера Garden Lounge на Удельной'),
        gallery_row($imgBase . 'garden.webp', 'Evening Vibe', 'Вечер в Garden Lounge Удельная'),
    );
}

function field_value($value)
{
    if (is_object($value) && method_exists($value, 'get_data')) {
        return trim((string) $value->get_data());
    }
    return trim((string) $value);
}

function read_repeatable_rows($field)
{
    $rows = array();

    if (!$field) {
        return $rows;
    }

    if (method_exists($field, 'get_rows')) {
        $sourceRows = $field->get_rows(true);
    } elseif (isset($field->rows) && is_array($field->rows)) {
        $sourceRows = $field->rows;
    } else {
        return $rows;
    }

    foreach ($sourceRows as $row) {
        if (!is_array($row)) {
            continue;
        }

        $img = isset($row['gallery_img']) ? field_value($row['gallery_img']) : '';
        if (!$img) {
            continue;
        }

        $title = isset($row['gallery_img_title']) ? field_value($row['gallery_img_title']) : '';
        $alt = isset($row['gallery_img_alt']) ? field_value($row['gallery_img_alt']) : '';

        $rows[] = gallery_row($img, $title, $alt ? $alt : $title);
    }

    return $rows;
}

function read_legacy_gallery_items($pg)
{
    $field = null;
    if (isset($pg->_fields['gallery_items'])) {
        $field = $pg->_fields['gallery_items'];
    } elseif (isset($pg->fields['gallery_items'])) {
        $field = $pg->fields['gallery_items'];
    }

    if (!$field) {
        return array(
            'interior' => array(),
            'menu' => array(),
            'vibe' => array(),
        );
    }

    $grouped = array(
        'interior' => array(),
        'menu' => array(),
        'vibe' => array(),
    );

    if (method_exists($field, 'get_rows')) {
        $sourceRows = $field->get_rows(true);
    } elseif (isset($field->rows) && is_array($field->rows)) {
        $sourceRows = $field->rows;
    } else {
        return $grouped;
    }

    foreach ($sourceRows as $row) {
        if (!is_array($row)) {
            continue;
        }

        $img = isset($row['gallery_img']) ? field_value($row['gallery_img']) : '';
        if (!$img) {
            continue;
        }

        $title = isset($row['gallery_img_title']) ? field_value($row['gallery_img_title']) : '';
        $alt = isset($row['gallery_img_alt']) ? field_value($row['gallery_img_alt']) : '';
        $category = isset($row['gallery_category']) ? field_value($row['gallery_category']) : 'interior';

        if (!isset($grouped[$category])) {
            $category = 'interior';
        }

        $grouped[$category][] = gallery_row($img, $title, $alt ? $alt : $title);
    }

    return $grouped;
}

function get_page_field($pg, $name)
{
    if (isset($pg->_fields[$name])) {
        return $pg->_fields[$name];
    }
    if (isset($pg->fields[$name])) {
        return $pg->fields[$name];
    }
    return null;
}

function save_repeatable_field($pg, $fieldName, $rows)
{
    $field = get_page_field($pg, $fieldName);
    if (!$field) {
        throw new RuntimeException('Field ' . $fieldName . ' missing');
    }

    $field->store_posted_changes($rows, 'db_persist');
}

function seed_gallery_template($templateName, $defaults)
{
    global $FUNCS;

    $FUNCS->invalidate_cache();
    $pg = new KWebpage($templateName);
    if (!empty($pg->error)) {
        throw new RuntimeException('Failed to load ' . $templateName . ': ' . $pg->err_msg);
    }

    $legacy = read_legacy_gallery_items($pg);

    $interior = read_repeatable_rows(get_page_field($pg, 'gallery_interior_items'));
    $menu = read_repeatable_rows(get_page_field($pg, 'gallery_menu_items'));
    $vibe = read_repeatable_rows(get_page_field($pg, 'gallery_vibe_items'));

    if (!$interior) {
        $interior = $legacy['interior'] ? $legacy['interior'] : $defaults['interior'];
    }
    if (!$menu) {
        $menu = $legacy['menu'] ? $legacy['menu'] : $defaults['menu'];
    }
    if (!$vibe) {
        $vibe = $legacy['vibe'] ? $legacy['vibe'] : $defaults['vibe'];
    }

    save_repeatable_field($pg, 'gallery_interior_items', $interior);
    save_repeatable_field($pg, 'gallery_menu_items', $menu);
    save_repeatable_field($pg, 'gallery_vibe_items', $vibe);

    $errors = $pg->save('db_persist');
    if ($errors) {
        throw new RuntimeException('Save failed for ' . $templateName . ' (' . $errors . ' errors)');
    }

    echo "{$templateName}: interior=" . count($interior) . ', menu=' . count($menu) . ', vibe=' . count($vibe) . "\n";
}

try {
    $menuDefaults = menu_gallery_defaults($menuBase);

    seed_gallery_template('gallery.php', array(
        'interior' => admiral_interior_defaults($imgBase),
        'menu' => $menuDefaults,
        'vibe' => admiral_vibe_defaults($imgBase),
    ));

    seed_gallery_template('udelnaya/gallery.php', array(
        'interior' => udelnaya_interior_defaults($imgBase),
        'menu' => $menuDefaults,
        'vibe' => udelnaya_vibe_defaults($imgBase),
    ));

    $FUNCS->invalidate_cache();
    echo "Gallery seed complete.\n";
} catch (Exception $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
