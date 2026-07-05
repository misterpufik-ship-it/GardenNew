<?php
/**
 * Ensure both FAQ templates exist in Couch + menu works.
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/fix-faq-admin-web.php?token=gl-cache-clear-20260623
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit("Forbidden\n");
}

header('Content-Type: text/plain; charset=utf-8');

require __DIR__ . '/patch-kfunctions-faq-branches.php';
echo "---\n";
require __DIR__ . '/register-faq-fields-web.php';
