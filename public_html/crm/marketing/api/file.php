<?php
require dirname(__DIR__, 2) . '/_lib/storage.php';

$id = $_GET['id'] ?? '';
if (!preg_match('/^[a-f0-9]{16}$/', $id)) {
    http_response_code(400);
    exit('Invalid id');
}

$manifest = crm_read_json('marketing', 'reports.json', ['items' => []]);
$item = null;
foreach ($manifest['items'] ?? [] as $row) {
    if (($row['id'] ?? '') === $id) {
        $item = $row;
        break;
    }
}

if (!$item) {
    http_response_code(404);
    exit('Not found');
}

$path = crm_root() . '/' . ltrim($item['path'], '/');
if (!is_file($path)) {
    $path = crm_root() . '/marketing/' . ltrim($item['path'], '/');
}

if (!is_file($path)) {
    http_response_code(404);
    exit('File missing');
}

$name = $item['originalName'] ?? basename($path);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . rawurlencode($name) . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
