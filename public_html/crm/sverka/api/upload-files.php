<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'POST only']);
    exit;
}

require_once dirname(__DIR__, 2) . '/_lib/storage.php';

function sverka_upload_error(string $field): ?string
{
    if (!isset($_FILES[$field])) {
        return null;
    }
    $err = (int) $_FILES[$field]['error'];
    if ($err === UPLOAD_ERR_OK) {
        return null;
    }
    $name = $_FILES[$field]['name'] ?? $field;
    $messages = [
        UPLOAD_ERR_INI_SIZE => 'Файл слишком большой (лимит сервера)',
        UPLOAD_ERR_FORM_SIZE => 'Файл слишком большой',
        UPLOAD_ERR_PARTIAL => 'Файл загружен не полностью',
        UPLOAD_ERR_NO_FILE => 'Файл не выбран',
        UPLOAD_ERR_NO_TMP_DIR => 'Нет временной папки на сервере',
        UPLOAD_ERR_CANT_WRITE => 'Нет места на диске или нет прав записи',
        UPLOAD_ERR_EXTENSION => 'Загрузка заблокирована расширением PHP',
    ];
    $msg = $messages[$err] ?? ('Код ошибки загрузки: ' . $err);
    return $name . ': ' . $msg;
}

$entity = $_POST['entity'] ?? 'lounge';
if (!in_array($entity, ['lounge', 'vympel'], true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Неверное юрлицо']);
    exit;
}

function sverka_store_upload(string $entity, string $field, string $kind): ?array
{
    $uploadErr = sverka_upload_error($field);
    if ($uploadErr !== null) {
        throw new RuntimeException($uploadErr);
    }
    if (!isset($_FILES[$field])) {
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
