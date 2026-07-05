<?php
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

function q1($db, $sql) {
    $res = $db->query($sql);
    return $res ? $res->fetch_assoc() : null;
}

$tplNames = array(
    'admiral text' => 'menu/text/index.php',
    'admiral visual' => 'menu/visual/index.php',
    'udelnaya text' => 'udelnaya/menu/text/index.php',
    'udelnaya visual' => 'udelnaya/menu/visual/index.php',
);

foreach ($tplNames as $label => $tplName) {
    $tplEsc = $db->real_escape_string($tplName);
    $tpl = q1($db, "SELECT id FROM `{$templates}` WHERE name='{$tplEsc}' LIMIT 1");
    if (!$tpl) { echo "{$label}: template missing\n"; continue; }
    $tplId = (int) $tpl['id'];
    $page = q1($db, "SELECT id, page_title FROM `{$pages}` WHERE template_id={$tplId} ORDER BY is_master DESC, id ASC LIMIT 1");
    if (!$page) { echo "{$label}: page missing\n"; continue; }
    $pageId = (int) $page['id'];
    echo "\n{$label} template={$tplId} page={$pageId} ({$page['page_title']})\n";

    $res = $db->query(
        "SELECT f.id, f.name, f.k_type, f.k_group, dt.value " .
        "FROM `{$fields}` f " .
        "LEFT JOIN `{$dataText}` dt ON dt.field_id=f.id AND dt.page_id={$pageId} " .
        "WHERE f.template_id={$tplId} AND (f.k_type='repeatable' OR f.name IN ('rep_hookahs_v2','rep_kitchen_v2','rep_bar_alc_v2','menu_shisha','menu_kitchen','menu_bar')) " .
        "ORDER BY f.k_order"
    );
    if (!$res) {
        echo "  query error: {$db->error}\n";
        continue;
    }
    while ($res && ($row = $res->fetch_assoc())) {
        $val = (string) $row['value'];
        echo "  repeatable {$row['name']}: value_len=" . strlen($val);
        if ($val !== '') {
            $decoded = @json_decode($val, true);
            if (is_array($decoded)) {
                echo " json_rows=" . count($decoded);
            }
        }
        echo "\n";
    }

    $childRes = $db->query(
        "SELECT f.k_group, f.name, COUNT(dt.id) AS cnt, SUM(LENGTH(dt.value)) AS total_len " .
        "FROM `{$fields}` f " .
        "JOIN `{$dataText}` dt ON dt.field_id=f.id AND dt.page_id={$pageId} " .
        "WHERE f.template_id={$tplId} AND f.k_group IN ('rep_hookahs_v2','rep_kitchen_v2','rep_bar_alc_v2','menu_shisha','menu_kitchen','menu_bar') " .
        "GROUP BY f.k_group, f.name ORDER BY f.k_group, f.name"
    );
    while ($childRes && ($row = $childRes->fetch_assoc())) {
        echo "    {$row['k_group']}.{$row['name']}: records={$row['cnt']} total_len={$row['total_len']}\n";
    }

    $sampleRes = $db->query(
        "SELECT f.k_group, f.name, dt.value " .
        "FROM `{$fields}` f " .
        "JOIN `{$dataText}` dt ON dt.field_id=f.id AND dt.page_id={$pageId} " .
        "WHERE f.template_id={$tplId} AND f.name IN ('i_name','kit_name','item_title','i_desc','kit_desc','item_desc','i_subheader') " .
        "AND dt.value != '' ORDER BY f.k_group, f.id LIMIT 12"
    );
    while ($sampleRes && ($row = $sampleRes->fetch_assoc())) {
        $val = mb_substr((string) $row['value'], 0, 80, 'UTF-8');
        echo "    sample {$row['k_group']}.{$row['name']}: {$val}\n";
    }
}
