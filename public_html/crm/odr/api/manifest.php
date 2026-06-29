<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

require dirname(__DIR__, 2) . '/_lib/storage.php';

echo json_encode(crm_read_json('odr', 'reports.json', ['reports' => []]));
