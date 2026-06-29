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
$id = is_array($data) ? ($data['id'] ?? '') : '';

if (!preg_match('/^[a-f0-9]{16}$/', $id)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid id']);
    exit;
}

$manifest = crm_read_json('marketing', 'reports.json', ['items' => []]);
$found = null;
$manifest['items'] = array_values(array_filter(
    $manifest['items'] ?? [],
    function ($item) use ($id, &$found) {
        if (($item['id'] ?? '') === $id) {
            $found = $item;
            return false;
        }
        return true;
    }
));

if (!$found) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'Not found']);
    exit;
}

foreach ([
    crm_root() . '/' . ltrim($found['path'] ?? '', '/'),
    crm_root() . '/marketing/' . ltrim($found['path'] ?? '', '/'),
] as $filePath) {
    if ($filePath && is_file($filePath)) {
        unlink($filePath);
        break;
    }
}

try {
    crm_write_json('marketing', 'reports.json', $manifest);
} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    exit;
}

echo json_encode(['ok' => true]);
