<?php
/**
 * Rebuild gallery lists on live after failed migration (paths from last known good state).
 */
if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}

$root = realpath(__DIR__ . '/..');
chdir($root);
require_once $root . '/couch/cms.php';

global $AUTH, $FUNCS;
$AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;

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

function save_rows($pg, $fieldName, $imgKey, $paths)
{
    $rows = array();
    foreach ($paths as $path) {
        $rows[] = array($imgKey => $path);
    }
    $field = get_page_field($pg, $fieldName);
    if (!$field) {
        throw new RuntimeException("Missing field {$fieldName}");
    }
    $field->store_posted_changes($rows, 'db_persist');
}

$lists = array(
    'gallery.php' => array(
        'gallery_interior_items' => array(
            'interior_img' => array(
                ':chatgpt-image-18-anv-2026-g-14_56_49.webp',
            ),
        ),
        'gallery_menu_items' => array(
            'kitchen_img' => array(
                ':menu-visual/kurinaia-grudka-s-triufel-nym-piure.webp',
                ':menu-visual/summer-spritz.webp',
                ':menu-visual/poke-s-lososem.webp',
            ),
        ),
        'gallery_vibe_items' => array(
            'vibe_img' => array(
                ':vibe-adm-01.webp',
                ':vibe-adm-02.webp',
                ':vibe-adm-03.webp',
                ':vibe-adm-04.webp',
                ':vibe-adm-05.webp',
                ':vibe-adm-06.webp',
                ':vibe-adm-07.webp',
                ':vibe-adm-08.webp',
                ':vibe-adm-09.webp',
                ':vibe-adm-10.webp',
                ':safonovleonid_green_65.webp',
            ),
        ),
    ),
    'udelnaya/gallery.php' => array(
        'gallery_interior_items' => array(
            'interior_img' => array(
                ':garden-2.webp',
            ),
        ),
        'gallery_menu_items' => array(
            'kitchen_img' => array(),
        ),
        'gallery_vibe_items' => array(
            'vibe_img' => array(
                ':safonovleonid_green_65.webp',
                ':garden.webp',
                ':vibe-udel-01.webp',
                ':vibe-udel-01b.webp',
                ':vibe-udel-02.webp',
                ':vibe-udel-03.webp',
            ),
        ),
    ),
);

$db = new mysqli(K_DB_HOST, K_DB_USER, K_DB_PASSWORD, K_DB_NAME);
$db->set_charset('utf8');

foreach ($lists as $templateName => $sections) {
    echo "\n=== {$templateName} ===\n";
    $FUNCS->invalidate_cache();
    $tid = (int) $db->query("SELECT id FROM couch_templates WHERE name='" . $db->real_escape_string($templateName) . "' LIMIT 1")->fetch_assoc()['id'];
    $pageId = (int) $db->query("SELECT id FROM couch_pages WHERE template_id={$tid} ORDER BY id LIMIT 1")->fetch_assoc()['id'];
    $pg = new KWebpage($pageId, 1);
    if (!empty($pg->error)) {
        throw new RuntimeException($pg->err_msg);
    }
    foreach ($sections as $repeatable => $imgMap) {
        foreach ($imgMap as $imgKey => $paths) {
            save_rows($pg, $repeatable, $imgKey, $paths);
            echo "{$repeatable}: " . count($paths) . " photos\n";
        }
    }
    $errors = $pg->save('db_persist');
    if ($errors) {
        throw new RuntimeException("Save failed ({$errors}) for {$templateName}");
    }
}

$FUNCS->invalidate_cache();
require __DIR__ . '/clear-couch-cache-cli.php';
echo "rebuild-gallery-live-lists complete.\n";
