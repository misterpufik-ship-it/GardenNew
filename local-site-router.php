<?php
/**
 * Local preview router for the PHP built-in server.
 * Docroot must be public_html. Does not redirect to production HTTPS.
 *
 * php -S 127.0.0.1:8090 -t public_html local-site-router.php
 */

$docroot = realpath(__DIR__ . '/public_html');
if ($docroot === false) {
    http_response_code(500);
    echo 'public_html not found';
    return true;
}
$docroot = str_replace('\\', '/', $docroot);

$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uriPath = rawurldecode($uriPath === null || $uriPath === '' ? '/' : $uriPath);
$uriPath = str_replace('\\', '/', $uriPath);
$originalPath = $uriPath;

if (preg_match('#^/udelnaya/couch/uploads/(.*)$#', $uriPath, $m)) {
    $uriPath = '/admiralteyskaya/couch/uploads/' . $m[1];
}

if (preg_match('#^/qr/([a-z0-9-]+)/([a-z0-9-]+)/?$#', $uriPath, $qrMatch)) {
    $_GET['branch'] = $qrMatch[1];
    $_GET['slug'] = $qrMatch[2];
    $uriPath = '/qr/index.php';
} elseif (preg_match('#^/qr/([a-z0-9-]+)/?$#', $uriPath, $qrMatch)) {
    $_GET['branch'] = $qrMatch[1];
    $_GET['slug'] = 'site';
    $uriPath = '/qr/index.php';
}

$explicit = array(
    '/' => '/index.php',
    '/admiralteyskaya' => '/admiralteyskaya/index.php',
    '/udelnaya' => '/admiralteyskaya/udelnaya/index.php',
    '/admiralteyskaya/menu' => '/admiralteyskaya/menu/index.php',
    '/admiralteyskaya/menu/text' => '/admiralteyskaya/menu/text/index.php',
    '/admiralteyskaya/menu/visual' => '/admiralteyskaya/menu/visual/index.php',
    '/admiralteyskaya/menu/english' => '/admiralteyskaya/menu/english/index.php',
    '/udelnaya/menu' => '/admiralteyskaya/udelnaya/menu/index.php',
    '/udelnaya/menu/text' => '/admiralteyskaya/udelnaya/menu/text/index.php',
    '/udelnaya/menu/visual' => '/admiralteyskaya/udelnaya/menu/visual/index.php',
    '/udelnaya/menu/english' => '/admiralteyskaya/udelnaya/menu/english/index.php',
    '/admiralteyskaya/loyalty' => '/admiralteyskaya/loyalty/index.php',
    '/udelnaya/loyalty' => '/udelnaya/loyalty/index.php',
);

$relative = isset($explicit[$uriPath]) ? $explicit[$uriPath] : $uriPath;
$candidate = $docroot . $relative;

if (is_dir($candidate)) {
    $index = rtrim($candidate, '/') . '/index.php';
    if (is_file($index)) {
        $candidate = $index;
        $relative = rtrim($relative, '/') . '/index.php';
    }
}

if (!is_file($candidate) && is_file($candidate . '.php')) {
    $candidate .= '.php';
    $relative .= '.php';
}

if (!is_file($candidate) && strpos($relative, '/udelnaya/') === 0) {
    $innerRel = '/admiralteyskaya' . $relative;
    $inner = $docroot . $innerRel;
    if (is_file($inner)) {
        $candidate = $inner;
        $relative = $innerRel;
    } elseif (is_file($inner . '.php')) {
        $candidate = $inner . '.php';
        $relative = $innerRel . '.php';
    }
}

if (!is_file($candidate)) {
    return false;
}

$ext = strtolower(pathinfo($candidate, PATHINFO_EXTENSION));
if ($ext !== 'php') {
        if ($relative !== $originalPath) {
        $mime = 'application/octet-stream';
        if (in_array($ext, array('jpg', 'jpeg'), true)) {
            $mime = 'image/jpeg';
        } elseif ($ext === 'png') {
            $mime = 'image/png';
        } elseif ($ext === 'webp') {
            $mime = 'image/webp';
        } elseif ($ext === 'gif') {
            $mime = 'image/gif';
        } elseif ($ext === 'svg') {
            $mime = 'image/svg+xml';
        } elseif ($ext === 'css') {
            $mime = 'text/css; charset=utf-8';
        } elseif ($ext === 'js') {
            $mime = 'application/javascript; charset=utf-8';
        } elseif ($ext === 'woff2') {
            $mime = 'font/woff2';
        }
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($candidate));
        readfile($candidate);
        return true;
    }
    return false;
}

$_SERVER['DOCUMENT_ROOT'] = $docroot;
$_SERVER['SCRIPT_FILENAME'] = str_replace('\\', '/', $candidate);
$_SERVER['SCRIPT_NAME'] = $relative;
$_SERVER['PHP_SELF'] = $relative;
chdir(dirname($candidate));

$origin = 'http://' . ($_SERVER['HTTP_HOST'] ?? '127.0.0.1:8090');
ob_start(function ($buffer) use ($origin, $relative) {
    if (strpos($relative, '/qr/') === 0 || substr($relative, -11) === '/qr-api.php') {
        return $buffer;
    }
    $type = '';
    foreach (headers_list() as $header) {
        if (stripos($header, 'Content-Type:') === 0) {
            $type = strtolower($header);
            break;
        }
    }
    if ($type !== '' && strpos($type, 'text/html') === false && strpos($type, 'application/json') === false && strpos($type, 'text/css') === false) {
        return $buffer;
    }
    return str_replace(
        array(
            'https://www.garden-lounge.pro',
            'http://www.garden-lounge.pro',
            'https://garden-lounge.pro',
            'http://garden-lounge.pro',
        ),
        $origin,
        $buffer
    );
});

require $candidate;
return true;
