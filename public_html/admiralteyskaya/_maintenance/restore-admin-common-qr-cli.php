<?php
/**
 * Restore Couch admin: fix "local stub" titles, register missing templates, QR section.
 * Run on server: php restore-admin-common-qr-cli.php
 */
if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}

$root = realpath(dirname(__DIR__));
chdir($root);

define('K_COUCH_DIR', $root . '/couch/');
require $root . '/couch/config.php';

$register = __DIR__ . '/couch-register-template-cli.php';
$toRegister = array(
    'home.php',
    'layout-spacing.php',
    'layout-scroll.php',
    'layout-hero.php',
    'layout-mobile-menu.php',
    'booking-settings.php',
    'age-gate-settings.php',
    'preloader-settings.php',
    'admin-labels.php',
    'qr-codes.php',
);

foreach ($toRegister as $tpl) {
    if (!is_file($root . '/' . $tpl)) {
        echo "SKIP missing file {$tpl}\n";
        continue;
    }
    passthru(PHP_BINARY . ' ' . escapeshellarg($register) . ' ' . escapeshellarg($tpl), $code);
    if ($code !== 0) {
        echo "WARN register {$tpl} exit {$code}\n";
    }
}

$db = new mysqli('127.0.0.1', K_DB_USER, K_DB_PASSWORD, K_DB_NAME);
$db->set_charset('utf8mb4');
$t = K_DB_TABLES_PREFIX . 'couch_templates';
$p = K_DB_TABLES_PREFIX . 'couch_pages';

$titles = array(
    'home.php' => 'Главная',
    'layout-spacing.php' => 'Отступы между блоками',
    'layout-scroll.php' => 'Прокрутка по меню',
    'layout-hero.php' => 'Hero — мобильный лого',
    'layout-mobile-menu.php' => 'Гамбургер-меню',
    'booking-settings.php' => 'Бронирование Telegram',
    'age-gate-settings.php' => 'Заглушка 18+',
    'preloader-settings.php' => 'Прелоадер',
    'admin-labels.php' => 'Названия разделов',
    'qr-codes.php' => 'QR коды',
);

foreach ($titles as $name => $title) {
    $nameEsc = $db->real_escape_string($name);
    $titleEsc = $db->real_escape_string($title);
    $db->query("UPDATE `{$t}` SET title='{$titleEsc}' WHERE name='{$nameEsc}'");
    $db->query(
        "UPDATE `{$p}` SET page_title='{$titleEsc}' WHERE template_id=(SELECT id FROM `{$t}` WHERE name='{$nameEsc}' LIMIT 1) AND is_master='1'"
    );
    echo "Title {$name} -> {$title}\n";
}

require __DIR__ . '/patch-kfunctions-layout-templates.php';

passthru(PHP_BINARY . ' ' . escapeshellarg(__DIR__ . '/run-register-qr-cli.php'), $qrCode);
if ($qrCode !== 0) {
    echo "WARN QR register exit {$qrCode}\n";
}

echo "Done. Refresh admin (Ctrl+F5).\n";
