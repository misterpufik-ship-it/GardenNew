<?php
if ((isset($_GET['key']) ? $_GET['key'] : '') !== md5('garden-probe-admin-cms')) {
    http_response_code(403);
    exit("Forbidden\n");
}
header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

register_shutdown_function(function () {
    $e = error_get_last();
    if ($e && in_array($e['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR), true)) {
        echo "\n[fatal] {$e['message']} in {$e['file']}:{$e['line']}\n";
    }
});

$root = realpath(__DIR__ . '/..');
define('K_COUCH_DIR', $root . '/couch/');
require_once K_COUCH_DIR . 'header.php';

echo "header ok, user id: " . $AUTH->user->id . "\n";

ob_start();
try {
    $AUTH->show_login(null);
    $out = ob_get_clean();
    echo "show_login output length: " . strlen($out) . "\n";
    if (strlen($out) < 800) {
        echo $out . "\n";
    } else {
        echo substr($out, 0, 400) . "\n...\n";
    }
} catch (Throwable $e) {
    ob_end_clean();
    echo "Throwable: " . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine() . "\n";
}
