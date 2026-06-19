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
$fields = K_DB_TABLES_PREFIX . 'couch_fields';

header('Content-Type: text/plain; charset=utf-8');
echo "DB: " . K_DB_NAME . "\n";

function qval($db, $value)
{
    if ($value === null) {
        return 'NULL';
    }
    return "'" . $db->real_escape_string((string)$value) . "'";
}

function fetch_one($db, $sql)
{
    $res = $db->query($sql);
    return $res ? $res->fetch_assoc() : null;
}

function ensure_page($db, $pages, $templateId, $title)
{
    $page = fetch_one($db, "SELECT id FROM `{$pages}` WHERE template_id=" . (int)$templateId . " LIMIT 1");
    if ($page) {
        echo "Page exists for template #{$templateId}: #{$page['id']}\n";
        return (int)$page['id'];
    }

    $ref = fetch_one($db, "SELECT * FROM `{$pages}` WHERE template_id=1 LIMIT 1");
    if (!$ref) {
        echo "Cannot create page: reference page missing\n";
        return 0;
    }

    unset($ref['id']);
    $now = date('Y-m-d H:i:s');
    $ref['template_id'] = (string)$templateId;
    $ref['page_title'] = $title;
    $ref['page_name'] = 'index';
    $ref['creation_date'] = $now;
    $ref['modification_date'] = $now;
    $ref['publish_date'] = $now;
    $ref['is_master'] = '1';

    $cols = array_keys($ref);
    $vals = array();
    foreach (array_values($ref) as $v) {
        $vals[] = qval($db, $v);
    }
    $sql = "INSERT INTO `{$pages}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $vals) . ")";
    if (!$db->query($sql)) {
        echo "Page insert failed: {$db->error}\n";
        return 0;
    }

    echo "Created page #{$db->insert_id} for template #{$templateId}\n";
    return (int)$db->insert_id;
}

function ensure_template($db, $templates, $pages, $name, $title, $executable, $hidden, $order, $cloneFrom)
{
    $row = fetch_one($db, "SELECT id, name, title, executable, hidden FROM `{$templates}` WHERE name='" . $db->real_escape_string($name) . "' LIMIT 1");
    if (!$row) {
        $sample = fetch_one($db, "SELECT * FROM `{$templates}` WHERE name='" . $db->real_escape_string($cloneFrom) . "' LIMIT 1");
        if (!$sample) {
            echo "Template {$name} missing and clone source {$cloneFrom} not found\n";
            return 0;
        }

        unset($sample['id']);
        $sample['name'] = $name;
        $sample['title'] = $title;
        $sample['executable'] = (string)$executable;
        $sample['hidden'] = (string)$hidden;
        $sample['clonable'] = '0';
        if (isset($sample['order'])) {
            $sample['order'] = (string)$order;
        }

        $cols = array_keys($sample);
        $vals = array();
        foreach (array_values($sample) as $v) {
            $vals[] = qval($db, $v);
        }
        $sql = "INSERT INTO `{$templates}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $vals) . ")";
        if (!$db->query($sql)) {
            echo "Template insert failed for {$name}: {$db->error}\n";
            return 0;
        }
        $templateId = (int)$db->insert_id;
        echo "Created template {$name} (#{$templateId})\n";
    } else {
        $templateId = (int)$row['id'];
        echo "Before {$name}: executable={$row['executable']} hidden={$row['hidden']} title={$row['title']}\n";
        $db->query(
            "UPDATE `{$templates}` SET executable='{$executable}', hidden='{$hidden}', title='" .
            $db->real_escape_string($title) . "' WHERE id={$templateId} LIMIT 1"
        );
        if ($db->error) {
            echo "Update failed for {$name}: {$db->error}\n";
        }
        $after = fetch_one($db, "SELECT executable, hidden, title FROM `{$templates}` WHERE id={$templateId} LIMIT 1");
        if ($after) {
            echo "After {$name}: executable={$after['executable']} hidden={$after['hidden']} title={$after['title']}\n";
        }
    }

    if ($executable) {
        ensure_page($db, $pages, $templateId, $title);
    } else {
        ensure_page($db, $pages, $templateId, $title);
    }

    $fieldCount = fetch_one($db, "SELECT COUNT(*) AS c FROM `{$fields}` WHERE template_id={$templateId}");
    if ($fieldCount) {
        echo "Fields for {$name}: {$fieldCount['c']}\n";
    }

    return $templateId;
}

ensure_template($db, $templates, $pages, 'home.php', 'Главная', 0, 0, 1, 'header.php');

echo "Ensuring site-home.php...\n";
$siteRow = fetch_one($db, "SELECT id, executable, hidden, title FROM `{$templates}` WHERE name='site-home.php' LIMIT 1");
if (!$siteRow) {
    $insertSql =
        "INSERT INTO `{$templates}` (name, title, executable, hidden, clonable) " .
        "SELECT 'site-home.php', 'Главная (сайт)', '1', '1', '0' FROM `{$templates}` WHERE name='index.php' LIMIT 1";
    if (!$db->query($insertSql)) {
        echo "site-home insert failed: {$db->error}\n";
    } else {
        echo "Created site-home.php template #{$db->insert_id}\n";
        ensure_page($db, $pages, (int)$db->insert_id, 'Главная (сайт)');
    }
} else {
    echo "Before site-home.php: executable={$siteRow['executable']} hidden={$siteRow['hidden']}\n";
    $db->query(
        "UPDATE `{$templates}` SET executable='1', hidden='1', title='Главная (сайт)' WHERE id=" . (int)$siteRow['id'] . " LIMIT 1"
    );
    if ($db->error) {
        echo "site-home update failed: {$db->error}\n";
    }
    ensure_page($db, $pages, (int)$siteRow['id'], 'Главная (сайт)');
    echo "After site-home.php updated\n";
}

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

echo "Cache cleared ({$removed} files)\n";
echo "OK\n";