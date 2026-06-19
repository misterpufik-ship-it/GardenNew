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

header('Content-Type: text/plain; charset=utf-8');
echo "DB: " . K_DB_NAME . " prefix: " . K_DB_TABLES_PREFIX . "\n";

$res = $db->query("SELECT id, name, executable, hidden FROM `{$templates}` WHERE name='" . $db->real_escape_string($name) . "' LIMIT 1");
$row = $res ? $res->fetch_assoc() : null;
if (!$row) {
    echo "template {$name} not found, attempting register...\n";
    $ref = $db->query("SELECT * FROM `{$templates}` WHERE name='index.php' LIMIT 1");
    $refRow = $ref ? $ref->fetch_assoc() : null;
    if (!$refRow) {
        echo "reference template index.php not found\n";
        exit;
    }
    unset($refRow['id']);
    $refRow['name'] = $name;
    $refRow['title'] = 'Главная';
    $refRow['executable'] = 1;
    $refRow['hidden'] = 0;
    $refRow['clonable'] = 0;
    $refRow['description'] = '';
    if (isset($refRow['custom_params'])) {
        $refRow['custom_params'] = '';
    }
    $cols = array_keys($refRow);
    $vals = array_map(function ($v) use ($db) {
        if ($v === null) return 'NULL';
        return "'" . $db->real_escape_string((string)$v) . "'";
    }, array_values($refRow));
    $sql = "INSERT INTO `{$templates}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $vals) . ")";
    if (!$db->query($sql)) {
        echo "insert failed: " . $db->error . "\n";
        exit;
    }
    $newId = (int)$db->insert_id;
    echo "Registered template #{$newId}\n";
    $now = date('Y-m-d H:i:s');
    $db->query(
        "INSERT INTO `{$pages}` (template_id, page_title, page_name, creation_date, modification_date, publish_date, status) VALUES (" .
        $newId . ", 'Главная', 'index', '{$now}', '{$now}', '{$now}', 0)"
    );
    echo "Created page for home.php\n";
    $res = $db->query("SELECT id, name, executable, hidden FROM `{$templates}` WHERE id={$newId} LIMIT 1");
    $row = $res ? $res->fetch_assoc() : null;
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

$pageRes = $db->query("SELECT id, page_title, page_name, status, publish_date FROM `{$pages}` WHERE template_id=" . (int)$row['id']);
echo "Pages for home.php:\n";
while ($p = $pageRes->fetch_assoc()) {
    echo "#{$p['id']} name={$p['page_name']} status={$p['status']} publish={$p['publish_date']}\n";
}

$refPage = $db->query("SELECT * FROM `{$pages}` WHERE template_id=1 LIMIT 1");
if ($refPage && ($rp = $refPage->fetch_assoc())) {
    echo "Reference index.php page columns sample:\n";
    foreach ($rp as $k => $v) {
        if (strlen((string)$v) > 80) $v = substr((string)$v, 0, 80) . '...';
        echo "  {$k} = {$v}\n";
    }
}

echo "OK\n";
