<?php
if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}
$config = __DIR__ . '/../couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;
$db = new mysqli(K_DB_HOST, K_DB_USER, K_DB_PASSWORD, K_DB_NAME);
if ($db->connect_errno) {
    fwrite(STDERR, $db->connect_error . "\n");
    exit(1);
}
$db->set_charset('utf8');

function gallery_pack($paths)
{
    $rows = array();
    foreach ($paths as $p) {
        $rows[] = array('gallery_img' => $p);
    }
    return serialize($rows);
}

function upsert($db, $pageId, $fieldId, $paths)
{
    $val = gallery_pack($paths);
    $res = $db->query(
        "UPDATE couch_data_text SET value='" . $db->real_escape_string($val) . "', search_value='' " .
        "WHERE page_id=" . (int) $pageId . " AND field_id=" . (int) $fieldId . " LIMIT 1"
    );
    $aff = $db->affected_rows;
    if ($aff === 0) {
        $exists = $db->query(
            "SELECT page_id FROM couch_data_text WHERE page_id=" . (int) $pageId .
            " AND field_id=" . (int) $fieldId . " LIMIT 1"
        )->fetch_assoc();
        if ($exists) {
            $db->query(
                "UPDATE couch_data_text SET value='" . $db->real_escape_string($val) . "', search_value='' " .
                "WHERE page_id=" . (int) $pageId . " AND field_id=" . (int) $fieldId . " LIMIT 1"
            );
            $aff = $db->affected_rows;
            echo "update-empty ";
        } else {
            $db->query(
                "INSERT INTO couch_data_text (page_id, field_id, value, search_value) VALUES (" .
                (int) $pageId . "," . (int) $fieldId . ",'" . $db->real_escape_string($val) . "','')"
            );
            $aff = $db->affected_rows;
            echo "insert ";
        }
    } else {
        echo "update ";
    }
    echo "page={$pageId} field={$fieldId} rows=" . count($paths) . " affected={$aff}\n";
}

$sets = array(
    'gallery.php' => array(
        'gallery_interior_items' => array(':chatgpt-image-18-anv-2026-g-14_56_49.webp'),
        'gallery_menu_items' => array(
            ':menu-visual/kurinaia-grudka-s-triufel-nym-piure.webp',
            ':menu-visual/summer-spritz.webp',
            ':menu-visual/poke-s-lososem.webp',
        ),
        'gallery_vibe_items' => array(
            ':vibe-adm-01.webp', ':vibe-adm-02.webp', ':vibe-adm-03.webp', ':vibe-adm-04.webp',
            ':vibe-adm-05.webp', ':vibe-adm-06.webp', ':vibe-adm-07.webp', ':vibe-adm-08.webp',
            ':vibe-adm-09.webp', ':vibe-adm-10.webp', ':safonovleonid_green_65.webp',
        ),
    ),
    'udelnaya/gallery.php' => array(
        'gallery_interior_items' => array(':garden-2.webp'),
        'gallery_menu_items' => array(),
        'gallery_vibe_items' => array(
            ':safonovleonid_green_65.webp', ':garden.webp', ':vibe-udel-01.webp',
            ':vibe-udel-01b.webp', ':vibe-udel-02.webp', ':vibe-udel-03.webp',
        ),
    ),
);

echo "force gallery data\n";
foreach ($sets as $templateName => $sections) {
    $tid = (int) $db->query("SELECT id FROM couch_templates WHERE name='" . $db->real_escape_string($templateName) . "' LIMIT 1")->fetch_assoc()['id'];
    $pageId = (int) $db->query("SELECT id FROM couch_pages WHERE template_id={$tid} ORDER BY id LIMIT 1")->fetch_assoc()['id'];
    echo "template {$templateName} page {$pageId}\n";
    foreach ($sections as $repeatable => $paths) {
        $fieldId = (int) $db->query(
            "SELECT id FROM couch_fields WHERE template_id={$tid} AND name='" . $db->real_escape_string($repeatable) . "' LIMIT 1"
        )->fetch_assoc()['id'];
        upsert($db, $pageId, $fieldId, $paths);
    }
}

require __DIR__ . '/clear-couch-cache-cli.php';
