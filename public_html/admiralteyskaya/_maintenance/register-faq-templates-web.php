<?php
/**
 * Register faq.php templates in CouchCMS DB for both branches.
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/register-faq-templates-web.php?token=gl-cache-clear-20260623
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit("Forbidden\n");
}

header('Content-Type: text/plain; charset=utf-8');

$root = realpath(__DIR__ . '/..');
$config = $root . '/couch/config.php';
if (!is_file($config)) {
    exit("CouchCMS config not found\n");
}

define('K_COUCH_DIR', dirname($config) . '/');
require_once $root . '/couch/cms.php';
require_once dirname($root) . '/age-gate/faq-content.php';

global $AUTH, $FUNCS;
if (!isset($AUTH->user) || !is_object($AUTH->user)) {
    exit("Couch auth not initialized\n");
}
$AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;

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

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';

function gl_faq_qval($db, $value)
{
    if ($value === null) {
        return 'NULL';
    }
    return "'" . $db->real_escape_string((string) $value) . "'";
}

function gl_faq_fetch_one($db, $sql)
{
    $res = $db->query($sql);
    return $res ? $res->fetch_assoc() : null;
}

function gl_faq_register_template($db, $templates, $pages, $name, $title, $order, $cloneFrom)
{
    $row = gl_faq_fetch_one(
        $db,
        "SELECT id FROM `{$templates}` WHERE name='" . $db->real_escape_string($name) . "' LIMIT 1"
    );

    if (!$row) {
        $sample = gl_faq_fetch_one(
            $db,
            "SELECT * FROM `{$templates}` WHERE name='" . $db->real_escape_string($cloneFrom) . "' LIMIT 1"
        );
        if (!$sample) {
            throw new RuntimeException("Clone source not found: {$cloneFrom}");
        }
        unset($sample['id']);
        $sample['name'] = $name;
        $sample['title'] = $title;
        $sample['executable'] = '0';
        $sample['hidden'] = '0';
        $sample['clonable'] = '0';
        if (isset($sample['order'])) {
            $sample['order'] = (string) $order;
        }
        $cols = array_keys($sample);
        $vals = array();
        foreach (array_values($sample) as $v) {
            $vals[] = gl_faq_qval($db, $v);
        }
        $sql = 'INSERT INTO `' . $templates . '` (`' . implode('`,`', $cols) . '`) VALUES (' . implode(',', $vals) . ')';
        if (!$db->query($sql)) {
            throw new RuntimeException("Template insert failed for {$name}: {$db->error}");
        }
        $templateId = (int) $db->insert_id;
        echo "Created template {$name} (#{$templateId})\n";
    } else {
        $templateId = (int) $row['id'];
        $db->query(
            "UPDATE `{$templates}` SET executable='0', hidden='0', title='" .
            $db->real_escape_string($title) . "', `order`='" . (int) $order . "' WHERE id={$templateId} LIMIT 1"
        );
        echo "Template {$name} exists (#{$templateId})\n";
    }

    $page = gl_faq_fetch_one(
        $db,
        "SELECT id FROM `{$pages}` WHERE template_id={$templateId} AND is_master='1' LIMIT 1"
    );
    if (!$page) {
        $ref = gl_faq_fetch_one($db, "SELECT * FROM `{$pages}` WHERE template_id={$templateId} LIMIT 1");
        if (!$ref) {
            $ref = gl_faq_fetch_one(
                $db,
                "SELECT * FROM `{$pages}` WHERE template_id=(SELECT id FROM `{$templates}` WHERE name='" .
                $db->real_escape_string($cloneFrom) . "' LIMIT 1) AND is_master='1' LIMIT 1"
            );
        }
        if (!$ref) {
            throw new RuntimeException("Cannot create master page for {$name}");
        }
        unset($ref['id']);
        $now = date('Y-m-d H:i:s');
        $ref['template_id'] = (string) $templateId;
        $ref['page_title'] = $title;
        $ref['page_name'] = 'index';
        $ref['creation_date'] = $now;
        $ref['modification_date'] = $now;
        $ref['publish_date'] = $now;
        $ref['is_master'] = '1';
        $cols = array_keys($ref);
        $vals = array();
        foreach (array_values($ref) as $v) {
            $vals[] = gl_faq_qval($db, $v);
        }
        $sql = 'INSERT INTO `' . $pages . '` (`' . implode('`,`', $cols) . '`) VALUES (' . implode(',', $vals) . ')';
        if (!$db->query($sql)) {
            throw new RuntimeException("Master page insert failed for {$name}: {$db->error}");
        }
        echo "Created master page for {$name}\n";
    }
}

try {
    gl_faq_register_template($db, $templates, $pages, 'faq.php', 'Вопросы и ответы', 205, 'akzii.php');
    gl_faq_register_template($db, $templates, $pages, 'udelnaya/faq.php', 'Уделка Вопросы и ответы', 35, 'udelnaya/akzii.php');

    $FUNCS->invalidate_cache();
    chdir($root);

    foreach (array('faq.php', 'udelnaya/faq.php') as $templateName) {
        $pg = new KWebpage($templateName);
        if (!empty($pg->error)) {
            throw new RuntimeException('KWebpage failed for ' . $templateName . ': ' . $pg->err_msg);
        }
        echo "Loaded fields for {$templateName}\n";
    }

    define('GL_SKIP_CLI_CHECK', true);
    require __DIR__ . '/seed-faq-cli.php';
} catch (Exception $e) {
    http_response_code(500);
    exit($e->getMessage() . "\n");
}
