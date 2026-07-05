<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

require dirname(__DIR__, 2) . '/_lib/storage.php';

$stored = crm_read_json('sverka', 'instructions.json', []);
$text = $stored['text'] ?? null;

if ($text === null || $text === '') {
    $path = dirname(__DIR__) . '/ИНСТРУКЦИЯ_сверка_расходов_БН.md';
    if (is_file($path)) {
        $text = file_get_contents($path);
        if ($text) {
            crm_write_json('sverka', 'instructions.json', [
                'text' => $text,
                'updatedAt' => date('c'),
            ]);
        }
    }
}

echo json_encode([
    'ok' => true,
    'text' => $text ?? '',
    'updatedAt' => $stored['updatedAt'] ?? null,
], JSON_UNESCAPED_UNICODE);
