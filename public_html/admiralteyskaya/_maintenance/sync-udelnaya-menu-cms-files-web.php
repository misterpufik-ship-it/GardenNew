<?php
/**
 * Ensure Udelnaya menu CMS templates exist where Couch admin expects them.
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/sync-udelnaya-menu-cms-files-web.php?token=gl-cache-clear-20260623
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit("Forbidden\n");
}

header('Content-Type: text/plain; charset=utf-8');

$root = realpath(__DIR__ . '/..');
$templates = array(
    'udelnaya/menu.php',
    'udelnaya/menu/text/index.php',
    'udelnaya/menu/visual/index.php',
    'udelnaya/menu/english/index.php',
);

require_once $root . '/couch/cms.php';

global $AUTH, $FUNCS;
if (!isset($AUTH->user) || !is_object($AUTH->user)) {
    exit("Couch auth not initialized\n");
}
$AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;

foreach ($templates as $tpl) {
    $target = $root . '/' . $tpl;
    if (!is_file($target)) {
        echo "MISSING: {$target}\n";
        continue;
    }

    chdir(dirname($target));
    $_SERVER['HTTP_HOST'] = 'garden-lounge.pro';
    $_SERVER['REQUEST_URI'] = '/admiralteyskaya/' . $tpl;
    $_SERVER['SCRIPT_NAME'] = '/admiralteyskaya/' . $tpl;
    $_SERVER['SCRIPT_FILENAME'] = $target;
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

    ob_start();
    require $target;
    ob_end_clean();

    $pg = new KWebpage($tpl);
    if (!empty($pg->error)) {
        echo "KWebpage failed for {$tpl}: {$pg->err_msg}\n";
        continue;
    }

    echo "OK {$tpl} fields=" . count($pg->fields) . "\n";
}

if (isset($FUNCS)) {
    $FUNCS->invalidate_cache();
}
echo "Done\n";
