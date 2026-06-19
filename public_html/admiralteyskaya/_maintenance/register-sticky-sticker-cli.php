<?php
/**
 * Регистрирует шаблоны sticky-sticker.php в CouchCMS (нужен super-admin).
 *
 * CLI:
 *   php _maintenance/register-sticky-sticker-cli.php
 */

$root = realpath(__DIR__ . '/..');
if (!$root) {
    fwrite(STDERR, "Cannot resolve template root\n");
    exit(1);
}

chdir($root);
require_once $root . '/couch/cms.php';

global $AUTH, $DB;

if (!isset($AUTH->user) || !is_object($AUTH->user)) {
    fwrite(STDERR, "Couch auth not initialized\n");
    exit(1);
}

$AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;

$targets = [
    [
        'file' => $root . '/sticky-sticker.php',
        'uri' => '/admiralteyskaya/sticky-sticker.php',
        'name' => 'sticky-sticker.php',
        'page_title' => 'Липкий стикер',
    ],
    [
        'file' => dirname($root) . '/udelnaya/sticky-sticker.php',
        'uri' => '/udelnaya/sticky-sticker.php',
        'name' => 'udelnaya/sticky-sticker.php',
        'page_title' => 'Липкий стикер (Удельная)',
    ],
];

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';

foreach ($targets as $target) {
    if (!is_file($target['file'])) {
        fwrite(STDERR, "Missing file: {$target['file']}\n");
        exit(1);
    }

    $_SERVER['HTTP_HOST'] = 'garden-lounge.pro';
    $_SERVER['REQUEST_URI'] = $target['uri'];
    $_SERVER['SCRIPT_NAME'] = $target['uri'];
    $_SERVER['SCRIPT_FILENAME'] = $target['file'];
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

    echo "Parsing {$target['name']} as super-admin...\n";
    ob_start();
    require $target['file'];
    ob_end_clean();

    $tplRows = $DB->select($templates, array('id', 'name', 'title'), "name='" . $DB->sanitize($target['name']) . "'");
    if (!count($tplRows)) {
        echo "Template {$target['name']} not found in DB\n";
        exit(1);
    }

    $templateId = (int) $tplRows[0]['id'];
    $DB->update(
        $templates,
        array('executable' => '0', 'hidden' => '0', 'title' => $target['page_title'], 'clonable' => '0'),
        "id='" . $DB->sanitize($templateId) . "'"
    );

    $pageRows = $DB->select($pages, array('id'), "template_id='" . $DB->sanitize($templateId) . "' LIMIT 1");
    if (!count($pageRows)) {
        $now = date('Y-m-d H:i:s');
        $DB->insert(
            $pages,
            array(
                'template_id' => $templateId,
                'page_title' => $target['page_title'],
                'page_name' => 'index',
                'creation_date' => $now,
                'modification_date' => $now,
                'publish_date' => $now,
                'status' => '0',
                'is_master' => '1',
            )
        );
        echo "Created master page for {$target['name']}\n";
    }

    $fieldRows = $DB->select($fields, array('id', 'name'), "template_id='" . $DB->sanitize($templateId) . "'");
    echo "{$target['name']} fields in DB: " . count($fieldRows) . "\n";
}

define('GL_SKIP_CLI_CHECK', true);
require __DIR__ . '/clear-couch-cache-cli.php';
