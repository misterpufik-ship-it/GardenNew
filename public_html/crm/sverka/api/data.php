<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

require __DIR__ . '/_archive.php';

$archive = sverka_load_archive();
$key = $_GET['key'] ?? $archive['activeKey'];
$session = ($key && isset($archive['sessions'][$key])) ? $archive['sessions'][$key] : null;

if (!$session) {
    echo json_encode([]);
    exit;
}

echo json_encode($session, JSON_UNESCAPED_UNICODE);
