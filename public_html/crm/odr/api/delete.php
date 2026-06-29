<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'POST only']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$id = is_array($data) ? ($data['id'] ?? '') : '';

if (!preg_match('/^[a-f0-9]{16}$/', $id)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid id']);
    exit;
}

$base = dirname(__DIR__);
$manifestPath = $base . '/reports.json';
$manifest = ['reports' => []];
if (is_file($manifestPath)) {
    $decoded = json_decode(file_get_contents($manifestPath), true);
    if (is_array($decoded['reports'] ?? null)) {
        $manifest['reports'] = $decoded['reports'];
    }
}

$found = null;
$manifest['reports'] = array_values(array_filter(
    $manifest['reports'],
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

$filePath = $base . '/' . ($found['path'] ?? '');
if (is_file($filePath)) {
    unlink($filePath);
}

$encoded = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
file_put_contents($manifestPath, $encoded . "\n");

echo json_encode(['ok' => true]);
