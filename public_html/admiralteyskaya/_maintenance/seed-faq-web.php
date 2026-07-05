<?php
/**
 * Seed FAQ CMS sections on production.
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/seed-faq-web.php?token=gl-cache-clear-20260623
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit('Forbidden');
}

define('GL_SKIP_CLI_CHECK', true);
require __DIR__ . '/seed-faq-cli.php';
