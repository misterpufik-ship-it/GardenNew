<?php
/**
 * HTTP one-time import (token required). Delete after use.
 * /admiralteyskaya/_maintenance/import-visual-taplink.php?token=...&branch=udelnaya
 */

$expected = 'garden-visual-' . substr(md5('loungegarden-menu-visual-2026'), 0, 12);
if (($_GET['token'] ?? '') !== $expected) {
    http_response_code(403);
    exit('Forbidden');
}

header('Content-Type: text/plain; charset=utf-8');

$branch = isset($_GET['branch']) ? $_GET['branch'] : 'admiralteyskaya';
$templates = [
    'admiralteyskaya' => 'menu/visual/index.php',
    'udelnaya' => 'udelnaya/menu/visual/index.php',
];

if (!isset($templates[$branch])) {
    http_response_code(400);
    exit("Unknown branch: {$branch}\n");
}

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

$templateName = $templates[$branch];
echo "Importing Taplink menu for {$branch} ({$templateName})\n";

$pg = new KWebpage($templateName);
if (!empty($pg->error)) {
    http_response_code(500);
    exit("Failed to load page: {$pg->err_msg}\n");
}

$map = [
    'menu_bar' => isset($payload['bar']) ? $payload['bar'] : [],
    'menu_kitchen' => isset($payload['kitchen']) ? $payload['kitchen'] : [],
    'menu_desserts' => isset($payload['desserts']) ? $payload['desserts'] : [],
];

foreach ($map as $fieldName => $rows) {
    if (!isset($pg->_fields[$fieldName])) {
        http_response_code(500);
        exit("Field not found: {$fieldName}\n");
    }
    $field = $pg->_fields[$fieldName];
    $field->store_posted_changes($rows, 'db_persist');
    echo "Prepared {$fieldName}: " . count($rows) . " rows\n";
}

$errors = $pg->save('db_persist');
if ($errors) {
    http_response_code(500);
    exit("Save failed with {$errors} error(s)\n");
}

$FUNCS->invalidate_cache();
echo "Import complete.\n";
