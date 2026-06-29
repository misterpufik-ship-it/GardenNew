<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

require dirname(__DIR__, 2) . '/_lib/storage.php';

$data = crm_read_json('marketing', 'reports.json', ['items' => []]);
if (!isset($data['items']) && isset($data['reports'])) {
    $data = ['items' => $data['reports']];
}
echo json_encode($data);
