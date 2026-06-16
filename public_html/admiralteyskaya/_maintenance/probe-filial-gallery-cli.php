<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

$root = realpath(__DIR__ . '/..');
chdir($root);
require_once $root . '/couch/cms.php';

global $AUTH;
$AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;

foreach (array('filial.php', 'udelnaya/filial.php') as $templateName) {
    echo "\n=== {$templateName} ===\n";
    $pg = new KWebpage($templateName);
    if (!empty($pg->error)) {
        echo "Error: {$pg->err_msg}\n";
        continue;
    }

    $names = array();
    foreach ($pg->fields as $name => $field) {
        $names[] = $name;
    }
    echo 'Fields: ' . implode(', ', $names) . "\n";

    if (!isset($pg->fields['final_gallery_items'])) {
        echo "final_gallery_items field missing\n";
        if (isset($pg->fields['final_gallery'])) {
            echo "final_gallery field exists instead\n";
        }
        continue;
    }

    $field = $pg->fields['final_gallery_items'];
    if (method_exists($field, 'get_rows')) {
        $rows = $field->get_rows(true);
        echo 'Rows via get_rows: ' . count($rows) . "\n";
        if ($rows) {
            print_r($rows[0]);
        }
    } else {
        echo 'Field class: ' . get_class($field) . "\n";
    }
}
