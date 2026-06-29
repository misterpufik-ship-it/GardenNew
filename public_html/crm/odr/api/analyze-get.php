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

$data = crm_read_json('odr', 'analytics/' . $month . '.json', null);
if (!$data) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'Not found']);
    exit;
}

echo json_encode(['ok' => true, 'data' => $data]);
