<?php
if (PHP_SAPI !== 'cli' && !defined('GL_SKIP_CLI_CHECK')) {
    http_response_code(403);
    exit("CLI only\n");
}

$config = __DIR__ . '/../couch/config.php';
if (!is_file($config)) {
    fwrite(STDERR, "CouchCMS config not found: {$config}\n");
    exit(1);
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

if (isset($FUNCS) && method_exists($FUNCS, 'invalidate_cache')) {
    $FUNCS->invalidate_cache();
}

echo "CouchCMS cache cleared ({$removed} files removed).\n";
