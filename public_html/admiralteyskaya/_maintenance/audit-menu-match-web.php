<?php
/**
 * Audit visual vs text menu matching (read-only).
 * ?token=gl-cache-clear-20260623
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit("Forbidden\n");
}

header('Content-Type: text/plain; charset=utf-8');

$config = __DIR__ . '/../couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ?: 3306;
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
}

$db = new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int) $port);
$db->set_charset('utf8');

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';
$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$dataText = K_DB_TABLES_PREFIX . 'couch_data_text';

define('GL_SYNC_LIB_ONLY', true);
require_once __DIR__ . '/sync-visual-menu-descriptions-web.php';

$branches = array(
    'admiralteyskaya' => array(
        'text' => 'menu/text/index.php',
        'visual' => 'menu/visual/index.php',
    ),
    'udelnaya' => array(
        'text' => 'udelnaya/menu/text/index.php',
        'visual' => 'udelnaya/menu/visual/index.php',
    ),
);

$sections = array(
    'hookahs' => array(
        'text_repeatable' => 'rep_hookahs_v2',
        'text_name' => 'i_name',
        'text_desc' => 'i_desc',
        'text_price' => 'i_price',
        'visual_repeatable' => 'menu_shisha',
        'visual_name' => 'item_title',
        'visual_desc' => 'item_desc',
        'visual_price' => 'item_price',
    ),
    'kitchen' => array(
        'text_repeatable' => 'rep_kitchen_v2',
        'text_name' => 'kit_name',
        'text_desc' => 'kit_desc',
        'text_price' => 'kit_price',
        'visual_repeatable' => 'menu_kitchen',
        'visual_name' => 'item_title',
        'visual_desc' => 'item_desc',
        'visual_price' => 'item_price',
    ),
    'bar' => array(
        'text_repeatable' => 'rep_bar_alc_v2',
        'text_name' => 'i_name',
        'text_desc' => 'i_subheader',
        'text_price' => 'i_price',
        'visual_repeatable' => 'menu_bar',
        'visual_name' => 'item_title',
        'visual_desc' => 'item_desc',
        'visual_price' => 'item_price',
    ),
);

function gl_audit_norm($name)
{
    return gl_sync_norm_name($name);
}

function gl_audit_norm_loose($name)
{
    $name = gl_audit_norm($name);
    $name = preg_replace('/\([^)]*\)/u', ' ', $name);
    $name = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $name);
    $name = preg_replace('/\s+/u', ' ', $name);
    return trim($name);
}

function gl_audit_price_key($price)
{
    $price = preg_replace('/[^\d]/u', '', (string) $price);
    return $price === '' ? '' : ltrim($price, '0');
}

function gl_audit_collect_items($rows, $nameField, $descField, $priceField, $requireDesc)
{
    $items = array();
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $rowType = gl_sync_row_value($row, 'row_type');
        if ($rowType !== '' && $rowType !== 'item') {
            continue;
        }
        $name = gl_sync_row_value($row, $nameField);
        if ($name === '') {
            continue;
        }
        $desc = gl_sync_row_value($row, $descField);
        if ($requireDesc && $desc === '') {
            continue;
        }
        $items[] = array(
            'name' => $name,
            'desc' => $desc,
            'price' => gl_sync_row_value($row, $priceField),
            'norm' => gl_audit_norm($name),
            'loose' => gl_audit_norm_loose($name),
            'price_key' => gl_audit_price_key(gl_sync_row_value($row, $priceField)),
        );
    }
    return $items;
}

echo "Menu match audit\n";
echo str_repeat('=', 60) . "\n";

echo "\nAll menu templates/pages:\n";
$res = $db->query(
    "SELECT t.name tname, p.id pid, p.page_title, p.is_master, LENGTH(dt.value) len " .
    "FROM `{$templates}` t " .
    "JOIN `{$pages}` p ON p.template_id=t.id " .
    "LEFT JOIN `{$fields}` f ON f.template_id=t.id AND f.name IN ('menu_kitchen','rep_kitchen_v2','menu_items_list') " .
    "LEFT JOIN `{$dataText}` dt ON dt.page_id=p.id AND dt.field_id=f.id " .
    "WHERE t.name LIKE '%menu%' " .
    "ORDER BY t.name, p.is_master DESC, p.id"
);
while ($res && ($row = $res->fetch_assoc())) {
    echo "  {$row['tname']} page={$row['pid']} master={$row['is_master']} len=" . (int) $row['len'] . " title={$row['page_title']}\n";
}

foreach ($branches as $branch => $tpls) {
    echo "\nBranch: {$branch}\n";
    $textPageId = gl_sync_get_master_page_id($db, $templates, $pages, $tpls['text']);
    $visualPageId = gl_sync_get_master_page_id($db, $templates, $pages, $tpls['visual']);
    echo "  text_page={$textPageId} visual_page={$visualPageId}\n";

    foreach ($sections as $section => $cfg) {
        $textFieldId = gl_sync_get_repeatable_field_id($db, $fields, $tpls['text'], $cfg['text_repeatable']);
        $visualFieldId = gl_sync_get_repeatable_field_id($db, $fields, $tpls['visual'], $cfg['visual_repeatable']);
        list(, $textRows) = gl_sync_read_repeatable_rows($db, $dataText, $textPageId, $textFieldId);
        list(, $visualRows) = gl_sync_read_repeatable_rows($db, $dataText, $visualPageId, $visualFieldId);

        $textAll = gl_audit_collect_items($textRows, $cfg['text_name'], $cfg['text_desc'], $cfg['text_price'], false);
        $textWithDesc = gl_audit_collect_items($textRows, $cfg['text_name'], $cfg['text_desc'], $cfg['text_price'], true);
        $visualAll = gl_audit_collect_items($visualRows, $cfg['visual_name'], $cfg['visual_desc'], $cfg['visual_price'], false);

        echo "  {$section}: text_rows=" . count($textRows)
            . " text_named=" . count($textAll)
            . " text_with_desc=" . count($textWithDesc)
            . " visual_named=" . count($visualAll) . "\n";

        $exactMap = gl_sync_build_desc_map($textRows, $cfg['text_name'], $cfg['text_desc']);
        $noMatch = array();
        foreach ($visualAll as $v) {
            if (isset($exactMap[$v['norm']])) {
                continue;
            }
            $noMatch[] = $v;
        }

        if ($noMatch) {
            echo "    no_match (" . count($noMatch) . "):\n";
            foreach ($noMatch as $v) {
                $candidates = array();
                foreach ($textWithDesc as $t) {
                    if ($v['loose'] !== '' && $t['loose'] !== '' && ($v['loose'] === $t['loose']
                        || mb_stripos($t['loose'], $v['loose']) !== false
                        || mb_stripos($v['loose'], $t['loose']) !== false)) {
                        $candidates[] = $t['name'];
                    } elseif ($v['price_key'] !== '' && $v['price_key'] === $t['price_key']
                        && similar_text($v['loose'], $t['loose'], $pct) && $pct >= 55) {
                        $candidates[] = $t['name'] . " (price+sim {$pct}%)";
                    }
                }
                $candidates = array_slice(array_unique($candidates), 0, 3);
                echo "      visual: {$v['name']} | price={$v['price']}";
                if ($candidates) {
                    echo " => maybe: " . implode(' ; ', $candidates);
                }
                echo "\n";
            }
        }
    }
}

echo "\nSearch 'говядина' in text kitchen (all branches):\n";
foreach ($branches as $branch => $tpls) {
    $textPageId = gl_sync_get_master_page_id($db, $templates, $pages, $tpls['text']);
    $textFieldId = gl_sync_get_repeatable_field_id($db, $fields, $tpls['text'], 'rep_kitchen_v2');
    list(, $textRows) = gl_sync_read_repeatable_rows($db, $dataText, $textPageId, $textFieldId);
    foreach ($textRows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $name = gl_sync_row_value($row, 'kit_name');
        if ($name !== '' && mb_stripos($name, 'говядина') !== false) {
            $desc = gl_sync_row_value($row, 'kit_desc');
            echo "  [{$branch}] text: {$name} | desc=" . mb_substr($desc, 0, 80) . "\n";
        }
    }
    $visualPageId = gl_sync_get_master_page_id($db, $templates, $pages, $tpls['visual']);
    $visualFieldId = gl_sync_get_repeatable_field_id($db, $fields, $tpls['visual'], 'menu_kitchen');
    list(, $visualRows) = gl_sync_read_repeatable_rows($db, $dataText, $visualPageId, $visualFieldId);
    foreach ($visualRows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $name = gl_sync_row_value($row, 'item_title');
        if ($name !== '' && mb_stripos($name, 'говядина') !== false) {
            $desc = gl_sync_row_value($row, 'item_desc');
            echo "  [{$branch}] visual: {$name} | desc=" . mb_substr($desc, 0, 80) . "\n";
        }
    }
}

echo "\nDuplicate couch_data_text rows (same page+field):\n";
$dup = $db->query(
    "SELECT page_id, field_id, COUNT(*) c FROM `{$dataText}` " .
    "GROUP BY page_id, field_id HAVING c > 1 ORDER BY c DESC LIMIT 20"
);
while ($dup && ($row = $dup->fetch_assoc())) {
    echo "  page={$row['page_id']} field={$row['field_id']} count={$row['c']}\n";
}
