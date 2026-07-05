<?php
/**
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/patch-kfunctions-faq-menu-web.php?token=gl-cache-clear-20260623
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit('Forbidden');
}
header('Content-Type: text/plain; charset=utf-8');
require __DIR__ . '/patch-kfunctions-faq-menu.php';
