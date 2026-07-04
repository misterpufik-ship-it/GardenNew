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

function gl_favicon_href($href = '/favicon.png')
{
    if ($href === '') {
        $href = '/favicon.png';
    }

    if (strpos($href, 'http://') === 0 || strpos($href, 'https://') === 0) {
        return $href;
    }

    if ($href[0] !== '/') {
        $href = '/' . $href;
    }

    return gl_public_file_url($href);
}

function gl_favicon_render_tags($href = '/favicon.png')
{
    $png = htmlspecialchars(gl_favicon_href($href), ENT_QUOTES, 'UTF-8');
    $ico = htmlspecialchars(gl_favicon_href('/favicon.ico'), ENT_QUOTES, 'UTF-8');

    echo '<link rel="icon" href="' . $ico . '" sizes="any">' . "\n";
    echo '<link rel="icon" type="image/png" sizes="32x32" href="' . $png . '">' . "\n";
    echo '<link rel="shortcut icon" type="image/png" href="' . $png . '">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . $png . '">' . "\n";
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

function gl_preloader_video_url($variant = 'desktop')
{
    $settings = garden_preloader_get_settings();
    $video = $settings['video'];

    if ($variant === 'mobile') {
        $video = $settings['video_mobile'];
    } elseif ($variant === 'desktop') {
        $video = $settings['video_desktop'];
    }

    if (preg_match('#^https?://#i', $video)) {
        return $video;
    }

    return gl_public_file_url($video);
}

function gl_preloader_pick_video_url()
{
    $is_mobile = isset($_SERVER['HTTP_USER_AGENT'])
        && preg_match('/Mobile|Android|iPhone|iPad|iPod|webOS|BlackBerry|IEMobile|Opera Mini/i', (string)$_SERVER['HTTP_USER_AGENT']);

    return gl_preloader_video_url($is_mobile ? 'mobile' : 'desktop');
}

function gl_preloader_render_styles()
{
    $settings = garden_preloader_get_settings();
    $desktop_fit = $settings['desktop_object_fit'] === 'contain' ? 'contain' : 'cover';
    $mobile_fit = $settings['mobile_object_fit'] === 'cover' ? 'cover' : 'contain';

    echo '<style>
#preloader{position:fixed;top:0;left:0;width:100%;height:100%;background:#000;display:flex;justify-content:center;align-items:center;z-index:10001;opacity:1;visibility:visible;transition:opacity .8s ease,visibility .8s ease;pointer-events:all;overflow:hidden}
#preloader-video{width:100%;height:100%;object-fit:' . $desktop_fit . ';object-position:center center}
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

    $video_desktop = htmlspecialchars(gl_preloader_video_url('desktop'), ENT_QUOTES, 'UTF-8');
    $video_mobile = htmlspecialchars(gl_preloader_video_url('mobile'), ENT_QUOTES, 'UTF-8');
    echo '<link rel="preload" as="video" href="' . $video_desktop . '" type="video/mp4" media="(min-width:768px)">' . "\n";
    if ($video_mobile !== $video_desktop) {
        echo '<link rel="preload" as="video" href="' . $video_mobile . '" type="video/mp4" media="(max-width:767px)">' . "\n";
    }
}

function gl_preloader_render()
{
    if (!garden_preloader_should_show()) {
        return;
    }

    $settings = garden_preloader_get_settings();
    $video_desktop = htmlspecialchars(gl_preloader_video_url('desktop'), ENT_QUOTES, 'UTF-8');
    $video_mobile = htmlspecialchars(gl_preloader_video_url('mobile'), ENT_QUOTES, 'UTF-8');
    $min_time = (int)$settings['min_time'];
    $max_time = (int)$settings['max_time'];
    $playback_rate = (float)$settings['playback_rate'];

    echo '<div id="preloader" aria-hidden="true"><video id="preloader-video" autoplay muted playsinline preload="metadata" data-desktop-src="' . $video_desktop . '" data-mobile-src="' . $video_mobile . '"></video></div>' . "\n";
    echo '<script>
(function(){
    document.body.classList.add("loading");
    var preloader=document.getElementById("preloader");
    var video=document.getElementById("preloader-video");
    if(!preloader)return;
    if(video){
        var desktop=video.getAttribute("data-desktop-src")||"";
        var mobile=video.getAttribute("data-mobile-src")||desktop;
        video.src=(window.matchMedia&&window.matchMedia("(max-width:767px)").matches)?mobile:desktop;
    }
    var hidden=false,domReady=false,videoDone=false,forceHide=false;
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
        if(hidden)return;
        if(forceHide||(domReady&&videoDone))hide();
    }
    function markDomReady(){domReady=true;tryHide();}
    if(document.readyState==="loading"){
        document.addEventListener("DOMContentLoaded",markDomReady,{once:true});
    }else{
        markDomReady();
    }
    window.addEventListener("load",markDomReady,{once:true});
    setTimeout(function(){forceHide=true;tryHide();},maxTime);
    if(video){
        video.defaultPlaybackRate=playbackRate;
        video.playbackRate=playbackRate;
        video.addEventListener("ended",function(){videoDone=true;tryHide();});
        video.addEventListener("error",function(){videoDone=true;tryHide();});
        var p=video.play();
        if(p&&p.catch)p.catch(function(){videoDone=true;tryHide();});
    }else{
        videoDone=true;
        tryHide();
    }
})();
</script>' . "\n";
}
