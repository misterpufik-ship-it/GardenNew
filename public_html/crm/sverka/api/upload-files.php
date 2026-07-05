<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'POST only']);
    exit;
}

require dirname(__DIR__, 2) . '/_lib/storage.php';

$entity = $_POST['entity'] ?? 'lounge';
if (!in_array($entity, ['lounge', 'vympel'], true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Неверное юрлицо']);
    exit;
}

function sverka_store_upload(string $entity, string $field, string $kind): ?array
{
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $name = $_FILES[$field]['name'];
    if (!preg_match('/\.xlsx$/i', $name)) {
        throw new RuntimeException('Только .xlsx: ' . $name);
    }

    $stamp = date('Y-m-d_His');
    $uploadDir = crm_uploads_dir('sverka', $entity, $stamp);
    $id = bin2hex(random_bytes(6));
    $safe = preg_replace('/[^a-zA-Z0-9._\-]/u', '_', $name);
    $stored = $id . '_' . $safe;
    $dest = $uploadDir . '/' . $stored;

    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $dest)) {
        throw new RuntimeException('Ошибка сохранения: ' . $name);
    }

    return [
        'id' => $id,
        'kind' => $kind,
        'originalName' => $name,
        'storedName' => $stored,
        'path' => 'storage/sverka/uploads/' . $entity . '/' . $stamp . '/' . $stored,
        'size' => filesize($dest) ?: 0,
        'uploadedAt' => date('c'),
    ];
}

try {
    $out = [
        'entity' => $entity,
        'report' => sverka_store_upload($entity, 'report', 'report'),
        'statement' => sverka_store_upload($entity, 'statement', 'statement'),
    ];
    if (!$out['report'] && !$out['statement']) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Файлы не получены']);
        exit;
    }
    echo json_encode(['ok' => true, 'files' => $out], JSON_UNESCAPED_UNICODE);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
