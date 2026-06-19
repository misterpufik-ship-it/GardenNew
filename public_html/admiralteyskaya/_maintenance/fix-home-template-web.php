<?php
if (($_GET['token'] ?? '') !== 'gl-home-fix-20260619') {
    http_response_code(404);
    exit;
}

$config = __DIR__ . '/../couch/config.php';
if (!is_file($config)) {
    http_response_code(500);
    exit('config missing');
}

define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ?: 3306;
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
}

$db = @new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
if ($db->connect_errno) {
    http_response_code(500);
    exit('db connect failed');
}
$db->set_charset('utf8');

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';
$name = 'home.php';

$res = $db->query("SELECT id, name, executable, hidden FROM `{$templates}` WHERE name='" . $db->real_escape_string($name) . "' LIMIT 1");
$row = $res ? $res->fetch_assoc() : null;
if (!$row) {
    exit('template not found');
}

header('Content-Type: text/plain; charset=utf-8');
echo "Before: executable={$row['executable']} hidden={$row['hidden']}\n";

$db->query("UPDATE `{$templates}` SET executable='1', hidden='0' WHERE id=" . (int)$row['id'] . " LIMIT 1");

$pageRes = $db->query("SELECT id FROM `{$pages}` WHERE template_id=" . (int)$row['id'] . " LIMIT 1");
if (!$pageRes || !$pageRes->fetch_assoc()) {
    $now = date('Y-m-d H:i:s');
    $db->query(
        "INSERT INTO `{$pages}` (template_id, page_title, page_name, creation_date, modification_date, publish_date, status) VALUES (" .
        (int)$row['id'] . ", 'Главная', 'index', '{$now}', '{$now}', '{$now}', 0)"
    );
    echo "Created page\n";
}

$res = $db->query("SELECT executable, hidden FROM `{$templates}` WHERE id=" . (int)$row['id'] . " LIMIT 1");
$row = $res->fetch_assoc();
echo "After: executable={$row['executable']} hidden={$row['hidden']}\n";
echo "OK\n";
