<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

require dirname(__DIR__, 2) . '/_lib/storage.php';
require __DIR__ . '/_defaults.php';

$stored = crm_read_json('sverka', 'config.json', []);
$config = sverka_merge_config(is_array($stored) ? $stored : []);

if ($stored && ($stored['paymentFilter'] ?? null) !== ($config['paymentFilter'] ?? null)) {
    try {
        crm_write_json('sverka', 'config.json', $config);
    } catch (RuntimeException $e) {
        // ignore repair errors on read
    }
}

echo json_encode($config, JSON_UNESCAPED_UNICODE);
