<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'POST only']);
    exit;
}

require_once dirname(__DIR__, 2) . '/_lib/storage.php';
require_once __DIR__ . '/_archive.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid JSON']);
    exit;
}

try {
    $month = $data['month'] ?? null;
    $entity = $data['entity'] ?? 'lounge';
    if ($month && preg_match('/^\d{4}-\d{2}$/', $month)) {
        $key = $month . '|' . $entity;
        $data['month'] = $month;
        $data['sessionKey'] = $key;
        $archive = sverka_load_archive();
        $archive['sessions'][$key] = $data;
        $archive['activeKey'] = $key;
        crm_write_json('sverka', 'archive.json', $archive);
    }
    crm_write_json('sverka', 'data.json', $data);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    exit;
}

echo json_encode(['ok' => true]);
