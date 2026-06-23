<?php
if ((isset($_GET['token']) ? $_GET['token'] : '') !== 'gl-cache-clear-20260623') {
    http_response_code(404);
    exit;
}

define('GL_SKIP_CLI_CHECK', 1);
require __DIR__ . '/register-preloader-fields-cli.php';
