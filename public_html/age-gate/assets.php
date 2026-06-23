<?php
/**
 * Age-gate asset URLs with filemtime cache busting.
 */
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

function gl_age_gate_render_assets()
{
    $css = htmlspecialchars(gl_age_gate_asset_url('age-gate.css'), ENT_QUOTES, 'UTF-8');
    $js = htmlspecialchars(gl_age_gate_asset_url('age-gate.js'), ENT_QUOTES, 'UTF-8');

    echo '<link rel="stylesheet" href="' . $css . '">' . "\n";
    echo '<script src="' . $js . '" defer></script>' . "\n";
}
