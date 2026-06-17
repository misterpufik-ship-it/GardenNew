<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

$config = __DIR__ . '/../couch/config.php';
define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ?: 3306;
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
}

$db = new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
$db->set_charset('utf8');

function probe_one($db, $sql)
{
    $res = $db->query($sql);
    if (!$res) {
        return null;
    }
    $row = $res->fetch_assoc();
    return $row ?: null;
}

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$fields = K_DB_TABLES_PREFIX . 'couch_fields';

foreach (array('gallery.php', 'udelnaya/gallery.php') as $templateName) {
    echo "\n=== {$templateName} ===\n";
    $res = $db->query(
        "SELECT f.id, f.name, f.k_group, f.k_type, f.label
         FROM `{$fields}` f
         JOIN `{$templates}` t ON t.id = f.template_id
         WHERE t.name='" . $db->real_escape_string($templateName) . "'
         ORDER BY f.id"
    );
    while ($row = $res->fetch_assoc()) {
        echo "{$row['id']}\t{$row['name']}\t{$row['k_type']}\tgroup={$row['k_group']}\t{$row['label']}\n";
    }

    $repeatable = probe_one($db, "SELECT id, _html FROM `{$fields}` WHERE name='gallery_items' AND template_id=(SELECT id FROM `{$templates}` WHERE name='" . $db->real_escape_string($templateName) . "' LIMIT 1) LIMIT 1");
    if ($repeatable) {
        echo "gallery_items _html:\n{$repeatable['_html']}\n";
    }
}
