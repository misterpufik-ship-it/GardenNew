<?php
if ((isset($_GET['key']) ? $_GET['key'] : '') !== md5('garden-probe-admin-cms')) {
    http_response_code(403);
    exit("Forbidden\n");
}
header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

$root = realpath(__DIR__ . '/..');
chdir($root . '/couch');
$_SERVER['HTTP_HOST'] = 'garden-lounge.pro';
$_SERVER['REQUEST_URI'] = '/admiralteyskaya/couch/';
$_SERVER['SCRIPT_NAME'] = '/admiralteyskaya/couch/index.php';
$_SERVER['SCRIPT_FILENAME'] = $root . '/couch/index.php';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

echo "Booting Couch admin...\n";
ob_start();
try {
    require $root . '/couch/index.php';
    $out = ob_get_clean();
    echo "OK, output length: " . strlen($out) . "\n";
    if (strlen($out) < 300) {
        echo $out . "\n";
    } else {
        echo substr($out, 0, 300) . "...\n";
    }
} catch (Throwable $e) {
    ob_end_clean();
    echo "Throwable: " . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine() . "\n";
}
