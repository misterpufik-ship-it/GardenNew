<?php
/**
 * HTTP one-time import (token required). Delete after use.
 * /admiralteyskaya/_maintenance/import-visual-taplink.php?token=...
 */

$expected = 'garden-visual-' . substr(md5('loungegarden-menu-visual-2026'), 0, 12);
if (($_GET['token'] ?? '') !== $expected) {
    http_response_code(403);
    exit('Forbidden');
}

header('Content-Type: text/plain; charset=utf-8');

$root = realpath(__DIR__ . '/..');
$dataPath = $root . '/menu/visual/taplink-import-data.json';
if (!is_file($dataPath)) {
    http_response_code(500);
    exit("Missing data file\n");
}

$payload = json_decode(file_get_contents($dataPath), true);
if (!is_array($payload)) {
    http_response_code(500);
    exit("Invalid JSON payload\n");
}

chdir($root);
require_once $root . '/couch/cms.php';

global $AUTH, $FUNCS;

$AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;

$pg = new KWebpage('menu/visual/index.php');
if (!empty($pg->error)) {
    http_response_code(500);
    exit("Failed to load page: {$pg->err_msg}\n");
}

$map = [
    'menu_bar' => $payload['bar'] ?? [],
    'menu_kitchen' => $payload['kitchen'] ?? [],
    'menu_desserts' => $payload['desserts'] ?? [],
];

foreach ($map as $fieldName => $rows) {
    if (!isset($pg->_fields[$fieldName])) {
        http_response_code(500);
        exit("Field not found: {$fieldName}\n");
    }
    $pg->_fields[$fieldName]->store_posted_changes($rows, 'db_persist');
    echo "Prepared {$fieldName}: " . count($rows) . " rows\n";
}

$errors = $pg->save('db_persist');
if ($errors) {
    http_response_code(500);
    echo "Save failed: {$errors} error(s)\n";
    foreach ($pg->fields as $field) {
        if (!empty($field->err_msg)) {
            echo " - {$field->name}: {$field->err_msg}\n";
        }
    }
    exit(1);
}

$FUNCS->invalidate_cache();
echo "Import complete.\n";
