<?php
/**
 * Register / refresh one Couch template from its PHP file (super-admin parse).
 * Usage: php couch-register-template-cli.php layout-spacing.php
 */
if (PHP_SAPI !== 'cli' || !isset($argv[1])) {
    fwrite(STDERR, "Usage: php couch-register-template-cli.php <template.php>\n");
    exit(1);
}

$root = realpath(dirname(__DIR__));
$name = basename($argv[1]);
$file = $root . DIRECTORY_SEPARATOR . $name;
if (!is_file($file)) {
    fwrite(STDERR, "Not found: {$file}\n");
    exit(1);
}

chdir($root);
$_SERVER['HTTP_HOST'] = '127.0.0.1';
$_SERVER['SERVER_NAME'] = '127.0.0.1';
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['SCRIPT_FILENAME'] = $file;
$_SERVER['SCRIPT_NAME'] = '/admiralteyskaya/' . $name;
$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
$_GET = array();

require_once $root . '/couch/cms.php';

global $AUTH;
if (!isset($AUTH->user) || !is_object($AUTH->user)) {
    fwrite(STDERR, "Couch auth not initialized\n");
    exit(1);
}
$AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;

$level = ob_get_level();
ob_start();
require $file;
while (ob_get_level() > $level) {
    ob_end_clean();
}
echo "OK {$name}\n";
