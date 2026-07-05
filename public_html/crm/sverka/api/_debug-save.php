<?php
header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

require dirname(__DIR__, 2) . '/_lib/storage.php';
require __DIR__ . '/_archive.php';

echo "storage ok\n";

$archive = sverka_load_archive();
echo 'sessions: ' . count($archive['sessions'] ?? []) . "\n";

$raw = file_get_contents('php://input');
if (!$raw) {
    $path = crm_storage_path('sverka', 'data.json');
    if (is_file($path)) {
        $raw = file_get_contents($path);
    }
}

if (!$raw) {
    echo "no input\n";
    exit;
}

$data = json_decode($raw, true);
if (!is_array($data)) {
    echo 'json_decode fail: ' . json_last_error_msg() . "\n";
    exit;
}

echo 'payload keys: ' . implode(', ', array_keys($data)) . "\n";
echo 'expenses: ' . count($data['expenses'] ?? []) . "\n";
echo 'statements: ' . count($data['statements'] ?? []) . "\n";

try {
    $month = $data['month'] ?? null;
    $entity = $data['entity'] ?? 'lounge';
    if ($month && preg_match('/^\d{4}-\d{2}$/', $month)) {
        $key = $month . '|' . $entity;
        $data['month'] = $month;
        $data['sessionKey'] = $key;
        $archive['sessions'][$key] = $data;
        $archive['activeKey'] = $key;
        crm_write_json('sverka', 'archive.json', $archive);
        echo "archive written\n";
    }
    crm_write_json('sverka', 'data.json', $data);
    echo "data written\n";
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
