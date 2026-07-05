<?php
/**
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/patch-kfunctions-faq-branches-web.php?token=gl-cache-clear-20260623
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit("Forbidden\n");
}

header('Content-Type: text/plain; charset=utf-8');
require __DIR__ . '/patch-kfunctions-faq-branches.php';

$cacheDir = dirname(__DIR__) . '/couch/cache';
if (is_dir($cacheDir)) {
    foreach (glob($cacheDir . '/*') as $file) {
        if (is_file($file) && basename($file) !== '.htaccess') {
            @unlink($file);
        }
    }
    echo "Couch cache cleared\n";
}
