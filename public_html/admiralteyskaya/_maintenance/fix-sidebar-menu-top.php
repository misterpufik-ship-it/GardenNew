<?php
$root = dirname(__DIR__);
$stylesPath = $root . '/couch/theme/garden/styles.css';
$kfnPath = $root . '/couch/addons/kfunctions.php';

$styles = file_get_contents($stylesPath);
$styles = preg_replace(
    '/#scroll-sidebar\{position:absolute!important;top:\d+px!important;right:0;left:0;bottom:132px!important;overflow-y:auto\}/',
    '#scroll-sidebar{position:absolute!important;top:0!important;right:0;left:0;bottom:132px!important;overflow-y:auto}',
    $styles
);
$styles = preg_replace(
    '/@media \(max-height:540px\)\{#scroll-sidebar\{top:\d+px!important;bottom:124px!important\}\}/',
    '@media (max-height:540px){#scroll-sidebar{top:0!important;bottom:124px!important}}',
    $styles
);
$styles = preg_replace(
    '/#menu-wrap \.garden-admin-brand\{padding:10px 10px 6px\}/',
    '#menu-wrap .garden-admin-brand{padding:8px 10px 4px}',
    $styles
);
file_put_contents($stylesPath, $styles);

$kfn = file_get_contents($kfnPath);
$kfn = preg_replace(
    '/#scroll-sidebar\{position:absolute!important;top:\d+px!important;right:0;left:0;bottom:132px!important;overflow-y:auto\}/',
    '#scroll-sidebar{position:absolute!important;top:0!important;right:0;left:0;bottom:132px!important;overflow-y:auto}',
    $kfn
);
$kfn = preg_replace(
    '/@media \(max-height:540px\)\{#scroll-sidebar\{top:\d+px!important;bottom:124px!important\}\}/',
    '@media (max-height:540px){#scroll-sidebar{top:0!important;bottom:124px!important}}',
    $kfn
);
$kfn = preg_replace(
    '/#menu-wrap \.garden-admin-brand\{padding:10px 10px 6px\}/',
    '#menu-wrap .garden-admin-brand{padding:8px 10px 4px}',
    $kfn
);
file_put_contents($kfnPath, $kfn);

passthru('php -l ' . escapeshellarg($kfnPath), $code);
echo "Sidebar menu pinned to top under logo\n";
exit($code);
