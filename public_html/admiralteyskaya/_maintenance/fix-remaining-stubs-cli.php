<?php
/**
 * Fix remaining "local stub" in admin: faq, site-home, admin-labels.
 */
if (PHP_SAPI !== 'cli') {
    exit("CLI only\n");
}

$root = realpath(dirname(__DIR__));
chdir($root);

$register = __DIR__ . '/couch-register-template-cli.php';
foreach (array('admin-labels.php', 'faq.php', 'site-home.php') as $tpl) {
    if (!is_file($root . '/' . $tpl)) {
        continue;
    }
    passthru(PHP_BINARY . ' ' . escapeshellarg($register) . ' ' . escapeshellarg($tpl), $code);
}

define('K_COUCH_DIR', $root . '/couch/');
require $root . '/couch/config.php';
$db = new mysqli('127.0.0.1', K_DB_USER, K_DB_PASSWORD, K_DB_NAME);
$db->set_charset('utf8mb4');
$t = K_DB_TABLES_PREFIX . 'couch_templates';
$p = K_DB_TABLES_PREFIX . 'couch_pages';

$titles = array(
    'admin-labels.php' => 'Названия разделов',
    'faq.php' => 'FAQ — Адмирал',
    'site-home.php' => 'Главная (сайт)',
);

foreach ($titles as $name => $title) {
    $ne = $db->real_escape_string($name);
    $te = $db->real_escape_string($title);
    $db->query("UPDATE `{$t}` SET title='{$te}' WHERE name='{$ne}'");
    $db->query(
        "UPDATE `{$p}` p INNER JOIN `{$t}` t ON t.id=p.template_id SET p.page_title='{$te}' WHERE t.name='{$ne}' AND p.is_master='1'"
    );
    echo "Title {$name} -> {$title}\n";
}
$db->query("UPDATE `{$t}` SET hidden='1' WHERE name='site-home.php'");

$faqListener = __DIR__ . '/append-faq-menu-listener-web.php';
if (is_file($faqListener)) {
    $_GET['token'] = 'gl-cache-clear-20260623';
    ob_start();
    include $faqListener;
    echo ob_get_clean();
}

echo "Done.\n";
