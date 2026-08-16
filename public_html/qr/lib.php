<?php
if (!defined('GL_QR_LIB')) {
    die('No direct access');
}

function gl_qr_branches()
{
    return array(
        'admiralteyskaya' => array(
            'label' => 'Адмиралтейская',
            'default_url' => 'https://garden-lounge.pro/admiralteyskaya',
        ),
        'udelnaya' => array(
            'label' => 'Удельная',
            'default_url' => 'https://garden-lounge.pro/udelnaya',
        ),
    );
}

function gl_qr_root()
{
    return str_replace('\\', '/', __DIR__);
}

function gl_qr_storage_dir()
{
    return gl_qr_root() . '/storage';
}

function gl_qr_png_dir()
{
    return gl_qr_storage_dir() . '/png';
}

function gl_qr_json_path()
{
    return gl_qr_storage_dir() . '/codes.json';
}

function gl_qr_public_base()
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (string)$_SERVER['SERVER_PORT'] === '443')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'garden-lounge.pro';
    return ($https ? 'https://' : 'http://') . $host;
}

function gl_qr_ensure_storage()
{
    $dirs = array(gl_qr_storage_dir(), gl_qr_png_dir());
    foreach ($dirs as $dir) {
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Не удалось создать папку хранения QR');
        }
    }
}

function gl_qr_default_payload()
{
    $now = date('c');
    $codes = array();
    foreach (gl_qr_branches() as $branch => $info) {
        $codes[] = array(
            'id' => $branch . '-site',
            'branch' => $branch,
            'slug' => 'site',
            'title' => 'Сайт филиала',
            'target_url' => $info['default_url'],
            'created_at' => $now,
            'updated_at' => $now,
        );
    }
    return array('version' => 1, 'codes' => $codes);
}

function gl_qr_load()
{
    gl_qr_ensure_storage();
    $path = gl_qr_json_path();
    if (!is_file($path)) {
        $data = gl_qr_default_payload();
        gl_qr_save($data);
        foreach ($data['codes'] as $code) {
            gl_qr_write_png($code);
        }
        return $data;
    }
    $raw = file_get_contents($path);
    $data = json_decode($raw, true);
    if (!is_array($data) || !isset($data['codes']) || !is_array($data['codes'])) {
        $data = gl_qr_default_payload();
        gl_qr_save($data);
    }
    return $data;
}

function gl_qr_save($data)
{
    gl_qr_ensure_storage();
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    if ($json === false) {
        throw new RuntimeException('Не удалось сохранить QR');
    }
    $path = gl_qr_json_path();
    $tmp = $path . '.tmp';
    if (file_put_contents($tmp, $json, LOCK_EX) === false) {
        throw new RuntimeException('Не удалось записать QR');
    }
    if (!rename($tmp, $path)) {
        @unlink($path);
        if (!rename($tmp, $path)) {
            throw new RuntimeException('Не удалось обновить QR');
        }
    }
}

function gl_qr_stable_url($branch, $slug)
{
    return gl_qr_public_base() . '/qr/' . rawurlencode($branch) . '/' . rawurlencode($slug);
}

function gl_qr_png_path($branch, $slug)
{
    return gl_qr_png_dir() . '/' . preg_replace('/[^a-z0-9-]+/', '-', $branch . '__' . $slug) . '.png';
}

function gl_qr_normalize_url($url)
{
    $url = trim((string)$url);
    if ($url === '') {
        return '';
    }
    if (isset($url[0]) && $url[0] === '/') {
        $url = gl_qr_public_base() . $url;
    }
    if (!preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }
    $parts = parse_url($url);
    if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
        return '';
    }
    $scheme = strtolower($parts['scheme']);
    if ($scheme !== 'http' && $scheme !== 'https') {
        return '';
    }
    return $url;
}

function gl_qr_slug($value)
{
    $value = trim((string)$value);
    if (function_exists('mb_strtolower')) {
        $value = mb_strtolower($value, 'UTF-8');
    } else {
        $value = strtolower($value);
    }
    $map = array(
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e',
        'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm',
        'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
        'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => '',
        'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
    );
    $value = strtr($value, $map);
    $value = preg_replace('/[^a-z0-9]+/', '-', $value);
    $value = trim($value, '-');
    if ($value === '') {
        $value = 'qr';
    }
    return substr($value, 0, 40);
}

function gl_qr_find(&$data, $id)
{
    foreach ($data['codes'] as $i => $code) {
        if (isset($code['id']) && $code['id'] === $id) {
            return $i;
        }
    }
    return -1;
}

function gl_qr_find_by_slug($data, $branch, $slug)
{
    foreach ($data['codes'] as $code) {
        if ($code['branch'] === $branch && $code['slug'] === $slug) {
            return $code;
        }
    }
    return null;
}

function gl_qr_write_png($code)
{
    $path = gl_qr_png_path($code['branch'], $code['slug']);
    if (is_file($path)) {
        return $path;
    }
    if (!function_exists('imagecreatetruecolor')) {
        throw new RuntimeException('На сервере нет GD для генерации PNG');
    }
    require_once gl_qr_root() . '/lib/qrcode.php';
    $url = gl_qr_stable_url($code['branch'], $code['slug']);
    $qr = QRCode::getMinimumQRCode($url, QR_ERROR_CORRECT_LEVEL_M);
    $im = $qr->createImage(10, 16, 0x111111, 0xFFFFFF);
    imagepng($im, $path, 6);
    imagedestroy($im);
    return $path;
}

function gl_qr_public_code($code)
{
    $pngRel = '/qr/storage/png/' . basename(gl_qr_png_path($code['branch'], $code['slug']));
    $pngFile = gl_qr_png_path($code['branch'], $code['slug']);
    return array(
        'id' => $code['id'],
        'branch' => $code['branch'],
        'slug' => $code['slug'],
        'title' => $code['title'],
        'target_url' => $code['target_url'],
        'stable_url' => gl_qr_stable_url($code['branch'], $code['slug']),
        'png_url' => $pngRel . (is_file($pngFile) ? ('?v=' . filemtime($pngFile)) : ''),
        'created_at' => isset($code['created_at']) ? $code['created_at'] : '',
        'updated_at' => isset($code['updated_at']) ? $code['updated_at'] : '',
    );
}
