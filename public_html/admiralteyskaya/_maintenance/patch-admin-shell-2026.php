<?php
$root = dirname(__DIR__);
$theme = $root . '/couch/theme/garden';
$stylesPath = $theme . '/styles.css';
$kfunctionsPath = $root . '/couch/addons/kfunctions.php';
$logoPath = $theme . '/logo.html';
$subtitlePath = $theme . '/subtitle.html';
$mainPath = $theme . '/main.html';

$logo = <<<'HTML'
<cms:capture into='my_logo'>
    <div class="garden-admin-brand garden-admin-brand--logo-only">
        <img
            <cms:if k_logo_id >id="<cms:show k_logo_id />"</cms:if>
            class="garden-admin-brand__logo<cms:if k_logo_class> <cms:show k_logo_class /></cms:if>"
            src="/img/logo3.png"
            alt="Garden Lounge"
            width="384"
            height="162"
            decoding="async"
        />
    </div>
</cms:capture>

<cms:if k_logo_href >
    <a href="<cms:show k_logo_href />" class="garden-admin-brand__link">
        <cms:show my_logo />
    </a>
<cms:else />
    <cms:show my_logo />
</cms:if>
HTML;
file_put_contents($logoPath, $logo);

file_put_contents($subtitlePath, "<!-- Garden admin: edit tab hidden via CSS -->\n");

$systemMain = file_get_contents(dirname($theme) . '/_system/main.html');
$systemMain = preg_replace(
    '#\s*<cms:if k_user_access_level ge \'10\'>\s*<div id="nav-links">.*?</div>\s*</cms:if>#is',
    '',
    $systemMain,
    1
);
file_put_contents($mainPath, $systemMain);

$layoutCss = <<<'CSS'

