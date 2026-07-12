<?php
if ((isset($_GET['token']) ? $_GET['token'] : '') !== 'gl-cache-clear-20260623') {
    http_response_code(404);
    exit;
}
ini_set('display_errors', '1');
error_reporting(E_ALL);
header('Content-Type: text/plain; charset=utf-8');

register_shutdown_function(function () {
    $e = error_get_last();
    if ($e && in_array($e['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR), true)) {
        echo "\n[fatal] {$e['message']} in {$e['file']}:{$e['line']}\n";
    }
});

$root = realpath(__DIR__ . '/..');
if (!$root) {
    exit("root missing\n");
}
chdir($root);

define('K_COUCH_DIR', $root . '/couch/');
require_once K_COUCH_DIR . 'header.php';
echo "header ok\n";

require_once $_SERVER['DOCUMENT_ROOT'] . '/age-gate/assets.php';
echo "assets ok\n";

require_once K_COUCH_DIR . 'addons/kfunctions.php';
echo "kfunctions ok\n";

require_once K_COUCH_DIR . 'addons/garden-cache/cache-lib.php';
echo "cache-lib ok\n";

echo "OK\n";
