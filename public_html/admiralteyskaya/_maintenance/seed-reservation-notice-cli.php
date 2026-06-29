<?php
/**
 * Заполняет res_modal_notice на мастер-страницах бронирования (если поле уже зарегистрировано в CouchCMS).
 *
 * CLI: php _maintenance/seed-reservation-notice-cli.php
 */

if (PHP_SAPI !== 'cli' && !defined('GL_SKIP_CLI_CHECK')) {
    http_response_code(403);
    exit("CLI only\n");
}

$root = realpath(__DIR__ . '/..');
$config = $root . '/couch/config.php';
if (!is_file($config)) {
    fwrite(STDERR, "CouchCMS config not found\n");
    exit(1);
}

define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;

$notice = "На компании от 6 человек взимается сервисный сбор 10%.\n\nПосещение Garden Lounge строго с 18 лет.";

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
$db->set_charset('utf8mb4');

$prefix = K_DB_TABLES_PREFIX;
$templates = array('reservation.php', 'udelnaya/reservation.php');
$notice_sql = "'" . $db->real_escape_string($notice) . "'";

foreach ($templates as $template_name) {
    $tpl = $db->real_escape_string($template_name);
    $row = $db->query(
        "SELECT t.id AS template_id, p.id AS page_id, f.id AS field_id
         FROM {$prefix}couch_templates t
         INNER JOIN {$prefix}couch_pages p ON p.template_id = t.id AND p.is_master = '1'
         LEFT JOIN {$prefix}couch_fields f ON f.template_id = t.id AND f.name = 'res_modal_notice'
         WHERE t.name = '{$tpl}'
         LIMIT 1"
    );
    if (!$row || !($data = $row->fetch_assoc())) {
        echo "Skip: template not found ({$template_name})\n";
        continue;
    }

    $page_id = (int)$data['page_id'];
    $field_id = (int)$data['field_id'];

    if (!$field_id) {
        $clone = $db->query(
            "SELECT f.* FROM {$prefix}couch_fields f
             INNER JOIN {$prefix}couch_templates t ON t.id = f.template_id
             WHERE t.name = '{$tpl}' AND f.name = 'res_modal_text'
             LIMIT 1"
        );
        if ($clone && ($src = $clone->fetch_assoc())) {
            unset($src['id']);
            $src['name'] = 'res_modal_notice';
            $src['label'] = 'Дополнительный текст после бронирования';
            $src['k_group'] = 'res_group_modal';
            $src['default_val'] = $notice;
            $src['modified'] = date('Y-m-d H:i:s');
            $cols = array();
            $vals = array();
            foreach ($src as $k => $v) {
                $cols[] = '`' . $k . '`';
                $vals[] = $v === null ? 'NULL' : "'" . $db->real_escape_string((string)$v) . "'";
            }
            if ($db->query('INSERT INTO ' . $prefix . 'couch_fields (' . implode(',', $cols) . ') VALUES (' . implode(',', $vals) . ')')) {
                $field_id = (int)$db->insert_id;
                echo "Created res_modal_notice for {$template_name} (#{$field_id})\n";
            }
        }
    }

    if (!$field_id) {
        echo "Field res_modal_notice missing for {$template_name}; open template in admin once.\n";
        continue;
    }

    $db->query("DELETE FROM {$prefix}couch_data_text WHERE page_id = {$page_id} AND field_id = {$field_id}");
    $db->query("INSERT INTO {$prefix}couch_data_text (page_id, field_id, value, search_value) VALUES ({$page_id}, {$field_id}, {$notice_sql}, {$notice_sql})");
    echo "Seeded notice for {$template_name}\n";
}

require __DIR__ . '/clear-couch-cache-cli.php';
