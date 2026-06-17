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

foreach (array('gallery.php', 'udelnaya/gallery.php') as $templateName) {
    echo "\n=== {$templateName} ===\n";
    $pg = new KWebpage($templateName);
    if (!empty($pg->error)) {
        echo "Error: {$pg->err_msg}\n";
        continue;
    }

    foreach (array('gallery_interior_items', 'gallery_menu_items', 'gallery_vibe_items', 'gallery_items') as $fieldName) {
        $field = null;
        if (isset($pg->_fields[$fieldName])) {
            $field = $pg->_fields[$fieldName];
        } elseif (isset($pg->fields[$fieldName])) {
            $field = $pg->fields[$fieldName];
        }

        if (!$field) {
            echo "{$fieldName}: missing\n";
            continue;
        }

        $count = 0;
        if (method_exists($field, 'get_rows')) {
            $rows = $field->get_rows(true);
            $count = count($rows);
            if ($rows) {
                $first = $rows[0];
                $img = is_array($first) && isset($first['gallery_img']) ? $first['gallery_img'] : '';
                if (is_object($img) && method_exists($img, 'get_data')) {
                    $img = $img->get_data();
                }
                echo "{$fieldName}: {$count} rows, first img=" . substr((string) $img, 0, 80) . "\n";
            } else {
                echo "{$fieldName}: 0 rows\n";
            }
        } else {
            echo "{$fieldName}: no get_rows\n";
        }
    }
}
