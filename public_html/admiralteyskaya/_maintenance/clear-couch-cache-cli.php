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
require_once K_COUCH_DIR . 'addons/garden-cache/cache-lib.php';

$removed = garden_clear_couch_cache();

echo "CouchCMS cache cleared ({$removed} files removed).\n";
