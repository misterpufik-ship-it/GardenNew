<?php
define('K_COUCH_DIR', '/');
require dirname(__DIR__) . '/couch/lang/RU.php';
$keys = array('menu_modules', 'menu_templates', 'greeting', 'admin_panel', 'login', 'user_remember');
foreach ($keys as $key) {
    echo $key . '=' . (isset($t[$key]) ? $t[$key] : '?') . "\n";
}
