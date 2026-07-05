<?php
/**
 * Restore missing visual menu rows from taplink-import-data.json (merge by title).
 * Keeps existing rows, photos and descriptions; appends only missing items.
 * ?token=gl-cache-clear-20260623&confirm=restore
 * Optional: &branch=admiralteyskaya|udelnaya|both (default both)
 * Optional: &sections=kitchen,bar (default kitchen,bar)
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit("Forbidden\n");
}
if ((isset($_GET['confirm']) ? $_GET['confirm'] : '') !== 'restore') {
    exit("Add &confirm=restore to run\n");
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

$dataPath = __DIR__ . '/../menu/visual/taplink-import-data.json';
if (!is_file($dataPath)) {
    exit("Missing taplink-import-data.json\n");
}
$payload = json_decode(file_get_contents($dataPath), true);
if (!is_array($payload)) {
    exit("Invalid taplink JSON\n");
}

$branchMap = array(
    'admiralteyskaya' => 'menu/visual/index.php',
    'udelnaya' => 'udelnaya/menu/visual/index.php',
);

$branchArg = isset($_GET['branch']) ? (string) $_GET['branch'] : 'both';
if ($branchArg === 'both') {
    $targetBranches = array_keys($branchMap);
} elseif (isset($branchMap[$branchArg])) {
    $targetBranches = array($branchArg);
} else {
    exit("Unknown branch: {$branchArg}\n");
}

$sectionMap = array(
    'kitchen' => array('source' => 'kitchen', 'field' => 'menu_kitchen'),
    'bar' => array('source' => 'bar', 'field' => 'menu_bar'),
);

$sectionsArg = isset($_GET['sections']) ? (string) $_GET['sections'] : 'kitchen,bar';
$sectionKeys = array();
foreach (explode(',', $sectionsArg) as $part) {
    $part = trim($part);
    if ($part !== '' && isset($sectionMap[$part])) {
        $sectionKeys[] = $part;
    }
}
if (!$sectionKeys) {
    $sectionKeys = array('kitchen', 'bar');
}

function gl_restore_norm_title($title)
{
    $title = trim(html_entity_decode((string) $title, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $title = preg_replace('/\s+/u', ' ', $title);
    return mb_strtolower($title, 'UTF-8');
}

function gl_restore_row_from_plain($item)
{
    $row = array();
    foreach (array('item_title', 'item_tag', 'item_price', 'item_weight', 'item_img', 'item_desc') as $key) {
        $value = isset($item[$key]) ? (string) $item[$key] : '';
        if ($key === 'item_tag' && $value === '') {
            $value = '-';
        }
        $row[$key] = gl_sync_encode_field_value($value);
    }
    return $row;
}

echo "Restore missing visual menu items from taplink JSON\n";
echo str_repeat('=', 60) . "\n";
echo "branches=" . implode(',', $targetBranches) . " sections=" . implode(',', $sectionKeys) . "\n\n";

$totalAdded = 0;

foreach ($targetBranches as $branchName) {
    $visualTpl = $branchMap[$branchName];
    $visualPageId = gl_sync_get_master_page_id($db, $mmTemplates, $mmPages, $visualTpl);
    if (!$visualPageId) {
        echo "Branch {$branchName}: visual page not found\n";
        continue;
    }
    echo "== {$branchName} page={$visualPageId} ==\n";

    foreach ($sectionKeys as $label) {
        $cfg = $sectionMap[$label];
        $sourceRows = isset($payload[$cfg['source']]) ? $payload[$cfg['source']] : array();
        if (!is_array($sourceRows)) {
            $sourceRows = array();
        }

        $fieldId = gl_sync_get_repeatable_field_id($db, $mmFields, $visualTpl, $cfg['field']);
        if (!$fieldId) {
            echo "  {$label}: field {$cfg['field']} not found\n";
            continue;
        }

        list($format, $existingRows) = gl_sync_read_repeatable_rows($db, $mmDataText, $visualPageId, $fieldId);
        $existingTitles = array();
        foreach ($existingRows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $title = gl_sync_row_value($row, 'item_title');
            if ($title !== '') {
                $existingTitles[gl_restore_norm_title($title)] = true;
            }
        }

        $added = 0;
        $addedNames = array();
        foreach ($sourceRows as $item) {
            if (!is_array($item)) {
                continue;
            }
            $title = trim((string) ($item['item_title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $norm = gl_restore_norm_title($title);
            if (isset($existingTitles[$norm])) {
                continue;
            }

            $existingRows[] = gl_restore_row_from_plain($item);
            $existingTitles[$norm] = true;
            $added++;
            $addedNames[] = $title;
        }

        echo "  {$label}: was=" . (count($existingRows) - $added) . " source=" . count($sourceRows) . " added={$added}\n";
        foreach ($addedNames as $name) {
            echo "    + {$name}\n";
        }

        if ($added > 0) {
            gl_sync_write_repeatable_rows($db, $mmDataText, $visualPageId, $fieldId, $existingRows, $format);
            $totalAdded += $added;
        }
    }
    echo "\n";
}

$cacheDir = dirname($config) . '/cache';
$removed = 0;
if (is_dir($cacheDir)) {
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cacheDir, FilesystemIterator::SKIP_DOTS)) as $item) {
        if ($item->isDir() || basename($item->getPathname()) === '.htaccess') {
            continue;
        }
        if (@unlink($item->getPathname())) {
            $removed++;
        }
    }
}

echo "\nDone. Total added={$totalAdded}, cache removed={$removed}\n";
echo "Run sync-visual-menu-descriptions-web.php next for descriptions.\n";
