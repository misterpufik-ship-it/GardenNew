<?php

function gl_hero_normalize_public_path($url)
{
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }

    if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
        $path = parse_url($url, PHP_URL_PATH);
        return $path ? $path : $url;
    }

    if ($url[0] !== '/') {
        return '/admiralteyskaya/' . ltrim($url, '/');
    }

    return $url;
}

function gl_hero_lcp_url($sourceUrl, $role = 'desk')
{
    $path = gl_hero_normalize_public_path($sourceUrl);
    if ($path === '') {
        return $sourceUrl;
    }

    $docRoot = rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/');
    $full = $docRoot . $path;
    if (!is_file($full)) {
        return $sourceUrl;
    }

    $dir = dirname($path);
    $base = pathinfo($path, PATHINFO_FILENAME);

    if ($role === 'mob') {
        $mob = $dir . '/' . $base . '-mob-768.webp';
        if (is_file($docRoot . $mob)) {
            return $mob;
        }

        $aliases = array(
            'garden-main' => '/admiralteyskaya/couch/uploads/image/garden-main-mobile.webp',
        );
        if (isset($aliases[$base]) && is_file($docRoot . $aliases[$base])) {
            return $aliases[$base];
        }
    }

    // Desktop keeps the original CMS file for full visual quality.
    return $path;
}

function gl_hero_resolve_lcp_urls($deskUrl, $mobUrl = '')
{
    if ($mobUrl === '') {
        $mobUrl = $deskUrl;
    }

    $deskPath = gl_hero_normalize_public_path($deskUrl);
    $mobPath = gl_hero_normalize_public_path($mobUrl);
    $deskLcp = gl_hero_lcp_url($deskUrl, 'desk');

    // Explicit CMS mobile asset — serve exactly as uploaded (no -mob-768 rewrite).
    if ($mobPath !== '' && $deskPath !== '' && $mobPath !== $deskPath) {
        $mobLcp = $mobPath;
    } else {
        $mobLcp = gl_hero_lcp_url($mobUrl, 'mob');
    }

    return array('desk' => $deskLcp, 'mob' => $mobLcp);
}

function gl_hero_render_preload_tags($deskUrl, $mobUrl = '')
{
    $urls = gl_hero_resolve_lcp_urls($deskUrl, $mobUrl);
    $desk = htmlspecialchars($urls['desk'], ENT_QUOTES, 'UTF-8');
    $mob = htmlspecialchars($urls['mob'], ENT_QUOTES, 'UTF-8');

    echo '<link rel="preload" as="image" href="' . $desk . '" media="(min-width: 768px)" fetchpriority="high">' . "\n";
    echo '<link rel="preload" as="image" href="' . $mob . '" media="(max-width: 767px)" fetchpriority="high">' . "\n";
}

function gl_hero_resize_webp_file($sourceFull, $destFull, $maxWidth, $quality = 78)
{
    if (!function_exists('imagecreatefromwebp') || !function_exists('imagewebp')) {
        return false;
    }
    if (!is_file($sourceFull)) {
        return false;
    }

    $src = @imagecreatefromwebp($sourceFull);
    if (!$src) {
        return false;
    }

    $width = imagesx($src);
    $height = imagesy($src);
    if ($width <= 0 || $height <= 0) {
        imagedestroy($src);
        return false;
    }

    if ($width <= $maxWidth) {
        imagedestroy($src);
        return copy($sourceFull, $destFull);
    }

    $newWidth = $maxWidth;
    $newHeight = (int) round($height * ($maxWidth / $width));
    $dst = imagecreatetruecolor($newWidth, $newHeight);
    if (!$dst) {
        imagedestroy($src);
        return false;
    }

    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    $ok = imagewebp($dst, $destFull, max(1, min(100, (int) $quality)));
    imagedestroy($src);
    imagedestroy($dst);

    return $ok;
}

function gl_hero_optimize_variants($imageDir)
{
    $results = array();
    $jobs = array(
        array('file' => 'garden-main.webp', 'desk' => 'garden-main-desk-1280.webp', 'deskW' => 1280, 'mob' => 'garden-main-mob-768.webp', 'mobW' => 768),
        array('file' => 'kalyannaya-garden-lounge-udelnaya-interer-spb.webp', 'desk' => 'kalyannaya-garden-lounge-udelnaya-interer-spb-desk-1280.webp', 'deskW' => 1280, 'mob' => 'kalyannaya-garden-lounge-udelnaya-interer-spb-mob-768.webp', 'mobW' => 768),
        array('file' => 'main-mobile_1.webp', 'desk' => 'main-mobile_1-desk-1280.webp', 'deskW' => 1280, 'mob' => 'main-mobile_1-mob-768.webp', 'mobW' => 768),
    );

    foreach ($jobs as $job) {
        $source = rtrim($imageDir, '/\\') . '/' . $job['file'];
        if (!is_file($source)) {
            $results[] = 'SKIP missing source: ' . $job['file'];
            continue;
        }

        $deskDest = rtrim($imageDir, '/\\') . '/' . $job['desk'];
        if (gl_hero_resize_webp_file($source, $deskDest, $job['deskW'])) {
            $results[] = 'OK desk ' . $job['desk'] . ' (' . filesize($deskDest) . ' bytes)';
        } else {
            $results[] = 'FAIL desk ' . $job['desk'];
        }

        $mobDest = rtrim($imageDir, '/\\') . '/' . $job['mob'];
        if (gl_hero_resize_webp_file($source, $mobDest, $job['mobW'])) {
            $results[] = 'OK mob ' . $job['mob'] . ' (' . filesize($mobDest) . ' bytes)';
        } else {
            $results[] = 'FAIL mob ' . $job['mob'];
        }
    }

    return $results;
}
