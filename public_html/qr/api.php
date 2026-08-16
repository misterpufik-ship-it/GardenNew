<?php
define('GL_QR_LIB', 1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/lib.php';

gl_qr_require_admin(true);

header('Cache-Control: no-store');
header('Content-Type: application/json; charset=utf-8');

function gl_qr_json($payload, $code = 200)
{
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function gl_qr_read_json_body()
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return array();
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : array();
}

$action = isset($_GET['action']) ? (string)$_GET['action'] : 'list';
$method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';

try {
    if ($action === 'list' && $method === 'GET') {
        $data = gl_qr_load();
        $out = array();
        foreach ($data['codes'] as $code) {
            if (!is_file(gl_qr_png_path($code['branch'], $code['slug']))) {
                gl_qr_write_png($code);
            }
            $out[] = gl_qr_public_code($code);
        }
        gl_qr_json(array(
            'ok' => true,
            'branches' => gl_qr_branches(),
            'codes' => $out,
        ));
    }

    if ($action === 'create' && $method === 'POST') {
        $body = gl_qr_read_json_body();
        $branches = gl_qr_branches();
        $branch = isset($body['branch']) ? (string)$body['branch'] : '';
        if (!isset($branches[$branch])) {
            gl_qr_json(array('ok' => false, 'error' => 'Неизвестный филиал'), 400);
        }
        $title = trim(isset($body['title']) ? (string)$body['title'] : '');
        if ($title === '') {
            $title = 'QR ' . $branches[$branch]['label'];
        }
        $slugSource = isset($body['slug']) && trim((string)$body['slug']) !== '' ? $body['slug'] : $title;
        $slug = gl_qr_slug($slugSource);
        $target = gl_qr_normalize_url(isset($body['target_url']) ? $body['target_url'] : '');
        if ($target === '') {
            gl_qr_json(array('ok' => false, 'error' => 'Укажите корректную ссылку (http/https)'), 400);
        }

        $data = gl_qr_load();
        $base = $slug;
        $n = 2;
        while (gl_qr_find_by_slug($data, $branch, $slug)) {
            $slug = $base . '-' . $n;
            $n++;
        }

        $now = date('c');
        $code = array(
            'id' => $branch . '-' . $slug . '-' . substr(bin2hex(random_bytes(4)), 0, 8),
            'branch' => $branch,
            'slug' => $slug,
            'title' => $title,
            'target_url' => $target,
            'created_at' => $now,
            'updated_at' => $now,
        );
        $data['codes'][] = $code;
        gl_qr_save($data);
        gl_qr_write_png($code);
        gl_qr_json(array('ok' => true, 'code' => gl_qr_public_code($code)));
    }

    if ($action === 'update' && $method === 'POST') {
        $body = gl_qr_read_json_body();
        $id = isset($body['id']) ? (string)$body['id'] : '';
        $data = gl_qr_load();
        $idx = gl_qr_find($data, $id);
        if ($idx < 0) {
            gl_qr_json(array('ok' => false, 'error' => 'QR не найден'), 404);
        }
        if (isset($body['title'])) {
            $title = trim((string)$body['title']);
            if ($title !== '') {
                $data['codes'][$idx]['title'] = $title;
            }
        }
        if (array_key_exists('target_url', $body)) {
            $target = gl_qr_normalize_url($body['target_url']);
            if ($target === '') {
                gl_qr_json(array('ok' => false, 'error' => 'Укажите корректную ссылку (http/https)'), 400);
            }
            $data['codes'][$idx]['target_url'] = $target;
        }
        $data['codes'][$idx]['updated_at'] = date('c');
        gl_qr_save($data);
        $code = $data['codes'][$idx];
        gl_qr_json(array('ok' => true, 'code' => gl_qr_public_code($code)));
    }

    if ($action === 'rebuild' && $method === 'POST') {
        gl_qr_json(array('ok' => false, 'error' => 'Картинка QR фиксируется при создании и не пересобирается'), 400);
    }

    gl_qr_json(array('ok' => false, 'error' => 'Неизвестное действие'), 400);
} catch (Throwable $e) {
    gl_qr_json(array('ok' => false, 'error' => $e->getMessage()), 500);
}
