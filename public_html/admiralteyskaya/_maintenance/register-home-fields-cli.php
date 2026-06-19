<?php
/**
 * Регистрирует поля шаблона home.php в CouchCMS (нужен super-admin).
 *
 * CLI:
 *   php _maintenance/register-home-fields-cli.php
 *
 * HTTP:
 *   /admiralteyskaya/_maintenance/register-home-fields-cli.php?key=<md5>
 *   key = md5('garden-lounge-register-home')
 */

$isWeb = (PHP_SAPI !== 'cli');
if ($isWeb) {
    $expectedKey = md5('garden-lounge-register-home');
    if ((isset($_GET['key']) ? $_GET['key'] : '') !== $expectedKey) {
        http_response_code(403);
        exit("Forbidden\n");
    }
    header('Content-Type: text/plain; charset=utf-8');
}

$root = realpath(__DIR__ . '/..');
if (!$root) {
    $msg = "Cannot resolve template root\n";
    if ($isWeb) {
        echo $msg;
    } else {
        fwrite(STDERR, $msg);
    }
    exit(1);
}

chdir($root);
require_once $root . '/couch/cms.php';

global $AUTH, $DB;

if (!isset($AUTH->user) || !is_object($AUTH->user)) {
    $msg = "Couch auth not initialized\n";
    if ($isWeb) {
        echo $msg;
    } else {
        fwrite(STDERR, $msg);
    }
    exit(1);
}

$AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;

$_SERVER['HTTP_HOST'] = 'garden-lounge.pro';
$_SERVER['REQUEST_URI'] = '/admiralteyskaya/home.php';
$_SERVER['SCRIPT_NAME'] = '/admiralteyskaya/home.php';
$_SERVER['SCRIPT_FILENAME'] = $root . '/home.php';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

echo "Parsing home.php as super-admin...\n";
ob_start();
require $root . '/home.php';
ob_end_clean();

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';

$tplRows = $DB->select($templates, array('id', 'executable', 'hidden', 'title'), "name='home.php'");
if (!count($tplRows)) {
    echo "Template home.php not found in DB\n";
    exit(1);
}

$templateId = (int) $tplRows[0]['id'];
$DB->update(
    $templates,
    array('executable' => '0', 'hidden' => '0', 'title' => 'Главная', 'clonable' => '0'),
    "id='" . $DB->sanitize($templateId) . "'"
);

$pageRows = $DB->select($pages, array('id'), "template_id='" . $DB->sanitize($templateId) . "' LIMIT 1");
if (!count($pageRows)) {
    $now = date('Y-m-d H:i:s');
    $DB->insert(
        $pages,
        array(
            'template_id' => $templateId,
            'page_title' => 'Главная',
            'page_name' => 'index',
            'creation_date' => $now,
            'modification_date' => $now,
            'publish_date' => $now,
            'status' => '0',
            'is_master' => '1',
        )
    );
    echo "Created master page for home.php\n";
}

$fieldRows = $DB->select($fields, array('id', 'name', 'k_type', 'label'), "template_id='" . $DB->sanitize($templateId) . "' ORDER BY id");
$count = count($fieldRows);
echo "home.php fields in DB: {$count}\n";

if ($count) {
    foreach ($fieldRows as $row) {
        echo "  - {$row['name']} [{$row['k_type']}] {$row['label']}\n";
    }
} else {
    echo "WARNING: no fields registered\n";
    exit(1);
}

define('GL_SKIP_CLI_CHECK', true);
require __DIR__ . '/clear-couch-cache-cli.php';
