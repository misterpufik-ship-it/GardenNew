<?php
/**
 * Favicon fix (admin-only branding), full admin logo, tighter sidebar, KCFinder popup CSS.
 */
$root = dirname(__DIR__);
$stylesPath = $root . '/couch/theme/garden/styles.css';
$kfunctionsPath = $root . '/couch/addons/kfunctions.php';
$logoPath = $root . '/couch/theme/garden/logo.html';

$logo = <<<'HTML'
<cms:capture into='my_logo'>
    <div class="garden-admin-brand garden-admin-brand--logo-only">
        <img
            <cms:if k_logo_id >id="<cms:show k_logo_id />"</cms:if>
            class="garden-admin-brand__logo<cms:if k_logo_class> <cms:show k_logo_class /></cms:if>"
            src="/img/logo3.webp"
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

$sidebarFix = <<<'CSS'
/* Sidebar: logo block + menu closer together */
#menu-wrap .garden-admin-brand{padding:8px 8px 4px}
#menu-wrap .garden-admin-brand__logo,#menu-wrap #logo{max-width:198px!important;max-height:74px!important;width:100%!important}
.garden-admin-brand__subtitle{display:none!important}
#menu-content{position:relative;height:100%}
#scroll-sidebar{position:absolute!important;top:88px!important;right:0;left:0;bottom:132px!important;overflow-y:auto}
@media (max-height:540px){#scroll-sidebar{top:80px!important;bottom:124px!important}}
#sidebar-greeting,#sidebar-top{position:absolute;right:0;bottom:84px;left:0;z-index:2;border-top:1px solid #000;border-bottom:none;padding:10px 14px 8px;background-color:var(--gl-panel);box-shadow:0 -1px 0 rgba(197,160,89,.08)}
#sidebar-greeting>p,#sidebar-top>p{color:var(--gl-muted);margin:0;font-size:12px;line-height:1.45}
#sidebar-greeting>p>a,#sidebar-top>p>a{color:var(--gl-text)}
div.kcfinder-iframe .mfp-content{max-width:968px;width:96vw;height:auto!important;max-height:90vh}
.kcfinder-iframe .mfp-iframe-scaler{padding-top:0!important;height:min(72vh,640px)!important;position:relative;overflow:hidden}
.kcfinder-iframe .mfp-iframe-scaler iframe{position:absolute;top:0;left:0;width:100%;height:100%;background:#fff}
CSS;

$styles = file_get_contents($stylesPath);
$styles = preg_replace(
    '/\/\* Sidebar:.*?\*\/\s*#menu-wrap \.garden-admin-brand\{.*?(?=#nav>\.nav-heading:first-child)/s',
    $sidebarFix . "\n",
    $styles,
    1,
    $count
);
if (!$count) {
  $styles = preg_replace(
      '/#scroll-sidebar\{position:absolute!important;top:\d+px!important;.*?(?=#nav>\.nav-heading:first-child)/s',
      $sidebarFix . "\n",
      $styles,
      1,
      $count2
  );
}
if (!$count && empty($count2)) {
    $styles = str_replace(
        "#nav>.nav-heading:first-child{margin-top:6px}",
        $sidebarFix . "\n#nav>.nav-heading:first-child{margin-top:2px}",
        $styles
    );
} else {
    $styles = str_replace('#nav>.nav-heading:first-child{margin-top:6px}', '#nav>.nav-heading:first-child{margin-top:2px}', $styles);
}
file_put_contents($stylesPath, $styles);

$kfunctions = file_get_contents($kfunctionsPath);

$newBranding = <<<'PHP'
function garden_admin_branding_output( &$html ){
    if ( !defined( 'K_ADMIN' ) ) {
        return;
    }

    $html = preg_replace( '#<title>[^<]*</title>#', '<title>Garden Lounge</title>', $html, 1 );
    $favicon = '<link rel="icon" type="image/png" href="/favicon.png">' . "\n    "
        . '<link rel="shortcut icon" type="image/png" href="/favicon.png">';
    if ( preg_match( '#<link[^>]+rel=["\'](?:shortcut )?icon["\'][^>]*/>#i', $html ) ) {
        $html = preg_replace( '#<link[^>]+rel=["\'](?:shortcut )?icon["\'][^>]*/>#i', $favicon, $html, 1 );
    } else {
        $html = preg_replace( '#</head>#', $favicon . "\n</head>", $html, 1 );
    }
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
#menu-wrap .garden-admin-brand{padding:8px 8px 4px}
#menu-wrap .garden-admin-brand__logo,#menu-wrap #logo{max-width:198px!important;max-height:74px!important;width:100%!important}
.garden-admin-brand__subtitle{display:none!important}
#menu-content{position:relative;height:100%}
#scroll-sidebar{position:absolute!important;top:88px!important;right:0;left:0;bottom:132px!important;overflow-y:auto}
@media (max-height:540px){#scroll-sidebar{top:80px!important;bottom:124px!important}}
#sidebar-greeting,#sidebar-top{position:absolute;right:0;bottom:84px;left:0;z-index:2;border-top:1px solid #000;border-bottom:none;padding:10px 14px 8px;background-color:#1a1a1a;box-shadow:0 -1px 0 rgba(197,160,89,.08)}
#sidebar-greeting>p,#sidebar-top>p{color:#999;margin:0;font-size:12px;line-height:1.45}
#sidebar-greeting>p>a,#sidebar-top>p>a{color:#ddd}
div.kcfinder-iframe .mfp-content{max-width:968px;width:96vw;height:auto!important;max-height:90vh}
.kcfinder-iframe .mfp-iframe-scaler{padding-top:0!important;height:min(72vh,640px)!important;position:relative;overflow:hidden}
.kcfinder-iframe .mfp-iframe-scaler iframe{position:absolute;top:0;left:0;width:100%;height:100%;background:#fff}
CSS;

    $FUNCS->add_css( $css );
}
PHP;

$kfunctions = preg_replace('/function garden_admin_branding_output\( &\$html \)\{.*?\n\}/s', trim($newBranding), $kfunctions, 1, $c1);
$kfunctions = preg_replace('/function garden_admin_sidebar_css\(\)\{.*?\n\}/s', trim($newSidebarCss), $kfunctions, 1, $c2);
if (!$c1 || !$c2) {
    fwrite(STDERR, "kfunctions replace failed branding=$c1 sidebar=$c2\n");
    exit(1);
}
file_put_contents($kfunctionsPath, $kfunctions);

passthru('php -l ' . escapeshellarg($kfunctionsPath), $code);
echo "Patched logo.html, styles.css, kfunctions.php\n";
exit($code);
