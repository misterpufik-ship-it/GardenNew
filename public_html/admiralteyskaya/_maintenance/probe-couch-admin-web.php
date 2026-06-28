<?php
if ((isset($_GET['key']) ? $_GET['key'] : '') !== md5('garden-probe-admin-cms')) {
    http_response_code(403);
    exit("Forbidden\n");
}
header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

$errors = array();
set_error_handler(function ($errno, $errstr, $errfile, $errline) use (&$errors) {
    $errors[] = "[$errno] $errstr in $errfile:$errline";
    return false;
});
register_shutdown_function(function () use (&$errors) {
    $e = error_get_last();
    if ($e && in_array($e['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR), true)) {
        $errors[] = "[fatal] {$e['message']} in {$e['file']}:{$e['line']}";
    }
    if ($errors) {
        echo "\nErrors:\n" . implode("\n", $errors) . "\n";
    }
});

$root = realpath(__DIR__ . '/..');
define('K_ADMIN', 1);
define('K_COUCH_DIR', $root . '/couch/');
require_once K_COUCH_DIR . 'header.php';

echo "header ok\n";
echo "theme: " . (defined('K_THEME_NAME') ? K_THEME_NAME : 'none') . "\n";

$loginTpl = K_THEME_DIR . 'login.html';
echo "login exists: " . (is_file($loginTpl) ? 'yes' : 'no') . "\n";
if (is_file($loginTpl)) {
    echo "login line1: " . trim(strtok(file_get_contents($loginTpl), "\n")) . "\n";
}

$html = $FUNCS->render('login');
echo "render login length: " . strlen($html) . "\n";
if (strlen($html) < 400) {
    echo $html . "\n";
}
