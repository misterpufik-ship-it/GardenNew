<?php
/**
 * Регистрирует шаблоны home.php и site-home.php в CouchCMS и очищает кэш.
 *
 * CLI:
 *   php _maintenance/register-home-templates-cli.php
 *
 * HTTP:
 *   /admiralteyskaya/_maintenance/register-home-templates-cli.php?key=<md5>
 *   key = md5('garden-lounge-register-home')
 */

$isWeb = (PHP_SAPI !== 'cli');
if ($isWeb) {
    $expectedKey = md5('garden-lounge-register-home');
    $providedKey = isset($_GET['key']) ? $_GET['key'] : '';
    if ($providedKey !== $expectedKey) {
        http_response_code(403);
        exit("Forbidden\n");
    }
    header('Content-Type: text/plain; charset=utf-8');
}

$branchDir = dirname(__DIR__);

$targets = array(
    array(
        'dir' => $branchDir,
        'script' => 'home.php',
        'uri' => '/admiralteyskaya/home.php',
    ),
);

foreach ($targets as $target) {
    chdir($target['dir']);
    $_SERVER['HTTP_HOST'] = 'garden-lounge.pro';
    $_SERVER['REQUEST_URI'] = $target['uri'];
    $_SERVER['SCRIPT_NAME'] = $target['uri'];
    $_SERVER['SCRIPT_FILENAME'] = $target['dir'] . '/' . $target['script'];
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

    ob_start();
    require $target['script'];
    ob_end_clean();

    echo "Registered: {$target['uri']}\n";
}

$config = $branchDir . '/couch/config.php';
if (is_file($config)) {
    if (!defined('K_COUCH_DIR')) {
        define('K_COUCH_DIR', dirname($config) . '/');
        require_once $config;
    }
    if (!isset($FUNCS)) {
        require_once K_COUCH_DIR . 'functions.php';
    }

    $templates = K_DB_TABLES_PREFIX . 'couch_templates';
    $pages = K_DB_TABLES_PREFIX . 'couch_pages';

    $host = K_DB_HOST;
    $port = ini_get('mysqli.default_port') ? ini_get('mysqli.default_port') : 3306;
    if (strpos($host, ':') !== false) {
        $parts = explode(':', $host, 2);
        $host = $parts[0];
        $port = $parts[1];
    }

    mysqli_report(MYSQLI_REPORT_OFF);
    $db = @new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
    if (!$db->connect_errno) {
        $db->set_charset('utf8');

        $fix = array(
            array('name' => 'home.php', 'executable' => 1, 'hidden' => 0, 'title' => 'Главная'),
        );

        foreach ($fix as $item) {
            $res = $db->query(
                "SELECT id FROM `{$templates}` WHERE name='" . $db->real_escape_string($item['name']) . "' LIMIT 1"
            );
            $row = $res ? $res->fetch_assoc() : null;
            if (!$row) {
                echo "Template not found after registration: {$item['name']}\n";
                continue;
            }

            $templateId = (int)$row['id'];
            $db->query(
                "UPDATE `{$templates}` SET executable='{$item['executable']}', hidden='{$item['hidden']}', title='" .
                $db->real_escape_string($item['title']) . "' WHERE id={$templateId} LIMIT 1"
            );
            echo "Updated {$item['name']}: executable={$item['executable']}, hidden={$item['hidden']}\n";

            if ($item['executable']) {
                $pageRes = $db->query("SELECT id FROM `{$pages}` WHERE template_id={$templateId} LIMIT 1");
                $page = $pageRes ? $pageRes->fetch_assoc() : null;
                if (!$page) {
                    $now = date('Y-m-d H:i:s');
                    $db->query(
                        "INSERT INTO `{$pages}` (template_id, page_title, page_name, creation_date, modification_date, publish_date, status) VALUES (" .
                        "{$templateId}, 'Главная', 'index', '{$now}', '{$now}', '{$now}', 0)"
                    );
                    echo "Created default page for {$item['name']}\n";
                }
            }
        }

        $db->close();
    } else {
        echo "DB fix skipped: {$db->connect_error}\n";
    }
}

define('GL_SKIP_CLI_CHECK', true);
require __DIR__ . '/clear-couch-cache-cli.php';
