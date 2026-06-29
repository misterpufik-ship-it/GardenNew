<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'POST only']);
    exit;
}

require dirname(__DIR__, 2) . '/_lib/storage.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data) || empty($data['month']) || !preg_match('/^\d{4}-\d{2}$/', $data['month'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid month']);
    exit;
}

$month = $data['month'];
$reportId = $data['reportId'] ?? '';

try {
    crm_ensure_storage('odr', 'analytics');
    $path = crm_storage_path('odr', 'analytics/' . $month . '.json');
    $payload = [
        'month' => $month,
        'reportId' => $reportId,
        'generatedAt' => date('c'),
        'analysis' => $data['analysis'] ?? $data,
    ];
    crm_write_json('odr', 'analytics/' . $month . '.json', $payload);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    exit;
}

echo json_encode(['ok' => true, 'month' => $month]);