/* === Garden admin shell v2 === */
#sidebar,#menu-wrap,#logo-wrap{background-color:var(--gl-black)!important;background-image:none!important}
#logo-wrap{border-bottom:1px solid var(--gl-border)}
#menu-wrap .garden-admin-brand{padding:10px 10px 6px}
#menu-wrap .garden-admin-brand__logo,#menu-wrap #logo{max-width:210px!important;max-height:82px!important;width:100%!important}
#menu-content{position:relative;height:100%}
#scroll-sidebar{position:absolute!important;top:76px!important;right:0;left:0;bottom:8px!important;overflow-y:auto}
@media (max-height:540px){#scroll-sidebar{top:70px!important}}
#nav-links,#sidebar-bot{display:none!important}
#sidebar-top,#sidebar-greeting,#sidebar-btns{position:static!important;width:auto!important;height:auto!important;padding:0!important;margin:0!important;border:0!important;background:transparent!important;box-shadow:none!important}
#sidebar-btns>.btn{height:34px;line-height:32px;padding:0 12px;font-size:11px}
#sidebar-btns>#log-out,#sidebar-btns>#view-site{width:auto!important;min-width:88px}
#sidebar-greeting>p,#sidebar-top>p{margin:0;font-size:12px;white-space:nowrap}
#header{background:var(--gl-black)!important;border-bottom:1px solid var(--gl-border)!important}
#header-inner{display:flex!important;align-items:center!important;justify-content:flex-end!important;gap:10px!important;padding:10px 16px!important;min-height:54px!important}
#header-inner>.btn-group{position:static!important;top:auto!important;right:auto!important;margin:0!important;flex:0 0 auto}
#header-title,#header ul#tabs,#tabs{display:none!important}
#header .subtitle{display:none!important}
#gl-header-user{display:flex;align-items:center;gap:10px;margin-right:auto;flex:1 1 auto;min-width:0}
#scroll-content{background:#f3f3f3}
#content{background:#fff;min-height:calc(100vh - 58px);padding:18px 24px 28px;border-top:0}
body #tabs-page #content{background:#fff;padding-bottom:28px}
.panel,.group-wrapper .panel{border-color:var(--gl-border)}
.gl-admin-dump-link{margin:0 0 12px;font-size:12px}
.gl-admin-dump-link a{color:var(--gl-gold)}

CSS;

$styles = file_get_contents($stylesPath);
$styles = preg_replace('/\n\/\* === Garden admin shell v2 === \*\/.*$/s', '', $styles);
$styles = rtrim($styles) . $layoutCss;
file_put_contents($stylesPath, $styles);

$labels = file_get_contents(__DIR__ . '/repair-kfunctions-utf8.php');
preg_match('/function garden_admin_label_defaults\(\)\{.*?\n\}/s', $labels, $m1);
preg_match('/function garden_alter_admin_menuitems\( &\$items \)\{.*?\n\}/s', $labels, $m2);
if (empty($m1[0]) || empty($m2[0])) {
    fwrite(STDERR, "Cannot extract UTF-8 menu blocks\n");
    exit(1);
}

$newBranding = <<<'PHP'
function garden_admin_branding_output( &$html ){
    if ( !defined( 'K_ADMIN' ) ) {
        return;
    }

    $html = preg_replace( '#<title>[^<]*</title>#', '<title>Garden Lounge</title>', $html, 1 );
    $html = preg_replace( '#<link[^>]+rel=["\'](?:shortcut )?icon["\'][^>]*/>#i', '', $html );
    $verPng = @filemtime( $_SERVER['DOCUMENT_ROOT'] . '/favicon.png' ) ?: time();
    $verIco = @filemtime( $_SERVER['DOCUMENT_ROOT'] . '/favicon.ico' ) ?: $verPng;
    $favicon = '<link rel="icon" href="/favicon.ico?v=' . $verIco . '" sizes="any">' . "\n    "
        . '<link rel="icon" type="image/png" sizes="32x32" href="/favicon.png?v=' . $verPng . '">' . "\n    "
        . '<link rel="shortcut icon" type="image/png" href="/favicon.png?v=' . $verPng . '">' . "\n    "
        . '<link rel="apple-touch-icon" href="/favicon.png?v=' . $verPng . '">';
    $html = preg_replace( '#</head>#', $favicon . "\n</head>", $html, 1 );

    $fonts = '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">';
    if ( strpos( $html, 'fonts.googleapis.com' ) === false ) {
        $html = preg_replace( '#</head>#', $fonts . "\n</head>", $html, 1 );
    }
    if ( defined( 'K_THEME_URL' ) && defined( 'K_THEME_DIR' ) && K_THEME_URL && is_file( K_THEME_DIR . 'styles.css' ) ) {
        $ver = filemtime( K_THEME_DIR . 'styles.css' );
        $html = str_replace( K_THEME_URL . 'styles.css', K_THEME_URL . 'styles.css?v=' . $ver, $html );
    }
}
PHP;

$newSidebarCss = <<<'PHP'
function garden_admin_sidebar_css(){
    global $FUNCS;

    $css = <<<'CSS'
#sidebar,#menu-wrap,#logo-wrap{background-color:#0a0a0a!important;background-image:none!important}
#menu-wrap .garden-admin-brand{padding:10px 10px 6px}
#menu-wrap .garden-admin-brand__logo,#menu-wrap #logo{max-width:210px!important;max-height:82px!important;width:100%!important}
.garden-admin-brand__subtitle{display:none!important}
#menu-content{position:relative;height:100%}
#scroll-sidebar{position:absolute!important;top:76px!important;right:0;left:0;bottom:8px!important;overflow-y:auto}
@media (max-height:540px){#scroll-sidebar{top:70px!important}}
#nav-links,#sidebar-bot{display:none!important}
#sidebar-top,#sidebar-greeting,#sidebar-btns{position:static!important;width:auto!important;height:auto!important;padding:0!important;margin:0!important;border:0!important;background:transparent!important;box-shadow:none!important}
#sidebar-btns>.btn{height:34px;line-height:32px;padding:0 12px;font-size:11px}
#sidebar-btns>#log-out,#sidebar-btns>#view-site{width:auto!important;min-width:88px}
#sidebar-greeting>p,#sidebar-top>p{margin:0;font-size:12px;white-space:nowrap}
#header{background:#0a0a0a!important;border-bottom:1px solid rgba(197,160,89,.28)!important}
#header-inner{display:flex!important;align-items:center!important;justify-content:flex-end!important;gap:10px!important;padding:10px 16px!important;min-height:54px!important}
#header-inner>.btn-group{position:static!important;top:auto!important;right:auto!important;margin:0!important}
#header-title,#header ul#tabs,#tabs{display:none!important}
#header .subtitle{display:none!important}
#gl-header-user{display:flex;align-items:center;gap:10px;margin-right:auto;flex:1 1 auto;min-width:0}
#scroll-content{background:#f3f3f3}
#content{background:#fff;min-height:calc(100vh - 58px);padding:18px 24px 28px;border-top:0}
body #tabs-page #content{background:#fff;padding-bottom:28px}
.gl-admin-dump-link{margin:0 0 12px;font-size:12px}
.gl-admin-dump-link a{color:#C5A059}
div.kcfinder-iframe .mfp-content{max-width:968px;width:96vw;height:auto!important;max-height:90vh}
.kcfinder-iframe .mfp-iframe-scaler{padding-top:0!important;height:min(72vh,640px)!important;position:relative;overflow:hidden}
.kcfinder-iframe .mfp-iframe-scaler iframe{position:absolute;top:0;left:0;width:100%;height:100%;background:#fff}
CSS;

    $FUNCS->add_css( $css );
}
PHP;

$newSidebarJs = <<<'PHP'
function garden_admin_sidebar_js(){
    global $FUNCS;

    $js = <<<'JS'
(function($){
    function moveUserBar(){
        var $headerInner = $('#header-inner');
        var $toolbar = $headerInner.children('.btn-group').first();
        var $greeting = $('#sidebar-top, #sidebar-greeting').first();
        var $btns = $('#sidebar-btns');
        if (!$headerInner.length || !$toolbar.length) return;

        var $bar = $('#gl-header-user');
        if (!$bar.length) {
            $bar = $('<div id="gl-header-user" class="gl-header-user"></div>');
            $headerInner.prepend($bar);
        }
        if ($greeting.length) $greeting.appendTo($bar);
        if ($btns.length) $btns.appendTo($bar);
    }

    function addDumpLink(){
        var $adv = $('.group-wrapper').filter(function(){
            return $(this).find('[name="k_publish_date"], [name="k_access_level"], [name="k_show_in_menu"]').length > 0;
        }).find('> .panel-collapse > .panel-body, > .panel-body').first();
        if (!$adv.length || $('.gl-admin-dump-link').length) return;
        var m = window.location.pathname.match(/^(.*\/couch\/)/);
        var href = m ? (m[1] + 'gen_dump.php') : 'gen_dump.php';
        $adv.prepend('<p class="gl-admin-dump-link"><a href="' + href + '">Download Dump</a></p>');
    }

    $(function(){
        moveUserBar();
        addDumpLink();

        if ( typeof COUCH === 'undefined' || !COUCH.state ) return;
        if ( $.hasCookie('collapsed_groups') ) return;
        var ids = [];
        $('#sidebar .nav-heading-toggle').each(function(){
            ids.push(String($(this).data('id')));
        });
        COUCH.state.collapsedGroups = ids;
    });
})(jQuery);
JS;

    $FUNCS->add_js( $js );
}
PHP;

$kfunctions = file_get_contents($kfunctionsPath);
$kfunctions = preg_replace('/function garden_admin_label_defaults\(\)\{.*?\n\}/s', trim($m1[0]), $kfunctions, 1, $c1);
$kfunctions = preg_replace('/function garden_alter_admin_menuitems\( &\$items \)\{.*?\n\}/s', trim($m2[0]), $kfunctions, 1, $c2);
$kfunctions = preg_replace('/function garden_admin_branding_output\( &\$html \)\{.*?\n\}/s', trim($newBranding), $kfunctions, 1, $c3);
$kfunctions = preg_replace('/function garden_admin_sidebar_css\(\)\{.*?\n\}/s', trim($newSidebarCss), $kfunctions, 1, $c4);
$kfunctions = preg_replace('/function garden_admin_sidebar_js\(\)\{.*?\n\}/s', trim($newSidebarJs), $kfunctions, 1, $c5);
if (!$c1 || !$c2 || !$c3 || !$c4 || !$c5) {
    fwrite(STDERR, "kfunctions replace failed: $c1 $c2 $c3 $c4 $c5\n");
    exit(1);
}
file_put_contents($kfunctionsPath, $kfunctions);

passthru('php -l ' . escapeshellarg($kfunctionsPath), $code);
echo "Patched admin shell: main.html, logo, styles, kfunctions ($c1/$c2/$c3/$c4/$c5)\n";
exit($code);
