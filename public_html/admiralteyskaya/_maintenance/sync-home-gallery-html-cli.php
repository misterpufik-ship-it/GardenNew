<?php
/**
 * Fix home gallery repeatable _html so child fields match home.php.
 */
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
$db = new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int) $port);
$db->set_charset('utf8');

$fields = K_DB_TABLES_PREFIX . 'couch_fields';

function q($db, $value)
{
    return "'" . $db->real_escape_string((string) $value) . "'";
}

function gallery_repeatable_html($imgName, $altName)
{
    return "<cms:editable name='" . $imgName . "' label='Фото' type='image' />\r\n" .
           "<cms:editable name='" . $altName . "' label='Alt / SEO' type='text' />";
}

$map = array(
    'home_adm_gallery' => gallery_repeatable_html('home_adm_gallery_img', 'home_adm_gallery_alt'),
    'home_udel_gallery' => gallery_repeatable_html('home_udel_gallery_img', 'home_udel_gallery_alt'),
);

foreach ($map as $name => $html) {
    $db->query("UPDATE `{$fields}` SET _html=" . q($db, $html) . " WHERE name=" . q($db, $name) . " LIMIT 1");
    echo "Updated _html for {$name}\n";
}

define('GL_SKIP_CLI_CHECK', true);
require __DIR__ . '/clear-couch-cache-cli.php';
