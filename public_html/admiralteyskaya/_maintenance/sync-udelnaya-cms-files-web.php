<?php
/**
 * Verify all Udelnaya CMS templates load in Couch admin context.
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/sync-udelnaya-cms-files-web.php?token=gl-cache-clear-20260623
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit("Forbidden\n");
}

header('Content-Type: text/plain; charset=utf-8');

$root = realpath(__DIR__ . '/..');
$templates = array(
    'udelnaya/header.php',
    'udelnaya/about.php',
    'udelnaya/akzii.php',
    'udelnaya/menu.php',
    'udelnaya/menu/text/index.php',
    'udelnaya/menu/english/index.php',
    'udelnaya/menu/visual/index.php',
    'udelnaya/sticky-sticker.php',
    'udelnaya/gallery.php',
    'udelnaya/faq.php',
    'udelnaya/reservation.php',
    'udelnaya/contacts.php',
    'udelnaya/filial.php',
    'udelnaya/globals.php',
    'udelnaya/index.php',
);

require_once $root . '/couch/cms.php';

global $AUTH, $FUNCS;
if (!isset($AUTH->user) || !is_object($AUTH->user)) {
    exit("Couch auth not initialized\n");
}
$AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;

$ok = 0;
$fail = 0;

foreach ($templates as $tpl) {
    $target = $root . '/' . $tpl;
    if (!is_file($target)) {
        echo "MISSING FILE: {$tpl}\n";
        $fail++;
        continue;
    }

    $pg = new KWebpage($tpl);
    if (!empty($pg->error)) {
        echo "FAIL {$tpl}: {$pg->err_msg}\n";
        $fail++;
        continue;
    }

    echo "OK {$tpl} fields=" . count($pg->fields) . "\n";
    $ok++;
}

if (isset($FUNCS)) {
    $FUNCS->invalidate_cache();
}

echo "Summary: ok={$ok} fail={$fail}\n";
echo "Done\n";
