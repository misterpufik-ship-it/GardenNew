<?php
/**
 * Revert sidebar flex (breaks mCustomScrollbar) while keeping footer alignment.
 */
$root = dirname(__DIR__);
$stylesPath = $root . '/couch/theme/garden/styles.css';
$kfnPath = $root . '/couch/addons/kfunctions.php';

$shellCss = <<<'CSS'
/* === Garden admin shell v2 === */
:root{--gl-admin-header:66px;--gl-admin-sidebar-footer:119px}
#sidebar,#menu-wrap,#logo-wrap{background-color:var(--gl-black)!important;background-image:none!important}
#logo-wrap{border-bottom:1px solid var(--gl-border)}
#menu-wrap .garden-admin-brand{padding:10px 10px 6px}
#menu-wrap .garden-admin-brand__logo,#menu-wrap #logo{max-width:210px!important;max-height:82px!important;width:100%!important}
#menu-content{position:relative;height:100%}
#scroll-sidebar{position:absolute!important;top:76px!important;right:0;left:0;bottom:132px!important;overflow-y:auto}
@media (max-height:540px){#scroll-sidebar{top:70px!important;bottom:124px!important}}
#nav-links{display:none!important}
#sidebar-greeting,#sidebar-top{position:absolute!important;right:0;bottom:84px;left:0;z-index:2;border-top:1px solid #000;border-bottom:none;padding:10px 14px 8px;background-color:var(--gl-black)!important;box-shadow:0 -1px 0 rgba(197,160,89,.08)}
#sidebar-greeting>p,#sidebar-top>p{color:var(--gl-muted);margin:0;font-size:12px;line-height:1.45}
#sidebar-greeting>p>a,#sidebar-top>p>a{color:var(--gl-text)}
#sidebar-btns{position:absolute!important;right:0;bottom:24px;left:0;height:60px!important;padding:11px 10px 10px!important;border-top:1px solid #000!important;background-color:var(--gl-black)!important}
#sidebar-btns>#log-out{width:110px!important}
#sidebar-btns>#view-site{width:109px!important}
#sidebar-bot{display:block!important;position:absolute!important;right:0;bottom:0;left:0;height:24px!important;visibility:hidden;overflow:hidden;padding:0!important;margin:0!important;border:0!important}
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

$styles = file_get_contents($stylesPath);
$styles = preg_replace(
    '/\/\* === Garden admin shell v2 === \*\/.*$/s',
    trim($shellCss),
    $styles,
    1,
    $count
);
if (!$count) {
    fwrite(STDERR, "styles shell block not found\n");
    exit(1);
}
file_put_contents($stylesPath, $styles);

$kfnCss = str_replace(
    array('var(--gl-black)', 'var(--gl-border)', 'var(--gl-muted)', 'var(--gl-text)', 'var(--gl-gold)'),
    array('#0a0a0a', 'rgba(197,160,89,.28)', '#999', '#ddd', '#C5A059'),
    $shellCss
);
$kfnCss .= "\ndiv.kcfinder-iframe .mfp-content{max-width:968px;width:96vw;height:auto!important;max-height:90vh}\n";
$kfnCss .= ".kcfinder-iframe .mfp-iframe-scaler{padding-top:0!important;height:min(72vh,640px)!important;position:relative;overflow:hidden}\n";
$kfnCss .= ".kcfinder-iframe .mfp-iframe-scaler iframe{position:absolute;top:0;left:0;width:100%;height:100%;background:#fff}\n";

$kfn = file_get_contents($kfnPath);
$replacement = "function garden_admin_sidebar_css(){\n    global \$FUNCS;\n\n    \$css = <<<'CSS'\n" . $kfnCss . "CSS;\n\n    \$FUNCS->add_css( \$css );\n}";
$kfn = preg_replace(
    '/function garden_admin_sidebar_css\(\)\{.*?\n\}\s*\n\$FUNCS->add_event_listener\( \'add_admin_css\', \'garden_admin_sidebar_css\' \);/s',
    $replacement . "\n\n\$FUNCS->add_event_listener( 'add_admin_css', 'garden_admin_sidebar_css' );",
    $kfn,
    1,
    $count2
);
if (!$count2) {
    fwrite(STDERR, "kfunctions css replace failed\n");
    exit(1);
}
file_put_contents($kfnPath, $kfn);

passthru('php -l ' . escapeshellarg($kfnPath), $code);
echo "Fixed admin sidebar layout (absolute positioning restored)\n";
exit($code);
