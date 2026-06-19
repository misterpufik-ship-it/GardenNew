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
$pagesTable = K_DB_TABLES_PREFIX . 'couch_pages';
$fieldsTable = K_DB_TABLES_PREFIX . 'couch_fields';
$dataText = K_DB_TABLES_PREFIX . 'couch_data_text';

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

    return $templateId;
}

function update_page_field($db, $fields, $text, $templates, $pages, $templateName, $fieldName, $newValue)
{
    $tpl = fetch_one($db, "SELECT id FROM `{$templates}` WHERE name='" . $db->real_escape_string($templateName) . "' LIMIT 1");
    if (!$tpl) {
        echo "Field update skipped: template {$templateName} not found\n";
        return;
    }

    $field = fetch_one(
        $db,
        "SELECT id FROM `{$fields}` WHERE template_id=" . (int)$tpl['id'] .
        " AND name='" . $db->real_escape_string($fieldName) . "' LIMIT 1"
    );
    if (!$field) {
        echo "Field update skipped: {$fieldName} not found\n";
        return;
    }

    $page = fetch_one($db, "SELECT id FROM `{$pages}` WHERE template_id=" . (int)$tpl['id'] . " LIMIT 1");
    if (!$page) {
        echo "Field update skipped: page for {$templateName} not found\n";
        return;
    }

    $row = fetch_one(
        $db,
        "SELECT id, value FROM `{$text}` WHERE field_id=" . (int)$field['id'] .
        " AND page_id=" . (int)$page['id'] . " LIMIT 1"
    );
    $escaped = $db->real_escape_string($newValue);
    if ($row) {
        $db->query("UPDATE `{$text}` SET value='{$escaped}' WHERE id=" . (int)$row['id'] . " LIMIT 1");
        echo "Updated {$fieldName}: {$row['value']} -> {$newValue}\n";
        return;
    }

    $db->query(
        "INSERT INTO `{$text}` (page_id, field_id, value) VALUES (" .
        (int)$page['id'] . ", " . (int)$field['id'] . ", '{$escaped}')"
    );
    echo "Inserted {$fieldName}: {$newValue}\n";
}

ensure_template($db, $templates, $pagesTable, 'home.php', 'Главная', 0, 0, 1, 'header.php');

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
        ensure_page($db, $pagesTable, (int)$db->insert_id, 'Главная (сайт)');
    }
} else {
    echo "Before site-home.php: executable={$siteRow['executable']} hidden={$siteRow['hidden']}\n";
    $db->query(
        "UPDATE `{$templates}` SET executable='1', hidden='1', title='Главная (сайт)' WHERE id=" . (int)$siteRow['id'] . " LIMIT 1"
    );
    if ($db->error) {
        echo "site-home update failed: {$db->error}\n";
    }
    ensure_page($db, $pagesTable, (int)$siteRow['id'], 'Главная (сайт)');
    echo "After site-home.php updated\n";
}

update_page_field(
    $db,
    $fieldsTable,
    $dataText,
    $templates,
    $pagesTable,
    'home.php',
    'home_udel_telegram',
    'https://t.me/gardenlounge_udelnaya'
);

echo "Syncing home.php editable fields...\n";
$homeRoot = realpath(__DIR__ . '/..');
if ($homeRoot) {
    chdir($homeRoot);
    require_once $homeRoot . '/couch/cms.php';
    global $AUTH, $DB;
    if (isset($AUTH->user) && is_object($AUTH->user)) {
        $AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;
        $pg = new KWebpage('home.php');
        if (!empty($pg->error)) {
            echo "Field sync error: {$pg->err_msg}\n";
        } elseif (isset($DB)) {
            $tplRows = $DB->select(K_TBL_TEMPLATES, array('id'), "name='home.php'");
            if (count($tplRows)) {
                $fid = $tplRows[0]['id'];
                $fc = $DB->select(K_TBL_FIELDS, array('count(*) as cnt'), "template_id='" . $DB->sanitize($fid) . "'");
                echo "home.php fields in DB: " . $fc[0]['cnt'] . "\n";
            }
        }
    } else {
        echo "Field sync skipped: auth not initialized\n";
    }
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