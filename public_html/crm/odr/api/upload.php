<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'POST only']);
    exit;
}

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

$base = dirname(__DIR__);
$uploadDir = $base . '/uploads/' . $month;
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Не удалось создать папку']);
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

$manifestPath = $base . '/reports.json';
$manifest = ['reports' => []];
if (is_file($manifestPath)) {
    $decoded = json_decode(file_get_contents($manifestPath), true);
    if (is_array($decoded['reports'] ?? null)) {
        $manifest['reports'] = $decoded['reports'];
    }
}

$note = trim($_POST['note'] ?? '');
$entry = [
    'id' => $id,
    'month' => $month,
    'originalName' => $name,
    'storedName' => $stored,
    'path' => 'uploads/' . $month . '/' . $stored,
    'size' => filesize($dest),
    'note' => $note,
    'uploadedAt' => date('c'),
];

$manifest['reports'][] = $entry;
usort($manifest['reports'], function ($a, $b) {
    $cmp = strcmp($b['month'], $a['month']);
    return $cmp !== 0 ? $cmp : strcmp($b['uploadedAt'], $a['uploadedAt']);
});

$encoded = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if ($encoded === false || file_put_contents($manifestPath, $encoded . "\n") === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Ошибка записи реестра']);
    exit;
}

echo json_encode(['ok' => true, 'report' => $entry]);
