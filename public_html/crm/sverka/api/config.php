<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

require dirname(__DIR__, 2) . '/_lib/storage.php';

$config = crm_read_json('sverka', 'config.json', []);
if (!$config) {
    $fallback = dirname(__DIR__) . '/config.json';
    if (is_file($fallback)) {
        $decoded = json_decode(file_get_contents($fallback), true);
        if (is_array($decoded)) {
            $config = $decoded;
        }
    }
}
echo json_encode($config);
