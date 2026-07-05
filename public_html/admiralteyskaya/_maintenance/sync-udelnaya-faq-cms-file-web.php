<?php
/**
 * Ensure udelnaya/faq.php exists where CouchCMS admin expects it.
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/sync-udelnaya-faq-cms-file-web.php?token=gl-cache-clear-20260623
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit("Forbidden\n");
}

header('Content-Type: text/plain; charset=utf-8');

$root = realpath(__DIR__ . '/..');
$target = $root . '/udelnaya/faq.php';
$source = __DIR__ . '/../udelnaya/faq.php';

if (!is_file($source)) {
    $source = dirname($root) . '/udelnaya/faq.php';
}

if (!is_dir(dirname($target))) {
    if (!mkdir(dirname($target), 0755, true)) {
        exit("Failed to create directory: " . dirname($target) . "\n");
    }
    echo "Created " . dirname($target) . "\n";
}

if (!is_file($target)) {
    if (!is_file($source)) {
        exit("Source template missing: {$source}\n");
    }
    if (!copy($source, $target)) {
        exit("Copy failed: {$source} -> {$target}\n");
    }
    echo "Copied template to {$target}\n";
} else {
    echo "Template already exists: {$target}\n";
}

require_once $root . '/couch/cms.php';

global $AUTH, $FUNCS;
if (!isset($AUTH->user) || !is_object($AUTH->user)) {
    exit("Couch auth not initialized\n");
}
$AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;

chdir(dirname($target));
$_SERVER['HTTP_HOST'] = 'garden-lounge.pro';
$_SERVER['REQUEST_URI'] = '/admiralteyskaya/udelnaya/faq.php';
$_SERVER['SCRIPT_NAME'] = '/admiralteyskaya/udelnaya/faq.php';
$_SERVER['SCRIPT_FILENAME'] = $target;
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

ob_start();
require $target;
ob_end_clean();

$pg = new KWebpage('udelnaya/faq.php');
if (!empty($pg->error)) {
    exit('KWebpage failed: ' . $pg->err_msg . "\n");
}

echo "KWebpage loaded udelnaya/faq.php OK\n";
echo 'Fields: ' . count($pg->fields) . "\n";

if (isset($FUNCS)) {
    $FUNCS->invalidate_cache();
}
echo "Done\n";
