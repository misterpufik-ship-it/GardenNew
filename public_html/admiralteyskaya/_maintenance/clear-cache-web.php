<?php
if ((isset($_GET['token']) ? $_GET['token'] : '') !== 'gl-cache-clear-20260623') {
    http_response_code(404);
    exit;
}

$config = __DIR__ . '/../couch/config.php';
if (!is_file($config)) {
    http_response_code(500);
    exit('config missing');
}

define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;
require_once K_COUCH_DIR . 'functions.php';

$cacheDir = K_COUCH_DIR . 'cache';
$removed = 0;
if (is_dir($cacheDir)) {
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cacheDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($items as $item) {
        $path = $item->getPathname();
        if ($item->isDir()) {
            if ($path !== $cacheDir && basename($path) !== 'booking-throttle') {
                @rmdir($path);
            }
            continue;
        }
        if (basename($path) === '.htaccess') {
            continue;
        }
        if (@unlink($path)) {
            $removed++;
        }
    }
}

if (is_file(K_COUCH_DIR . 'addons/garden-cache/cache-lib.php')) {
    require_once K_COUCH_DIR . 'addons/garden-cache/cache-lib.php';
    if (function_exists('garden_clear_couch_cache')) {
        $removed += (int)garden_clear_couch_cache();
    }
}

global $FUNCS;
if (isset($FUNCS) && method_exists($FUNCS, 'invalidate_cache')) {
    $FUNCS->invalidate_cache();
}

header('Content-Type: text/plain; charset=utf-8');
echo "CouchCMS cache cleared ({$removed} files removed).\n";
