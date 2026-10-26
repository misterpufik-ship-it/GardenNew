<?php
/**
 * Translate visual menu descriptions to Russian (titles unchanged)
 * and split enumerations onto separate lines.
 *
 * Dry-run: ?token=gl-cache-clear-20260623
 * Apply:   ?token=gl-cache-clear-20260623&confirm=apply
 */
if (!defined('GL_FORMAT_LIB_ONLY')) {
    $token = isset($_GET['token']) ? (string) $_GET['token'] : '';
    if ($token !== 'gl-cache-clear-20260623') {
        http_response_code(403);
        exit("Forbidden\n");
    }

    header('Content-Type: text/plain; charset=utf-8');
    ini_set('display_errors', '1');
    error_reporting(E_ALL);

    $apply = (isset($_GET['confirm']) ? (string) $_GET['confirm'] : '') === 'apply';

    define('GL_SYNC_LIB_ONLY', true);
    require_once __DIR__ . '/sync-visual-menu-descriptions-web.php';
}

function gl_desc_ucfirst($text)
{
    $text = trim((string) $text);
    if ($text === '') {
        return $text;
    }
    if (preg_match('/^\d/u', $text)) {
        return $text;
    }

    return mb_strtoupper(mb_substr($text, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($text, 1, null, 'UTF-8');
}

function gl_desc_parts_are_short(array $parts)
{
    if (count($parts) < 2) {
        return false;
    }
    foreach ($parts as $part) {
        $words = preg_split('/\s+/u', trim($part));
        $words = array_values(array_filter($words));
        if (count($words) === 0 || count($words) > 7 || mb_strlen($part, 'UTF-8') > 56) {
            return false;
        }
    }

    return true;
}

function gl_desc_translate($text)
{
    $phrases = array(
        'gigner ale' => 'имбирный эль',
        'ginger ale' => 'имбирный эль',
        'red currant' => 'красная смородина',
        'sakura tonic' => 'тоник сакура',
        'lemon-lime' => 'лимон-лайм',
        'lemon‑lime' => 'лимон-лайм',
        'semi-sweet' => 'полусладкое',
        'semi sweet' => 'полусладкое',
        'semi-dry' => 'полусухое',
        'semi dry' => 'полусухое',
        'pedro ximénez' => 'Pedro Ximénez',
        'pedro ximenez' => 'Pedro Ximénez',
        'domaine des hates' => 'Domaine des Hates',
        'serafini & vidotto' => 'Serafini & Vidotto',
        'paolo leo' => 'Paolo Leo',
    );
    $words = array(
        'mango' => 'манго',
        'peach' => 'персик',
        'apple' => 'яблоко',
        'tomato' => 'томат',
        'classic' => 'классик',
        'zero' => 'зеро',
        'tonic' => 'тоник',
        'pink' => 'розовое',
        'white' => 'белое',
        'red' => 'красное',
        'dry' => 'сухое',
        'brut' => 'брют',
        'brutus' => 'брют',
        'russia' => 'россия',
        'italy' => 'италия',
        'spain' => 'испания',
        'chile' => 'чили',
        'germany' => 'германия',
        'france' => 'франция',
        'portugal' => 'португалия',
        'moselle' => 'мозель',
        'burgundy' => 'бургундия',
        'puglia' => 'апулия',
        'veneto' => 'венето',
        'summer' => 'лето',
        'autumn' => 'осень',
        'winter' => 'зима',
        'spring' => 'весна',
        'elderflower' => 'бузина',
    );

    foreach ($phrases as $from => $to) {
        $text = preg_replace('/' . preg_quote($from, '/') . '/ui', $to, $text);
    }
    foreach ($words as $from => $to) {
        $text = preg_replace('/\b' . preg_quote($from, '/') . '\b/ui', $to, $text);
    }

    return $text;
}

function gl_desc_protect($text)
{
    $map = array();
    $text = preg_replace_callback('/\bб\/[аaгg]\b/ui', function ($m) use (&$map) {
        $key = '%%GLP' . count($map) . '%%';
        $map[$key] = $m[0];
        return $key;
    }, $text);
    $text = preg_replace_callback('/\b\d+(?:[.,]\d+)?\s*\/\s*\d+(?:[.,]\d+)?(?:\s*(?:мл|ml|л|l))?\b/ui', function ($m) use (&$map) {
        $key = '%%GLP' . count($map) . '%%';
        $map[$key] = $m[0];
        return $key;
    }, $text);

    return array($text, $map);
}

function gl_desc_unprotect($text, array $map)
{
    if (!$map) {
        return $text;
    }

    return strtr($text, $map);
}

function gl_desc_format_block($block)
{
    $block = trim($block);
    if ($block === '') {
        return '';
    }

    list($protected, $map) = gl_desc_protect($block);
    if (preg_match('/^%%GLP\d+%%$/u', trim($protected))) {
        return gl_desc_unprotect(trim($protected), $map);
    }
    $hasSlash = mb_strpos($protected, '/') !== false;
    if ($hasSlash) {
        $joined = preg_replace('/\s+/u', ' ', str_replace(array("\r\n", "\n", "\r"), ' ', $protected));
        $parts = preg_split('/\s*\/\s*/u', $joined);
        $parts = array_values(array_filter(array_map('trim', $parts), 'strlen'));
        if (gl_desc_parts_are_short($parts)) {
            $lines = array();
            foreach ($parts as $part) {
                $lines[] = gl_desc_ucfirst(gl_desc_unprotect($part, $map));
            }

            return implode("\n", $lines);
        }
    }

    $plain = gl_desc_unprotect($protected, $map);
    if (mb_strpos($plain, ',') !== false && !preg_match('/[.!?](\s|$)/u', $plain)) {
        $parts = preg_split('/\s*,\s*/u', $plain);
        $parts = array_values(array_filter(array_map('trim', $parts), 'strlen'));
        if (gl_desc_parts_are_short($parts)) {
            $lines = array();
            foreach ($parts as $part) {
                $lines[] = gl_desc_ucfirst($part);
            }

            return implode("\n", $lines);
        }
    }

    return $plain;
}

function gl_format_visual_desc($desc)
{
    $desc = html_entity_decode((string) $desc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $desc = str_replace("\r\n", "\n", $desc);
    $desc = str_replace("\r", "\n", $desc);
    $desc = trim($desc);
    if ($desc === '') {
        return '';
    }

    $desc = gl_desc_translate($desc);
    $blocks = preg_split("/\n{2,}/u", $desc);
    $formatted = array();
    foreach ($blocks as $block) {
        $out = gl_desc_format_block($block);
        if ($out !== '') {
            $formatted[] = $out;
        }
    }

    return trim(implode("\n\n", $formatted));
}

if (defined('GL_FORMAT_LIB_ONLY')) {
    return;
}

$branches = array(
    'admiralteyskaya' => 'menu/visual/index.php',
    'udelnaya' => 'udelnaya/menu/visual/index.php',
);
$sections = array('menu_shisha', 'menu_kitchen', 'menu_bar');

echo "Format visual descriptions: Russian + line breaks for lists\n";
echo str_repeat('=', 60) . "\n";
echo $apply ? "Mode: APPLY\n\n" : "Mode: dry-run (add &confirm=apply to write)\n\n";

$total = 0;
foreach ($branches as $branch => $tpl) {
    $pageId = gl_sync_get_master_page_id($db, $templates, $pages, $tpl);
    echo "== {$branch} page={$pageId} ==\n";
    foreach ($sections as $fieldName) {
        $fieldId = gl_sync_get_repeatable_field_id($db, $fields, $tpl, $fieldName);
        if (!$fieldId) {
            echo "  {$fieldName}: missing\n";
            continue;
        }
        list($format, $rows) = gl_sync_read_repeatable_rows($db, $dataText, $pageId, $fieldId);
        $updated = 0;
        $examples = array();
        foreach ($rows as $idx => $row) {
            if (!is_array($row)) {
                continue;
            }
            if (trim(gl_sync_row_value($row, 'item_type')) === 'header') {
                continue;
            }
            $old = gl_sync_row_value($row, 'item_desc');
            if ($old === '') {
                continue;
            }
            $new = gl_format_visual_desc($old);
            if ($new === '' || $new === $old) {
                continue;
            }
            gl_sync_set_row_value($rows[$idx], 'item_desc', $new);
            $updated++;
            if (count($examples) < 8) {
                $title = gl_sync_prepare_name(gl_sync_row_value($row, 'item_title'));
                $examples[] = $title . " :: " . str_replace("\n", ' | ', $new);
            }
        }
        echo "  {$fieldName}: would_update={$updated}\n";
        foreach ($examples as $example) {
            echo "    - {$example}\n";
        }
        if ($apply && $updated > 0) {
            gl_sync_write_repeatable_rows($db, $dataText, $pageId, $fieldId, $rows, $format);
            echo "    saved {$updated}\n";
        }
        $total += $updated;
    }
    echo "\n";
}

echo str_repeat('=', 60) . "\n";
echo "Done. " . ($apply ? 'updated' : 'would_update') . "={$total}\n";
if (!$apply && $total > 0) {
    echo "Re-run with &confirm=apply to write.\n";
}

$cacheDir = dirname($config) . '/cache';
$removed = 0;
if ($apply && is_dir($cacheDir)) {
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cacheDir, FilesystemIterator::SKIP_DOTS)) as $item) {
        if ($item->isDir() || basename($item->getPathname()) === '.htaccess') {
            continue;
        }
        if (@unlink($item->getPathname())) {
            $removed++;
        }
    }
    echo "Cache files removed: {$removed}\n";
}
