<?php
/**
 * Register FAQ templates in Couch admin menu (web).
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/patch-kfunctions-faq-web.php?token=gl-cache-clear-20260623
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit('Forbidden');
}

require __DIR__ . '/patch-kfunctions-faq.php';
