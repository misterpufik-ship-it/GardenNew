?php
if ((isset($_GET['token']) ? $_GET['token'] : '') !== 'gl-cache-clear-20260623') { http_response_code(404); exit; }
ini_set('display_errors', '1');
error_reporting(E_ALL);
header('Content-Type: text/plain; charset=utf-8');
$config = __DIR__ . '/../couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;
require_once K_COUCH_DIR . 'functions.php';
echo "config ok\n";
require_once $_SERVER['DOCUMENT_ROOT'] . '/age-gate/assets.php';
echo "assets ok\n";
require_once K_COUCH_DIR . 'addons/kfunctions.php';
echo "kfunctions ok\n";
require_once K_COUCH_DIR . 'addons/garden-cache/cache-lib.php';
echo "cache-lib ok\n";
