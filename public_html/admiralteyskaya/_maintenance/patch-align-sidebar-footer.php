<?php
/**
 * Align sidebar footer (greeting + buttons) with the right content area bottom edge.
 */
$root = dirname( __DIR__ );
$stylesPath = $root . '/couch/theme/garden/styles.css';
$kfnPath = $root . '/couch/addons/kfunctions.php';

$shellCss = <<<'CSS'
/* === Garden admin shell v2 === */
:root{--gl-admin-header:66px;--gl-admin-sidebar-footer:119px}
#sidebar{display:flex!important;flex-direction:column;background-color:var(--gl-black)!important;background-image:none!important}
#menu-wrap,#logo-wrap{flex:0 0 auto;background-color:var(--gl-black)!important;background-image:none!important}
#logo-wrap{border-bottom:1px solid var(--gl-border)}
#menu-wrap .garden-admin-brand{padding:10px 10px 6px}
#menu-wrap .garden-admin-brand__logo,#menu-wrap #logo{max-width:210px!important;max-height:82px!important;width:100%!important}
#menu-content{display:flex!important;flex-direction:column;flex:1 1 auto;min-height:0;position:relative;height:auto!important}
#scroll-sidebar{position:relative!important;top:auto!important;right:auto!important;bottom:auto!important;left:auto!important;flex:1 1 auto;min-height:0;overflow-y:auto}
#nav-links{display:none!important}
#sidebar-greeting,#sidebar-top{position:static!important;flex:0 0 auto;z-index:2;border-top:1px solid #000;border-bottom:none;padding:10px 14px 8px;background-color:var(--gl-black)!important;box-shadow:0 -1px 0 rgba(197,160,89,.08)}
#sidebar-greeting>p,#sidebar-top>p{color:var(--gl-muted);margin:0;font-size:12px;line-height:1.45}
#sidebar-greeting>p>a,#sidebar-top>p>a{color:var(--gl-text)}
#sidebar-btns{position:static!important;flex:0 0 60px;height:60px!important;padding:11px 10px 10px!important;border-top:1px solid #000!important;background-color:var(--gl-black)!important}
#sidebar-btns>#log-out{width:110px!important}
#sidebar-btns>#view-site{width:109px!important}
#sidebar-bot{display:block!important;flex:0 0 24px;height:24px!important;visibility:hidden;overflow:hidden;padding:0!important;margin:0!important;border:0!important}
#header{background:var(--gl-black)!important;border-bottom:1px solid var(--gl-border)!important;padding:14px 24px 0!important}
#header-inner{padding-bottom:14px!important;min-height:38px}
#header-title,#header ul#tabs,#tabs{display:none!important}
#header .subtitle{display:none!important}
#gl-header-user{display:none!important}
#scroll-content{background:#f3f3f3}
#content{background:#fff;min-height:calc(100vh - var(--gl-admin-header) - var(--gl-admin-sidebar-footer));padding:18px 24px 28px;border-top:0}
body #tabs-page #content{background:#fff;padding-bottom:28px}
.panel,.group-wrapper .panel{border-color:var(--gl-border)}
.gl-admin-dump-link{margin:0 0 12px;font-size:12px}
.gl-admin-dump-link a{color:var(--gl-gold)}
CSS;

$styles = file_get_contents( $stylesPath );
if ( !preg_match( '/\/\* === Garden admin shell v2 === \*\//', $styles ) ) {
    fwrite( STDERR, "shell v2 block not found in styles.css\n" );
    exit( 1 );
}
$styles = preg_replace(
    '/\/\* === Garden admin shell v2 === \*\/.*$/s',
    trim( $shellCss ),
    $styles,
    1,
    $count
);
if ( !$count ) {
    fwrite( STDERR, "styles.css shell replace failed\n" );
    exit( 1 );
}
file_put_contents( $stylesPath, $styles );

$kfnCss = $shellCss;
$kfnCss = str_replace( 'var(--gl-black)', '#0a0a0a', $kfnCss );
$kfnCss = str_replace( 'var(--gl-border)', 'rgba(197,160,89,.28)', $kfnCss );
$kfnCss = str_replace( 'var(--gl-muted)', '#999', $kfnCss );
$kfnCss = str_replace( 'var(--gl-text)', '#ddd', $kfnCss );
$kfnCss = str_replace( 'var(--gl-gold)', '#C5A059', $kfnCss );
$kfnCss .= "\ndiv.kcfinder-iframe .mfp-content{max-width:968px;width:96vw;height:auto!important;max-height:90vh}\n";
$kfnCss .= ".kcfinder-iframe .mfp-iframe-scaler{padding-top:0!important;height:min(72vh,640px)!important;position:relative;overflow:hidden}\n";
$kfnCss .= ".kcfinder-iframe .mfp-iframe-scaler iframe{position:absolute;top:0;left:0;width:100%;height:100%;background:#fff}";

$kfn = file_get_contents( $kfnPath );
$replacement = "function garden_admin_sidebar_css(){\n    global \$FUNCS;\n\n    \$css = <<<'CSS'\n" . $kfnCss . "CSS;\n\n    \$FUNCS->add_css( \$css );\n}";
$kfn = preg_replace(
    '/function garden_admin_sidebar_css\(\)\{.*?\n\}\s*\n\$FUNCS->add_event_listener\( \'add_admin_css\', \'garden_admin_sidebar_css\' \);/s',
    $replacement . "\n\n\$FUNCS->add_event_listener( 'add_admin_css', 'garden_admin_sidebar_css' );",
    $kfn,
    1,
    $count2
);
if ( !$count2 ) {
    fwrite( STDERR, "kfunctions sidebar css replace failed\n" );
    exit( 1 );
}
file_put_contents( $kfnPath, $kfn );

passthru( 'php -l ' . escapeshellarg( $kfnPath ), $code );
echo "Aligned sidebar footer CSS in styles.css and kfunctions.php\n";
exit( $code );
