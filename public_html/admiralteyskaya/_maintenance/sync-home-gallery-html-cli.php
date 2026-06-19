<?php
/**
 * Sync repeatable _html for home gallery fields to match home.php definitions.
 */
$root = realpath(__DIR__ . '/..');
chdir($root);
require_once $root . '/couch/cms.php';
global $AUTH, $DB;

$AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;
$_SERVER['HTTP_HOST'] = 'garden-lounge.pro';
$_SERVER['REQUEST_URI'] = '/admiralteyskaya/home.php';
$_SERVER['SCRIPT_NAME'] = '/admiralteyskaya/home.php';
$_SERVER['SCRIPT_FILENAME'] = $root . '/home.php';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

ob_start();
require $root . '/home.php';
ob_end_clean();

$fields = K_DB_TABLES_PREFIX . 'couch_fields';
foreach (array('home_adm_gallery', 'home_udel_gallery') as $name) {
    $rows = $DB->select($fields, array('id', '_html'), "name='" . $DB->sanitize($name) . "' LIMIT 1");
    if (!count($rows)) {
        echo "{$name}: missing\n";
        continue;
    }
    $html = $rows[0]['_html'];
    echo "{$name} (#{$rows[0]['id']}):\n";
    echo '  home_adm_gallery_img: ' . (strpos($html, 'home_adm_gallery_img') !== false ? 'yes' : 'no') . "\n";
    echo '  home_udel_gallery_img: ' . (strpos($html, 'home_udel_gallery_img') !== false ? 'yes' : 'no') . "\n";
    echo '  home_gallery_img: ' . (strpos($html, 'home_gallery_img') !== false ? 'yes' : 'no') . "\n";
}

define('GL_SKIP_CLI_CHECK', true);
require __DIR__ . '/clear-couch-cache-cli.php';
