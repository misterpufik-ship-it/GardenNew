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
$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';
$text = K_DB_TABLES_PREFIX . 'couch_data_text';

$templateNames = ['filial.php', 'udelnaya/filial.php'];

foreach ($templateNames as $templateName) {
    echo "\n=== {$templateName} ===\n";

    $tpl = $db->query("SELECT id, name, title FROM `{$templates}` WHERE name='" . $db->real_escape_string($templateName) . "' LIMIT 1");
    $tplRow = $tpl ? $tpl->fetch_assoc() : null;
    if (!$tplRow) {
        echo "Template not registered in DB.\n";
        continue;
    }

    echo "Template ID: {$tplRow['id']} ({$tplRow['title']})\n";

    $fieldRes = $db->query("SELECT name, label, k_type FROM `{$fields}` WHERE template_id=" . (int)$tplRow['id'] . " ORDER BY id");
    echo "Fields:\n";
    if (!$fieldRes) {
        echo " Field query error: {$db->error}\n";
    }
    while ($fieldRes && ($field = $fieldRes->fetch_assoc())) {
        echo " - {$field['name']} [{$field['k_type']}] label=\"{$field['label']}\"\n";
    }

    $pageRes = $db->query("SELECT id, page_title FROM `{$pages}` WHERE template_id=" . (int)$tplRow['id'] . " LIMIT 5");
    while ($pageRes && ($page = $pageRes->fetch_assoc())) {
        echo "Page #{$page['id']}: {$page['page_title']}\n";
        $dataRes = $db->query(
            "SELECT f.name, t.value
             FROM `{$text}` t
             JOIN `{$fields}` f ON f.id = t.field_id
             WHERE t.page_id=" . (int)$page['id'] . "
               AND f.name IN ('final_img','final_gallery','final_gallery_img','final_media_mode')
             ORDER BY f.name"
        );
        while ($dataRes && ($data = $dataRes->fetch_assoc())) {
            $value = strlen($data['value']) > 120 ? substr($data['value'], 0, 120) . '...' : $data['value'];
            echo "   {$data['name']}: {$value}\n";
        }
    }
}

echo "\n";
