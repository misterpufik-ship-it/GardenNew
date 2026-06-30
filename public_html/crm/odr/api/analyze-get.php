<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

require dirname(__DIR__, 2) . '/_lib/storage.php';

$month = $_GET['month'] ?? '';
if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid month']);
    exit;
}

$path = crm_storage_path('odr', 'analytics/' . $month . '.json');
if (!is_file($path)) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'Not found']);
    exit;
}

$raw = file_get_contents($path);
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Invalid cache file']);
    exit;
}

echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
