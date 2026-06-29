<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'POST only']);
    exit;
}

require dirname(__DIR__, 2) . '/_lib/storage.php';

$month = $_POST['month'] ?? '';
if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Укажите месяц в формате YYYY-MM']);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Файл не загружен']);
    exit;
}

$name = $_FILES['file']['name'];
if (!preg_match('/\.xlsx$/i', $name)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Только .xlsx']);
    exit;
}

try {
    $uploadDir = crm_uploads_dir('odr', $month);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    exit;
}

$id = bin2hex(random_bytes(8));
$safe = preg_replace('/[^a-zA-Z0-9._\-]/u', '_', $name);
$stored = $id . '_' . $safe;
$dest = $uploadDir . '/' . $stored;

if (!move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Ошибка сохранения']);
    exit;
}

$relativePath = 'storage/odr/uploads/' . $month . '/' . $stored;
$manifest = crm_read_json('odr', 'reports.json', ['reports' => []]);
if (!is_array($manifest['reports'])) {
    $manifest['reports'] = [];
}

$note = trim($_POST['note'] ?? '');
$entry = [
    'id' => $id,
    'month' => $month,
    'originalName' => $name,
    'storedName' => $stored,
    'path' => $relativePath,
    'size' => filesize($dest),
    'note' => $note,
    'uploadedAt' => date('c'),
];

$manifest['reports'][] = $entry;
usort($manifest['reports'], function ($a, $b) {
    $cmp = strcmp($b['month'], $a['month']);
    return $cmp !== 0 ? $cmp : strcmp($b['uploadedAt'], $a['uploadedAt']);
});

try {
    crm_write_json('odr', 'reports.json', $manifest);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    exit;
}

echo json_encode(['ok' => true, 'report' => $entry]);
