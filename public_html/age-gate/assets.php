<?php
/**
 * Shared head assets: favicon + age-gate with filemtime cache busting.
 */
function gl_public_file_version($webPath)
{
    static $versions = array();

    if (!isset($versions[$webPath])) {
        $path = $_SERVER['DOCUMENT_ROOT'] . $webPath;
        $versions[$webPath] = @filemtime($path) ?: 1;
    }

    return $versions[$webPath];
}

function gl_public_file_url($webPath)
{
    return $webPath . '?v=' . gl_public_file_version($webPath);
}

function gl_age_gate_asset_version($filename)
{
    static $versions = array();

    if (!isset($versions[$filename])) {
        $path = __DIR__ . '/' . ltrim($filename, '/');
        $versions[$filename] = @filemtime($path) ?: 1;
    }

    return $versions[$filename];
}

function gl_age_gate_asset_url($filename)
{
    return '/age-gate/' . ltrim($filename, '/') . '?v=' . gl_age_gate_asset_version($filename);
}

function gl_favicon_render_tags($href = '/favicon.png')
{
    if ($href === '') {
        $href = '/favicon.png';
    }

    if (strpos($href, 'http://') === 0 || strpos($href, 'https://') === 0) {
        $url = $href;
    } else {
        if ($href[0] !== '/') {
            $href = '/' . $href;
        }
        $url = gl_public_file_url($href);
    }

    $url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    echo '<link rel="icon" type="image/png" sizes="32x32" href="' . $url . '">' . "\n";
    echo '<link rel="shortcut icon" type="image/png" href="' . $url . '">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . $url . '">' . "\n";
}

function gl_age_gate_render_assets()
{
    $css = htmlspecialchars(gl_age_gate_asset_url('age-gate.css'), ENT_QUOTES, 'UTF-8');
    $js = htmlspecialchars(gl_age_gate_asset_url('age-gate.js'), ENT_QUOTES, 'UTF-8');

    echo '<link rel="stylesheet" href="' . $css . '">' . "\n";
    echo '<script src="' . $js . '" defer></script>' . "\n";
}

function gl_render_head_assets($faviconHref = '/favicon.png')
{
    gl_favicon_render_tags($faviconHref);
    gl_age_gate_render_assets();
}
