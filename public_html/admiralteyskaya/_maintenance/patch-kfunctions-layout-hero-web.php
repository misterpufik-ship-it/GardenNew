<?php
/**
 * Web runner for patch-kfunctions-layout-hero.php
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/patch-kfunctions-layout-hero-web.php?key=<md5>
 * key = md5('garden-lounge-patch-kfunctions-layout-hero')
 */
$expectedKey = md5('garden-lounge-patch-kfunctions-layout-hero');
if ((isset($_GET['key']) ? $_GET['key'] : '') !== $expectedKey) {
    http_response_code(403);
    exit("Forbidden\n");
}
header('Content-Type: text/plain; charset=utf-8');
require __DIR__ . '/patch-kfunctions-layout-hero.php';
