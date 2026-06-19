<?php
if ((isset($_GET['token']) ? $_GET['token'] : '') !== 'gl-home-fix-20260619') {
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
require_once K_COUCH_DIR . 'functions.php';

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ? ini_get('mysqli.default_port') : 3306;
if (strpos($host, ':') !== false) {
    $parts = explode(':', $host, 2);
    $host = $parts[0];
    $port = $parts[1];
}

$db = @new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
if ($db->connect_errno) {
    http_response_code(500);
    exit('db connect failed');
}
$db->set_charset('utf8');

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';

header('Content-Type: text/plain; charset=utf-8');
echo "DB: " . K_DB_NAME . " prefix: " . K_DB_TABLES_PREFIX . "\n";

function fix_home_template($db, $templates, $pages, $name, $executable, $hidden, $title) {
    $res = $db->query("SELECT id, name, title, executable, hidden FROM `{$templates}` WHERE name='" . $db->real_escape_string($name) . "' LIMIT 1");
    $row = $res ? $res->fetch_assoc() : null;
    if (!$row) {
        echo "Template {$name} not found — open Couch admin once after deploy to register new files.\n";
        return;
    }

    echo "Before {$name}: executable={$row['executable']} hidden={$row['hidden']} title={$row['title']}\n";
    $db->query(
        "UPDATE `{$templates}` SET executable='{$executable}', hidden='{$hidden}', title='" .
        $db->real_escape_string($title) . "' WHERE id=" . (int)$row['id'] . " LIMIT 1"
    );

    if ($executable) {
        $pageRes = $db->query("SELECT id FROM `{$pages}` WHERE template_id=" . (int)$row['id'] . " LIMIT 1");
        $page = $pageRes ? $pageRes->fetch_assoc() : null;
        if (!$page) {
            $now = date('Y-m-d H:i:s');
            $db->query(
                "INSERT INTO `{$pages}` (template_id, page_title, page_name, creation_date, modification_date, publish_date, status) VALUES (" .
                (int)$row['id'] . ", 'Главная', 'index', '{$now}', '{$now}', '{$now}', 0)"
            );
            echo "Created page for {$name}\n";
        }
    }

    $res = $db->query("SELECT executable, hidden, title FROM `{$templates}` WHERE id=" . (int)$row['id'] . " LIMIT 1");
    $row = $res ? $res->fetch_assoc() : null;
    if ($row) {
        echo "After {$name}: executable={$row['executable']} hidden={$row['hidden']} title={$row['title']}\n";
    }
}

fix_home_template($db, $templates, $pages, 'home.php', 0, 0, 'Главная');
fix_home_template($db, $templates, $pages, 'site-home.php', 1, 1, 'Главная (сайт)');

$cacheDir = K_COUCH_DIR . 'cache';
$removed = 0;
if (is_dir($cacheDir)) {
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cacheDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($items as $item) {
        $path = $item->getPathname();
        if ($item->isDir()) {
            if ($path !== $cacheDir && basename($path) !== 'booking-throttle') {
                @rmdir($path);
            }
            continue;
        }
        if (basename($path) === '.htaccess') {
            continue;
        }
        if (@unlink($path)) {
            $removed++;
        }
    }
}
if (isset($FUNCS) && method_exists($FUNCS, 'invalidate_cache')) {
    $FUNCS->invalidate_cache();
}

echo "CouchCMS cache cleared ({$removed} files removed).\n";
echo "OK\n";
