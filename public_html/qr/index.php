<?php
define('GL_QR_LIB', 1);
require_once __DIR__ . '/lib.php';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$branches = gl_qr_branches();
$branch = isset($_GET['branch']) ? strtolower(trim((string)$_GET['branch'])) : '';
$slug = isset($_GET['slug']) ? strtolower(trim((string)$_GET['slug'])) : 'site';
$branch = preg_replace('/[^a-z0-9-]+/', '', $branch);
$slug = preg_replace('/[^a-z0-9-]+/', '', $slug);

if ($branch === '' || $slug === '' || !isset($branches[$branch])) {
    http_response_code(404);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><meta charset="utf-8"><title>QR не найден</title><p>QR-код не найден.</p>';
    exit;
}

$data = gl_qr_load();
$code = gl_qr_find_by_slug($data, $branch, $slug);
if (!$code) {
    http_response_code(404);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><meta charset="utf-8"><title>QR не найден</title><p>QR-код ещё не создан.</p>';
    exit;
}

$url = gl_qr_normalize_url($code['target_url']);
if ($url === '') {
    http_response_code(404);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><meta charset="utf-8"><title>Ссылка не задана</title><p>Для этого QR ещё не указана ссылка.</p>';
    exit;
}

header('Location: ' . $url, true, 302);
exit;
