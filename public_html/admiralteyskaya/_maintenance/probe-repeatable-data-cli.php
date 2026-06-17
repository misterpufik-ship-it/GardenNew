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

function dump_page_repeatable($db, $fields, $text, $pageId, $label)
{
    echo "\n=== {$label} (page #{$pageId}) ===\n";

    $res = $db->query(
        "SELECT f.id, f.name, f.k_group, f.k_type, t.value
         FROM `{$text}` t
         JOIN `{$fields}` f ON f.id = t.field_id
         WHERE t.page_id=" . (int)$pageId . "
         ORDER BY f.id, t.id"
    );

    $count = 0;
    while ($res && ($row = $res->fetch_assoc())) {
        $count++;
        $value = strlen($row['value']) > 100 ? substr($row['value'], 0, 100) . '...' : $row['value'];
        echo "  [{$row['id']}] {$row['name']} (group={$row['k_group']}, type={$row['k_type']}): {$value}\n";
    }
    echo "Total rows: {$count}\n";

    $fieldRes = $db->query("SELECT id, name, _html FROM `{$fields}` WHERE name IN ('final_gallery_items','gallery_items')");
    while ($fieldRes && ($field = $fieldRes->fetch_assoc())) {
        echo "Field {$field['name']} (#{$field['id']}) _html length: " . strlen($field['_html']) . "\n";
        if ($field['_html']) {
            echo "  _html preview: " . substr(str_replace("\r\n", ' ', $field['_html']), 0, 200) . "\n";
        }
    }
}

dump_page_repeatable($db, $fields, $text, 8, 'filial.php');
dump_page_repeatable($db, $fields, $text, 36, 'udelnaya/filial.php');
dump_page_repeatable($db, $fields, $text, 3, 'gallery.php');

foreach (array(88, 757, 758, 759, 544, 763, 764, 765) as $fieldId) {
    $res = $db->query("SELECT page_id, LENGTH(value) AS len FROM `{$text}` WHERE field_id=" . (int)$fieldId);
    $total = 0;
    while ($res && ($row = $res->fetch_assoc())) {
        $total++;
        echo "field #{$fieldId} page #{$row['page_id']} len={$row['len']}\n";
    }
    if (!$total) {
        echo "field #{$fieldId}: no rows in couch_data_text\n";
    }
}

$fieldRes = $db->query("SELECT id, name, _html FROM `{$fields}` WHERE name IN ('gallery_interior_items','gallery_menu_items','gallery_vibe_items')");
while ($fieldRes && ($field = $fieldRes->fetch_assoc())) {
    echo "\nField {$field['name']} (#{$field['id']})\n";
    echo "  _html: " . str_replace("\r\n", ' ', substr($field['_html'], 0, 220)) . "\n";
    $childRes = $db->query("SELECT id, name, k_type, k_group FROM `{$fields}` WHERE k_group=" . $db->real_escape_string($field['name']) . " OR (template_id=(SELECT template_id FROM `{$fields}` WHERE id=" . (int)$field['id'] . ") AND k_group='" . $db->real_escape_string($field['name']) . "')");
    while ($childRes && ($child = $childRes->fetch_assoc())) {
        echo "  child {$child['name']} type={$child['k_type']} group={$child['k_group']}\n";
    }

    $dataRes = $db->query("SELECT LENGTH(value) AS len, LEFT(value, 120) AS preview FROM `{$text}` WHERE page_id=3 AND field_id=" . (int)$field['id'] . " LIMIT 1");
    if ($dataRes && ($data = $dataRes->fetch_assoc())) {
        echo "  page data len={$data['len']} preview={$data['preview']}\n";
    } else {
        echo "  page data: none\n";
    }
}
