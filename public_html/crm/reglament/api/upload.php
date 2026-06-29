<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'POST only']);
    exit;
}

$category = trim($_POST['category'] ?? 'other');
$allowedCat = ['operations', 'finance', 'hr', 'safety', 'service', 'other'];
if (!in_array($category, $allowedCat, true)) {
    $category = 'other';
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Файл не загружен']);
    exit;
}

$name = $_FILES['file']['name'];
if (!preg_match('/\.(xlsx|xls|pdf|doc|docx)$/i', $name)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Допустимы: xlsx, xls, pdf, doc, docx']);
    exit;
}

$base = dirname(__DIR__);
$uploadDir = $base . '/uploads/' . $category;
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
$manifest = ['items' => []];
if (is_file($manifestPath)) {
    $decoded = json_decode(file_get_contents($manifestPath), true);
    if (is_array($decoded['items'] ?? null)) {
        $manifest['items'] = $decoded['items'];
    }
}

$note = trim($_POST['note'] ?? '');
$version = trim($_POST['version'] ?? '');
$entry = [
    'id' => $id,
    'category' => $category,
    'originalName' => $name,
    'storedName' => $stored,
    'path' => 'uploads/' . $category . '/' . $stored,
    'size' => filesize($dest),
    'note' => $note,
    'version' => $version,
    'uploadedAt' => date('c'),
];

$manifest['items'][] = $entry;
usort($manifest['items'], function ($a, $b) {
    $cmp = strcmp($a['category'], $b['category']);
    return $cmp !== 0 ? $cmp : strcmp($b['uploadedAt'], $a['uploadedAt']);
});

$encoded = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if ($encoded === false || file_put_contents($manifestPath, $encoded . "\n") === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Ошибка записи реестра']);
    exit;
}

echo json_encode(['ok' => true, 'item' => $entry]);
