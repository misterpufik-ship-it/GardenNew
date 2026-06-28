<?php
/**
 * Parse preloader-settings.php as super-admin to register all editable fields.
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/register-preloader-parse-web.php?key=<md5>
 * key = md5('garden-lounge-register-preloader-parse')
 */
$expectedKey = md5('garden-lounge-register-preloader-parse');
if ((isset($_GET['key']) ? $_GET['key'] : '') !== $expectedKey) {
    http_response_code(403);
    exit("Forbidden\n");
}
header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

$root = realpath(__DIR__ . '/..');
$file = $root . '/preloader-settings.php';
if (!is_file($file)) {
    exit("Missing {$file}\n");
}

chdir($root);
require_once $root . '/couch/cms.php';

global $AUTH, $DB;

if (!isset($AUTH->user) || !is_object($AUTH->user)) {
    exit("Couch auth not initialized\n");
}

$AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;

$_SERVER['HTTP_HOST'] = 'garden-lounge.pro';
$_SERVER['REQUEST_URI'] = '/admiralteyskaya/preloader-settings.php';
$_SERVER['SCRIPT_NAME'] = '/admiralteyskaya/preloader-settings.php';
$_SERVER['SCRIPT_FILENAME'] = $file;
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

echo "Parsing preloader-settings.php as super-admin...\n";
ob_start();
require $file;
ob_end_clean();

$tpl = $DB->select(K_DB_TABLES_PREFIX . 'couch_templates', array('id', 'name'), "name = 'preloader-settings.php'");
if (!count($tpl)) {
    exit("Template still missing after parse\n");
}
$templateId = (int)$tpl[0]['id'];
$fieldCount = $DB->select(K_DB_TABLES_PREFIX . 'couch_fields', array('id'), "template_id = {$templateId}");
echo "Template #{$templateId}, fields: " . count($fieldCount) . "\n";

if (isset($FUNCS) && method_exists($FUNCS, 'invalidate_cache')) {
    $FUNCS->invalidate_cache();
}
$cacheDir = $root . '/couch/cache';
if (is_dir($cacheDir)) {
    foreach (glob($cacheDir . '/*') as $f) {
        if (is_file($f) && basename($f) !== '.htaccess') {
            @unlink($f);
        }
    }
}
echo "Done.\n";
