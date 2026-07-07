<?php
/**
 * Generate LCP hero WebP variants (desk 1280 / mob 768).
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/optimize-hero-lcp-web.php?token=gl-cache-clear-20260623
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit("Forbidden\n");
}

header('Content-Type: text/plain; charset=utf-8');

require_once dirname(__DIR__, 2) . '/age-gate/hero-helpers.php';

$imageDir = dirname(__DIR__) . '/couch/uploads/image';
$results = gl_hero_optimize_variants($imageDir);

foreach ($results as $line) {
    echo $line . "\n";
}

$check = array(
    'garden-main.webp',
    'garden-main-mobile.webp',
    'garden-main-desk-1280.webp',
    'garden-main-mob-768.webp',
    'kalyannaya-garden-lounge-udelnaya-interer-spb.webp',
    'kalyannaya-garden-lounge-udelnaya-interer-spb-desk-1280.webp',
    'kalyannaya-garden-lounge-udelnaya-interer-spb-mob-768.webp',
    'main-mobile_1.webp',
    'main-mobile_1-mob-768.webp',
);

echo "\nSizes:\n";
foreach ($check as $name) {
    $path = $imageDir . '/' . $name;
    if (!is_file($path)) {
        echo $name . ": missing\n";
        continue;
    }
    $size = @getimagesize($path);
    $dim = is_array($size) ? ($size[0] . 'x' . $size[1]) : '?';
    echo $name . ': ' . filesize($path) . ' bytes, ' . $dim . "\n";
}

echo "\nDone\n";
