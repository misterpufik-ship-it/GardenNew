<?php
/**
 * Register FAQ editable fields + seed default Q&A for both branches.
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/register-faq-fields-web.php?token=gl-cache-clear-20260623
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit("Forbidden\n");
}

header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

$root = realpath(__DIR__ . '/..');
require_once $root . '/couch/cms.php';

global $AUTH, $DB, $FUNCS;

if (!isset($AUTH->user) || !is_object($AUTH->user)) {
    exit("Couch auth not initialized\n");
}

$AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$fields = K_DB_TABLES_PREFIX . 'couch_fields';

function gl_faq_q($db, $value)
{
    if ($value === null) {
        return 'NULL';
    }
    return "'" . $db->real_escape_string((string) $value) . "'";
}

function gl_faq_one($db, $sql)
{
    $res = $db->query($sql);
    return $res ? $res->fetch_assoc() : null;
}

function gl_faq_parse_template($filePath, $webPath)
{
    if (!is_file($filePath)) {
        throw new RuntimeException('Missing file: ' . $filePath);
    }

    $_SERVER['HTTP_HOST'] = 'garden-lounge.pro';
    $_SERVER['REQUEST_URI'] = $webPath;
    $_SERVER['SCRIPT_NAME'] = $webPath;
    $_SERVER['SCRIPT_FILENAME'] = $filePath;
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

    chdir(dirname($filePath));
    ob_start();
    require $filePath;
    ob_end_clean();
}

function gl_faq_field_count($DB, $templateId)
{
    $rows = $DB->select(K_DB_TABLES_PREFIX . 'couch_fields', array('id'), 'template_id = ' . (int) $templateId);
    return is_array($rows) ? count($rows) : 0;
}

function gl_faq_has_field($db, $fields, $templateId, $name)
{
    $row = gl_faq_one(
        $db,
        'SELECT id FROM `' . $fields . '` WHERE template_id=' . (int) $templateId .
        " AND name='" . $db->real_escape_string($name) . "' LIMIT 1"
    );
    return $row ? (int) $row['id'] : 0;
}

function gl_faq_insert_field($db, $fields, $row)
{
    unset($row['id']);
    $cols = array_keys($row);
    $vals = array();
    foreach (array_values($row) as $value) {
        $vals[] = gl_faq_q($db, $value);
    }
    $sql = 'INSERT INTO `' . $fields . '` (`' . implode('`,`', $cols) . '`) VALUES (' . implode(',', $vals) . ')';
    if (!$db->query($sql)) {
        throw new RuntimeException('Field insert failed: ' . $db->error);
    }
    return (int) $db->insert_id;
}

function gl_faq_sql_register_fields($db, $fields, $templateName, $cloneTemplateName)
{
    $tpl = gl_faq_one(
        $db,
        "SELECT id FROM `" . K_DB_TABLES_PREFIX . "couch_templates` WHERE name='" .
        $db->real_escape_string($templateName) . "' LIMIT 1"
    );
    if (!$tpl) {
        throw new RuntimeException('Template missing: ' . $templateName);
    }
    $templateId = (int) $tpl['id'];

    $cloneTpl = gl_faq_one(
        $db,
        "SELECT id FROM `" . K_DB_TABLES_PREFIX . "couch_templates` WHERE name='" .
        $db->real_escape_string($cloneTemplateName) . "' LIMIT 1"
    );
    if (!$cloneTpl) {
        throw new RuntimeException('Clone template missing: ' . $cloneTemplateName);
    }
    $cloneId = (int) $cloneTpl['id'];

    if (gl_faq_has_field($db, $fields, $templateId, 'faq_list')) {
        echo "{$templateName}: faq_list already registered\n";
        return $templateId;
    }

    $defs = array(
        array('name' => 'faq_admin_help', 'label' => 'Справка', 'type' => 'message', 'group' => '', 'order' => 0, 'html' => '<div>FAQ admin help</div>'),
        array('name' => 'group_titles', 'label' => 'Заголовки секции', 'type' => 'group', 'group' => '', 'order' => 1, 'html' => ''),
        array('name' => 'faq_main_title', 'label' => 'Заголовок', 'type' => 'text', 'group' => 'group_titles', 'order' => 2, 'html' => '', 'default' => 'Частые вопросы'),
        array('name' => 'faq_subtitle', 'label' => 'Подзаголовок', 'type' => 'text', 'group' => 'group_titles', 'order' => 3, 'html' => '', 'default' => 'FAQ Garden Lounge'),
        array('name' => 'group_faq', 'label' => 'Список вопросов', 'type' => 'group', 'group' => '', 'order' => 4, 'html' => ''),
    );

    foreach ($defs as $def) {
        if (gl_faq_has_field($db, $fields, $templateId, $def['name'])) {
            continue;
        }
        $sample = gl_faq_one(
            $db,
            "SELECT * FROM `{$fields}` WHERE template_id={$cloneId} AND k_type='" .
            $db->real_escape_string($def['type']) . "' LIMIT 1"
        );
        if (!$sample) {
            $sample = gl_faq_one($db, "SELECT * FROM `{$fields}` WHERE k_type='text' LIMIT 1");
        }
        if (!$sample) {
            throw new RuntimeException('No sample field for ' . $def['name']);
        }
        $row = $sample;
        $row['template_id'] = (string) $templateId;
        $row['name'] = $def['name'];
        $row['label'] = $def['label'];
        $row['k_type'] = $def['type'];
        $row['k_group'] = $def['group'];
        $row['k_order'] = (string) $def['order'];
        $row['_html'] = $def['html'];
        if (!empty($def['default'])) {
            $row['default_val'] = $def['default'];
        }
        $fieldId = gl_faq_insert_field($db, $fields, $row);
        echo "Added field {$def['name']} (#{$fieldId}) for {$templateName}\n";
    }

    if (!gl_faq_has_field($db, $fields, $templateId, 'faq_list')) {
        $promo = gl_faq_one(
            $db,
            "SELECT * FROM `{$fields}` WHERE template_id={$cloneId} AND name='promo_list' LIMIT 1"
        );
        if (!$promo) {
            throw new RuntimeException('promo_list sample missing on ' . $cloneTemplateName);
        }
        $promo['template_id'] = (string) $templateId;
        $promo['name'] = 'faq_list';
        $promo['label'] = 'Вопросы и ответы';
        $promo['k_group'] = 'group_faq';
        $promo['k_order'] = '5';
        $promo['_html'] =
            "<cms:editable name='faq_question' label='Вопрос' type='text' />\r\n" .
            "<cms:editable name='faq_answer_html' label='Ответ на сайте (HTML)' type='textarea' />\r\n" .
            "<cms:editable name='faq_answer_schema' label='Текст для поисковиков (без HTML)' type='textarea' />";
        $repeatableId = gl_faq_insert_field($db, $fields, $promo);
        echo "Added repeatable faq_list (#{$repeatableId}) for {$templateName}\n";
    }

    return $templateId;
}

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ?: 3306;
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
}
mysqli_report(MYSQLI_REPORT_OFF);
$db = @new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int) $port);
if ($db->connect_errno) {
    exit("DB connection failed: {$db->connect_error}\n");
}
$db->set_charset('utf8mb4');

try {
    echo "Parsing faq.php...\n";
    gl_faq_parse_template($root . '/faq.php', '/admiralteyskaya/faq.php');
    echo "Parsing udelnaya/faq.php...\n";
    gl_faq_parse_template($root . '/udelnaya/faq.php', '/udelnaya/faq.php');

    $FUNCS->invalidate_cache();

    $faqTpl = gl_faq_one($db, "SELECT id FROM `{$templates}` WHERE name='faq.php' LIMIT 1");
    $udelTpl = gl_faq_one($db, "SELECT id FROM `{$templates}` WHERE name='udelnaya/faq.php' LIMIT 1");
    $faqCount = $faqTpl ? gl_faq_field_count($DB, (int) $faqTpl['id']) : 0;
    $udelCount = $udelTpl ? gl_faq_field_count($DB, (int) $udelTpl['id']) : 0;
    echo "faq.php fields after parse: {$faqCount}\n";
    echo "udelnaya/faq.php fields after parse: {$udelCount}\n";

    if (!$faqCount || !gl_faq_has_field($db, $fields, (int) $faqTpl['id'], 'faq_list')) {
        echo "SQL fallback for faq.php...\n";
        gl_faq_sql_register_fields($db, $fields, 'faq.php', 'akzii.php');
    }
    if (!$udelCount || !gl_faq_has_field($db, $fields, (int) $udelTpl['id'], 'faq_list')) {
        echo "SQL fallback for udelnaya/faq.php...\n";
        gl_faq_sql_register_fields($db, $fields, 'udelnaya/faq.php', 'udelnaya/akzii.php');
    }

    $FUNCS->invalidate_cache();
    $cacheDir = $root . '/couch/cache';
    if (is_dir($cacheDir)) {
        foreach (glob($cacheDir . '/*') as $file) {
            if (is_file($file) && basename($file) !== '.htaccess') {
                @unlink($file);
            }
        }
        echo "Cache cleared\n";
    }

    define('GL_SKIP_CLI_CHECK', true);
    require __DIR__ . '/seed-faq-cli.php';
} catch (Exception $e) {
    http_response_code(500);
    exit($e->getMessage() . "\n");
}
