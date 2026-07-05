<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

require __DIR__ . '/_archive.php';

$archive = sverka_load_archive();
$key = $_GET['key'] ?? null;

if ($key) {
    $session = $archive['sessions'][$key] ?? null;
    if (!$session) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'Сессия не найдена']);
        exit;
    }
    echo json_encode(['ok' => true, 'session' => $session, 'activeKey' => $archive['activeKey']], JSON_UNESCAPED_UNICODE);
    exit;
}

$sessions = [];
foreach ($archive['sessions'] as $k => $s) {
    $sessions[] = [
        'key' => $k,
        'month' => $s['month'] ?? substr($k, 0, 7),
        'entity' => $s['entity'] ?? 'lounge',
        'reportFile' => $s['reportFile'] ?? '',
        'statementFile' => $s['statementFile'] ?? '',
        'reconciledAt' => $s['reconciledAt'] ?? null,
        'stats' => $s['stats'] ?? null,
    ];
}

usort($sessions, static function ($a, $b) {
    return strcmp($b['month'] . $b['entity'], $a['month'] . $a['entity']);
});

echo json_encode([
    'ok' => true,
    'activeKey' => $archive['activeKey'],
    'sessions' => $sessions,
], JSON_UNESCAPED_UNICODE);
