<?php
/**
 * Remove empty black admin header strip on form/settings pages.
 */
$root = dirname(__DIR__);

$replacements = array(
    ':root{--gl-admin-header:66px;--gl-admin-sidebar-footer:119px}' =>
        ':root{--gl-admin-header:0;--gl-admin-sidebar-footer:119px}',

    "#header{background:var(--gl-black)!important;border-bottom:1px solid var(--gl-border)!important;padding:14px 24px 0!important}\n#header-inner{padding-bottom:14px!important;min-height:38px}" =>
        "#header{display:none!important;padding:0!important;border:0!important;background:transparent!important}\n#header:has(.btn-group a.btn){display:block!important;padding:10px 24px 8px!important;background:var(--gl-black)!important;border-bottom:1px solid var(--gl-border)!important}\n#header-inner{padding:0!important;min-height:0!important}",

    '#content{background:#fff;min-height:calc(100vh - var(--gl-admin-header) - var(--gl-admin-sidebar-footer));padding:18px 24px 28px;border-top:0}' =>
        '#content{background:#fff;min-height:calc(100vh - var(--gl-admin-sidebar-footer));padding:18px 24px 28px;border-top:0}',

    "#header{background:#0a0a0a!important;border-bottom:1px solid rgba(197,160,89,.28)!important;padding:14px 24px 0!important}\n#header-inner{padding-bottom:14px!important;min-height:38px}" =>
        "#header{display:none!important;padding:0!important;border:0!important;background:transparent!important}\n#header:has(.btn-group a.btn){display:block!important;padding:10px 24px 8px!important;background:#0a0a0a!important;border-bottom:1px solid rgba(197,160,89,.28)!important}\n#header-inner{padding:0!important;min-height:0!important}",

    '#content{background:#fff;min-height:calc(100vh - var(--gl-admin-header) - var(--gl-admin-sidebar-footer));padding:18px 24px 28px;border-top:0}' =>
        '#content{background:#fff;min-height:calc(100vh - var(--gl-admin-sidebar-footer));padding:18px 24px 28px;border-top:0}',
);

$paths = array(
    $root . '/couch/theme/garden/styles.css',
    $root . '/couch/addons/kfunctions.php',
);

foreach ($paths as $path) {
    $content = file_get_contents($path);
    $original = $content;
    foreach ($replacements as $old => $new) {
        $oldCrlf = str_replace("\n", "\r\n", $old);
        $newCrlf = str_replace("\n", "\r\n", $new);
        if (strpos($content, $oldCrlf) !== false) {
            $content = str_replace($oldCrlf, $newCrlf, $content);
        } elseif (strpos($content, $old) !== false) {
            $content = str_replace($old, $new, $content);
        }
    }
    if ($content === $original) {
        if (strpos($content, '#header:has(.btn-group a.btn)') !== false) {
            echo basename($path) . " already patched\n";
            continue;
        }
        fwrite(STDERR, basename($path) . ": nothing changed\n");
        exit(1);
    }
    file_put_contents($path, $content);
    echo "Updated " . basename($path) . "\n";
}

passthru('php -l ' . escapeshellarg($root . '/couch/addons/kfunctions.php'), $code);
exit($code ?? 0);
