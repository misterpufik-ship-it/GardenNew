<?php
/**
 * Shared head assets: favicon + age-gate with filemtime cache busting.
 */
require_once __DIR__ . '/preloader-lib.php';
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

function gl_age_gate_lib_loaded()
{
    static $loaded = false;
    if ( !$loaded ) {
        require_once __DIR__ . '/age-gate-lib.php';
        $loaded = true;
    }
}

function gl_age_gate_render_assets()
{
    static $rendered = false;
    if ( $rendered ) {
        return;
    }

    gl_age_gate_lib_loaded();

    if ( !garden_age_gate_should_show() ) {
        return;
    }

    $rendered = true;

    $settings = garden_age_gate_get_settings();
    $config = garden_age_gate_js_config();
    $css = htmlspecialchars(gl_age_gate_asset_url('age-gate.css'), ENT_QUOTES, 'UTF-8');
    $js = htmlspecialchars(gl_age_gate_asset_url('age-gate.js'), ENT_QUOTES, 'UTF-8');
    $opacity = max(0, min(100, (int)$settings['overlay_opacity']));
    $overlay_inner = round($opacity * 0.92 / 100, 3);
    $overlay_outer = round($opacity / 100, 3);

    echo '<style>:root{'
        . '--ag-gold-main:' . htmlspecialchars($settings['color_gold'], ENT_QUOTES, 'UTF-8') . ';'
        . '--ag-gold-dark:' . htmlspecialchars($settings['color_gold_dark'], ENT_QUOTES, 'UTF-8') . ';'
        . '--ag-gold-light:' . htmlspecialchars($settings['color_gold_light'], ENT_QUOTES, 'UTF-8') . ';'
        . '--ag-overlay-inner:rgba(18,16,14,' . $overlay_inner . ');'
        . '--ag-overlay-outer:rgba(0,0,0,' . $overlay_outer . ');'
        . '}</style>' . "\n";
    echo '<link rel="stylesheet" href="' . $css . '">' . "\n";
    echo '<script>window.__glAgeGateConfig=' . json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';</script>' . "\n";
    echo '<script src="' . $js . '" defer></script>' . "\n";
}

function gl_render_head_assets($faviconHref = '/favicon.png')
{
    gl_favicon_render_tags($faviconHref);
    gl_age_gate_render_assets();
}

function gl_preloader_video_url()
{
    $settings = garden_preloader_get_settings();
    $video = $settings['video'];

    if (preg_match('#^https?://#i', $video)) {
        return $video;
    }

    return gl_public_file_url($video);
}

function gl_preloader_render_styles()
{
    $settings = garden_preloader_get_settings();
    $mobile_fit = $settings['mobile_object_fit'] === 'cover' ? 'cover' : 'contain';

    echo '<style>
#preloader{position:fixed;top:0;left:0;width:100%;height:100%;background:#000;display:flex;justify-content:center;align-items:center;z-index:10001;opacity:1;visibility:visible;transition:opacity .8s ease,visibility .8s ease;pointer-events:all;overflow:hidden}
#preloader-video{width:100%;height:100%;object-fit:cover;object-position:center center}
@media (max-width:767px){#preloader-video{width:auto;max-width:100%;height:100%;object-fit:' . $mobile_fit . ';object-position:center center}}
.preloader-hidden{opacity:0!important;visibility:hidden!important;pointer-events:none!important}
body.loading{overflow:hidden!important;height:100vh}
</style>' . "\n";
}

function gl_preloader_render_head($include_styles = false)
{
    if (!garden_preloader_should_show()) {
        return;
    }

    gl_preloader_render_styles();

    $video = htmlspecialchars(gl_preloader_video_url(), ENT_QUOTES, 'UTF-8');
    echo '<link rel="preload" as="video" href="' . $video . '" type="video/mp4">' . "\n";
}

function gl_preloader_render()
{
    if (!garden_preloader_should_show()) {
        return;
    }

    $settings = garden_preloader_get_settings();
    $video = htmlspecialchars(gl_preloader_video_url(), ENT_QUOTES, 'UTF-8');
    $min_time = (int)$settings['min_time'];
    $max_time = (int)$settings['max_time'];
    $playback_rate = (float)$settings['playback_rate'];

    echo '<div id="preloader" aria-hidden="true"><video id="preloader-video" src="' . $video . '" autoplay muted playsinline preload="auto"></video></div>' . "\n";
    echo '<script>
(function(){
    document.body.classList.add("loading");
    var preloader=document.getElementById("preloader");
    var video=document.getElementById("preloader-video");
    if(!preloader)return;
    var hidden=false,pageLoaded=false,videoDone=false;
    var minTime=' . $min_time . ',maxTime=' . $max_time . ',playbackRate=' . $playback_rate . ',start=Date.now();
    function doHide(){
        if(hidden)return;
        hidden=true;
        preloader.classList.add("preloader-hidden");
        document.body.classList.remove("loading");
        setTimeout(function(){if(preloader.parentNode)preloader.parentNode.removeChild(preloader);},850);
    }
    function hide(){
        var wait=Math.max(0,minTime-(Date.now()-start));
        setTimeout(doHide,wait);
    }
    function tryHide(){
        if(pageLoaded&&(videoDone||(Date.now()-start)>=maxTime))hide();
    }
    window.addEventListener("load",function(){pageLoaded=true;tryHide();});
    if(video){
        video.defaultPlaybackRate=playbackRate;
        video.playbackRate=playbackRate;
        video.addEventListener("ended",function(){videoDone=true;tryHide();});
        video.addEventListener("error",function(){videoDone=true;tryHide();});
        var p=video.play();
        if(p&&p.catch)p.catch(function(){videoDone=true;tryHide();});
    }else{videoDone=true;}
    setTimeout(function(){videoDone=true;tryHide();},maxTime);
})();
</script>' . "\n";
}
