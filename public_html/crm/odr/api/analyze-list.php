<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

require dirname(__DIR__, 2) . '/_lib/storage.php';

$dir = crm_storage_dir('odr') . '/analytics';
$items = [];
if (is_dir($dir)) {
    foreach (glob($dir . '/*.json') as $file) {
        $month = basename($file, '.json');
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            continue;
        }
        $decoded = json_decode(file_get_contents($file), true);
        $items[] = [
            'month' => $month,
            'reportId' => $decoded['reportId'] ?? '',
            'generatedAt' => $decoded['generatedAt'] ?? null,
            'fileName' => $decoded['analysis']['meta']['fileName'] ?? ($decoded['meta']['fileName'] ?? ''),
        ];
    }
}

usort($items, function ($a, $b) {
    return strcmp($b['month'], $a['month']);
});

echo json_encode(['items' => $items]);
