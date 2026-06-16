<?php
/**
 * One-time Taplink visual menu import for CouchCMS.
 * Run on server: php public_html/admiralteyskaya/_maintenance/import-visual-taplink-cli.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

$root = realpath(__DIR__ . '/..');
$dataPath = $root . '/menu/visual/taplink-import-data.json';
if (!is_file($dataPath)) {
    fwrite(STDERR, "Missing data file: {$dataPath}\n");
    exit(1);
}

$payload = json_decode(file_get_contents($dataPath), true);
if (!is_array($payload)) {
    fwrite(STDERR, "Invalid JSON payload\n");
    exit(1);
}

chdir($root);
require_once $root . '/couch/cms.php';

global $AUTH, $FUNCS;

if (!isset($AUTH->user) || !is_object($AUTH->user)) {
    fwrite(STDERR, "Couch auth not initialized\n");
    exit(1);
}

$AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;

$pg = new KWebpage('menu/visual/index.php');
if (!empty($pg->error)) {
    fwrite(STDERR, "Failed to load page: {$pg->err_msg}\n");
    exit(1);
}

$map = [
    'menu_bar' => $payload['bar'] ?? [],
    'menu_kitchen' => $payload['kitchen'] ?? [],
    'menu_desserts' => $payload['desserts'] ?? [],
];

foreach ($map as $fieldName => $rows) {
    if (!isset($pg->_fields[$fieldName])) {
        fwrite(STDERR, "Field not found: {$fieldName}\n");
        exit(1);
    }
    $field = $pg->_fields[$fieldName];
    $field->store_posted_changes($rows, 'db_persist');
    echo "Prepared {$fieldName}: " . count($rows) . " rows\n";
}

$errors = $pg->save('db_persist');
if ($errors) {
    fwrite(STDERR, "Save failed with {$errors} error(s)\n");
    foreach ($pg->fields as $field) {
        if (!empty($field->err_msg)) {
            fwrite(STDERR, " - {$field->name}: {$field->err_msg}\n");
        }
    }
    exit(1);
}

$FUNCS->invalidate_cache();
echo "Import complete.\n";
