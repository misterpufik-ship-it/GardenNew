<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

require dirname(__DIR__, 2) . '/_lib/storage.php';

$data = crm_read_json('tasks', 'tasks.json', ['tasks' => []]);
echo json_encode($data);
