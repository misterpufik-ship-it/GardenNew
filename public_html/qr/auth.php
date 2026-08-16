<?php
function gl_qr_require_admin($ajax = false)
{
    if (!defined('K_COUCH_DIR')) {
        define('K_COUCH_DIR', str_replace('\\', '/', dirname(__DIR__) . '/admiralteyskaya/couch/'));
    }
    if (!defined('K_ADMIN')) {
        define('K_ADMIN', 1);
    }
    require_once K_COUCH_DIR . 'header.php';

    global $AUTH;
    $ok = isset($AUTH->user) && is_object($AUTH->user) && $AUTH->user->access_level >= K_ACCESS_LEVEL_ADMIN;
    if ($ok) {
        return;
    }
    if ($ajax) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('ok' => false, 'error' => 'Нужна авторизация администратора'), JSON_UNESCAPED_UNICODE);
        exit;
    }
    $login = (defined('K_ADMIN_URL') ? K_ADMIN_URL : '/admiralteyskaya/couch/') . 'index.php';
    header('Location: ' . $login, true, 302);
    exit;
}
