<?php
/**
 * Report visual menu rows: counts, empty descriptions, taplink diff.
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

$mmTemplates = K_DB_TABLES_PREFIX . 'couch_templates';
$mmPages = K_DB_TABLES_PREFIX . 'couch_pages';
$mmFields = K_DB_TABLES_PREFIX . 'couch_fields';
$mmDataText = K_DB_TABLES_PREFIX . 'couch_data_text';

define('GL_SYNC_LIB_ONLY', true);
require_once __DIR__ . '/sync-visual-menu-descriptions-web.php';

$branches = array(
    'admiralteyskaya' => 'menu/visual/index.php',
    'udelnaya' => 'udelnaya/menu/visual/index.php',
);

$sections = array(
    'hookahs' => 'menu_shisha',
    'kitchen' => 'menu_kitchen',
    'bar' => 'menu_bar',
);

$taplink = array();
$tapPath = __DIR__ . '/../menu/visual/taplink-import-data.json';
if (is_file($tapPath)) {
    $payload = json_decode(file_get_contents($tapPath), true);
    if (is_array($payload)) {
        $taplink['kitchen'] = isset($payload['kitchen']) ? count($payload['kitchen']) : 0;
        $taplink['bar'] = isset($payload['bar']) ? count($payload['bar']) : 0;
    }
}

function gl_report_norm($title)
{
    $title = trim(html_entity_decode((string) $title, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    return mb_strtolower(preg_replace('/\s+/u', ' ', $title), 'UTF-8');
}

echo "Visual menu report\n";
echo str_repeat('=', 60) . "\n";
if ($taplink) {
    echo "taplink reference: kitchen={$taplink['kitchen']}, bar={$taplink['bar']}\n\n";
}

foreach ($branches as $branch => $tpl) {
    echo "Branch: {$branch} ({$tpl})\n";
    $pageId = gl_sync_get_master_page_id($db, $mmTemplates, $mmPages, $tpl);
    echo "  page_id={$pageId}\n";

    foreach ($sections as $section => $field) {
        $fieldId = gl_sync_get_repeatable_field_id($db, $mmFields, $tpl, $field);
        list(, $rows) = gl_sync_read_repeatable_rows($db, $mmDataText, $pageId, $fieldId);
        $named = 0;
        $emptyDesc = 0;
        $emptyImg = 0;
        $emptyNames = array();
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $title = gl_sync_row_value($row, 'item_title');
            if ($title === '') {
                continue;
            }
            $named++;
            $desc = gl_sync_row_value($row, 'item_desc');
            $img = gl_sync_row_value($row, 'item_img');
            if ($desc === '') {
                $emptyDesc++;
                $emptyNames[] = $title;
            }
            if ($img === '') {
                $emptyImg++;
            }
        }

        $ref = isset($taplink[$section]) ? $taplink[$section] : '-';
        echo "  {$section}: rows={$named} ref={$ref}";
        if ($ref !== '-' && (int) $ref > $named) {
            echo " MISSING=" . ((int) $ref - $named);
        }
        echo " empty_desc={$emptyDesc} empty_img={$emptyImg}\n";
        if ($emptyNames) {
            foreach ($emptyNames as $name) {
                echo "    no desc: {$name}\n";
            }
        }
    }
    echo "\n";
}
