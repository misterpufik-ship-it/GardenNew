<?php
if ((isset($_GET['key']) ? $_GET['key'] : '') !== md5('garden-probe-admin')) {
    http_response_code(403);
    exit("Forbidden\n");
}
header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

$config = __DIR__ . '/../couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;
require_once K_COUCH_DIR . 'functions.php';

echo "config ok\n";
if (is_file(K_COUCH_DIR . 'addons/kfunctions.php')) {
    require_once K_COUCH_DIR . 'addons/kfunctions.php';
    echo "kfunctions ok\n";
}

$login = K_COUCH_DIR . 'theme/garden/login.html';
echo "login first line: " . trim(strtok(file_get_contents($login), "\n")) . "\n";

if (class_exists('COUCH')) {
    echo "COUCH class exists\n";
}

echo "done\n";
