<?php
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit("Forbidden\n");
}
header('Content-Type: text/plain; charset=utf-8');
require __DIR__ . '/patch-admin-menu-icons.php';

$cacheDir = dirname(__DIR__) . '/couch/cache';
if (is_dir($cacheDir)) {
    foreach (glob($cacheDir . '/*') as $file) {
        if (is_file($file) && basename($file) !== '.htaccess') {
            @unlink($file);
        }
    }
    echo "Couch cache cleared\n";
}
