<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

$config = __DIR__ . '/../couch/config.php';
if (!is_file($config)) {
    fwrite(STDERR, "CouchCMS config not found: {$config}\n");
    exit(1);
}

define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ?: 3306;
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
}

mysqli_report(MYSQLI_REPORT_OFF);
$db = @new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
if ($db->connect_errno) {
    fwrite(STDERR, "DB connection failed: {$db->connect_error}\n");
    exit(1);
}
$db->set_charset('utf8');

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';

$name = 'home.php';
$res = $db->query("SELECT id, name, title, executable, clonable, hidden FROM `{$templates}` WHERE name='" . $db->real_escape_string($name) . "' LIMIT 1");
$row = $res ? $res->fetch_assoc() : null;

if (!$row) {
    echo "Template {$name} is not registered. Visiting via web as admin may be required.\n";
    exit(2);
}

echo "Before: ";
print_r($row);

$db->query(
    "UPDATE `{$templates}` SET executable='1', hidden='0' WHERE id=" . (int)$row['id'] . " LIMIT 1"
);

$pageRes = $db->query("SELECT id, page_title, publish_date FROM `{$pages}` WHERE template_id=" . (int)$row['id'] . " LIMIT 1");
$page = $pageRes ? $pageRes->fetch_assoc() : null;
if (!$page) {
    $now = date('Y-m-d H:i:s');
    $db->query(
        "INSERT INTO `{$pages}` (template_id, page_title, page_name, creation_date, modification_date, publish_date, status) VALUES (" .
        (int)$row['id'] . ", 'Главная', 'index', '{$now}', '{$now}', '{$now}', 0)"
    );
    echo "Created default page for home.php\n";
} else {
    echo "Page exists: #{$page['id']} {$page['page_title']}\n";
}

$res = $db->query("SELECT id, name, title, executable, clonable, hidden FROM `{$templates}` WHERE id=" . (int)$row['id'] . " LIMIT 1");
$row = $res ? $res->fetch_assoc() : null;
echo "After: ";
print_r($row);

echo "Done.\n";
