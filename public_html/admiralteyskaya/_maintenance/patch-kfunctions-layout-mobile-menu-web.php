<?php
/**
 * Web runner for patch-kfunctions-layout-mobile-menu.php
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/patch-kfunctions-layout-mobile-menu-web.php?key=<md5>
 * key = md5('garden-lounge-patch-kfunctions-layout-mobile-menu')
 */
$expectedKey = md5('garden-lounge-patch-kfunctions-layout-mobile-menu');
if ((isset($_GET['key']) ? $_GET['key'] : '') !== $expectedKey) {
    http_response_code(403);
    exit("Forbidden\n");
}
header('Content-Type: text/plain; charset=utf-8');
require __DIR__ . '/patch-kfunctions-layout-mobile-menu.php';
