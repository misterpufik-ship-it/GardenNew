<?php
$root = realpath(__DIR__ . '/..');
chdir($root);
require_once $root . '/couch/cms.php';
global $DB;

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';
$dataText = K_DB_TABLES_PREFIX . 'couch_data_text';

$tplRows = $DB->select($templates, array('id'), "name='home.php' LIMIT 1");
$pageRows = $DB->select($pages, array('id', 'page_title'), "template_id='" . $DB->sanitize($tplRows[0]['id']) . "' LIMIT 1");
$pageId = (int) $pageRows[0]['id'];
echo "home page #{$pageId} " . $pageRows[0]['page_title'] . "\n";

$fieldRows = $DB->select($fields, array('id', 'name', 'k_type'), "template_id='" . $DB->sanitize($tplRows[0]['id']) . "' AND (name LIKE '%gallery%' OR name LIKE '%btn%') ORDER BY k_order");
foreach ($fieldRows as $row) {
    if ($row['k_type'] === '__repeatable') {
        $htmlRows = $DB->select($fields, array('_html'), "id='" . $DB->sanitize($row['id']) . "' LIMIT 1");
        if (count($htmlRows)) {
            echo "  _html: " . substr($htmlRows[0]['_html'], 0, 200) . "\n";
        }
    }
    $dataRows = $DB->select($dataText, array('value'), "page_id='" . $DB->sanitize($pageId) . "' AND field_id='" . $DB->sanitize($row['id']) . "' LIMIT 1");
    $len = (count($dataRows) && $dataRows[0]['value']) ? strlen($dataRows[0]['value']) : 0;
    echo $row['id'] . ' ' . $row['name'] . ' [' . $row['k_type'] . '] len=' . $len . "\n";
    if ($len) {
        echo '  preview: ' . substr($dataRows[0]['value'], 0, 220) . "\n";
        $un = @unserialize($dataRows[0]['value']);
        if (is_array($un)) {
            echo '  rows=' . count($un) . "\n";
            if (isset($un[0]) && is_array($un[0])) {
                echo '  keys: ' . implode(', ', array_keys($un[0])) . "\n";
            }
        }
    }
}
