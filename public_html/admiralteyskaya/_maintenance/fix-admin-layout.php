<?php
$stylesPath = dirname(__DIR__) . '/couch/theme/garden/styles.css';
$kfunctionsPath = dirname(__DIR__) . '/couch/addons/kfunctions.php';
$logoPath = dirname(__DIR__) . '/couch/theme/garden/logo.html';

$sidebarBlock = <<<'CSS'
/* Sidebar stack: logo + menu + greeting at bottom */
#menu-wrap{background-color:var(--gl-panel)}
#menu-content{display:flex;flex-direction:column;height:calc(100% - 96px)}
#scroll-sidebar{position:relative!important;top:auto!important;flex:1 1 auto;min-height:0}
#sidebar-greeting,#sidebar-top{border-top:1px solid #000;border-bottom:none;padding:10px 14px 8px;flex:0 0 auto}
#sidebar-greeting>p,#sidebar-top>p{color:var(--gl-muted);margin:0;font-size:12px;line-height:1.45}
#sidebar-greeting>p>a,#sidebar-top>p>a{color:var(--gl-text)}
#sidebar-btns{flex:0 0 auto}
CSS;

$sidebarFix = <<<'CSS'
/* Sidebar: keep Couch absolute layout, greeting above footer buttons */
#menu-content{position:relative;height:100%}
#scroll-sidebar{position:absolute!important;top:124px!important;right:0;left:0;bottom:132px!important;overflow-y:auto}
@media (max-height:540px){#scroll-sidebar{top:96px!important;bottom:124px!important}}
#sidebar-greeting,#sidebar-top{position:absolute;right:0;bottom:84px;left:0;z-index:2;border-top:1px solid #000;border-bottom:none;padding:10px 14px 8px;background-color:var(--gl-panel);box-shadow:0 -1px 0 rgba(197,160,89,.08)}
#sidebar-greeting>p,#sidebar-top>p{color:var(--gl-muted);margin:0;font-size:12px;line-height:1.45}
#sidebar-greeting>p>a,#sidebar-top>p>a{color:var(--gl-text)}

/* KCFinder browse popup */
div.kcfinder-iframe .mfp-content{max-width:968px;width:96vw;height:auto!important;max-height:90vh}
.kcfinder-iframe .mfp-iframe-scaler{padding-top:0!important;height:min(72vh,640px)!important;position:relative;overflow:hidden}
.kcfinder-iframe .mfp-iframe-scaler iframe{position:absolute;top:0;left:0;width:100%;height:100%;background:#fff}
CSS;

$styles = file_get_contents($stylesPath);
if (strpos($styles, $sidebarBlock) !== false) {
    $styles = str_replace($sidebarBlock, $sidebarFix, $styles, $count);
} elseif (strpos($styles, 'KCFinder browse popup') === false) {
    $styles = preg_replace(
        '/\/\* Sidebar stack:.*?\*\/\s*#menu-wrap\{background-color:var\(--gl-panel\)\}.*?(?=#nav>\.nav-heading:first-child)/s',
        $sidebarFix . "\n",
        $styles,
        1,
        $count
    );
}
if (!isset($count) || !$count) {
    if (strpos($styles, 'KCFinder browse popup') === false) {
        $styles = str_replace(
            "#nav>.nav-heading:first-child{margin-top:6px}",
            $sidebarFix . "\n#nav>.nav-heading:first-child{margin-top:6px}",
            $styles,
            $count2
        );
    }
}
file_put_contents($stylesPath, $styles);

$logo = <<<'HTML'
<cms:capture into='my_logo'>
    <div class="garden-admin-brand garden-admin-brand--logo-only">
        <img
            <cms:if k_logo_id >id="<cms:show k_logo_id />"</cms:if>
            class="garden-admin-brand__logo<cms:if k_logo_class> <cms:show k_logo_class /></cms:if>"
            src="/img/logo-gl.webp"
            alt="Garden Lounge"
            width="197"
            height="184"
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

$kfunctions = file_get_contents($kfunctionsPath);
$newSidebarCss = <<<'PHP'
function garden_admin_sidebar_css(){
    global $FUNCS;

    $css = <<<'CSS'
#menu-wrap .garden-admin-brand{padding:10px 8px 6px}
#menu-wrap .garden-admin-brand__logo,#menu-wrap #logo{max-width:110px!important;max-height:74px!important;width:100%!important}
.garden-admin-brand__subtitle{display:none!important}
#menu-content{position:relative;height:100%}
#scroll-sidebar{position:absolute!important;top:124px!important;right:0;left:0;bottom:132px!important;overflow-y:auto}
@media (max-height:540px){#scroll-sidebar{top:96px!important;bottom:124px!important}}
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

$newLoginCss = <<<'PHP'
function garden_admin_login_css(){
    global $FUNCS;

    $css = <<<'CSS'
#simple-page .garden-admin-brand--logo-only{padding:22px 16px 10px}
#simple-page .garden-admin-brand__subtitle{display:none!important}
#simple-page .garden-admin-brand__logo,#simple-page #simple-logo{max-width:340px!important;max-height:110px!important;width:min(78vw,340px)!important}
#simple-page #simple-wrap .panel-heading.simple-heading{display:flex!important;justify-content:center!important;align-items:center!important;padding:12px 15px!important;color:#C5A059!important;text-align:center!important;background-color:#141414!important;background-image:none!important;text-shadow:none!important;font-weight:600;border-color:rgba(197,160,89,.28)!important}
#simple-page #simple-wrap .panel-heading.simple-heading .login-heading{display:block!important;width:100%!important;color:#C5A059!important;text-align:center!important;text-shadow:none!important;font-size:15px;letter-spacing:.04em}
#simple-page .login-remember{margin:0 0 14px;text-align:left}
#simple-page .login-remember label{display:inline-flex;align-items:center;gap:8px;color:#bbb;font-size:12px;font-weight:500;cursor:pointer;margin:0}
#simple-page .login-remember input[type=checkbox]{margin:0}
CSS;

    $FUNCS->add_css( $css );
}
PHP;

$kfunctions = preg_replace('/function garden_admin_sidebar_css\(\)\{.*?\n\}/s', trim($newSidebarCss), $kfunctions, 1, $c1);
$kfunctions = preg_replace('/function garden_admin_login_css\(\)\{.*?\n\}/s', trim($newLoginCss), $kfunctions, 1, $c2);
if (!$c1 || !$c2) {
    fwrite(STDERR, "kfunctions replace failed sidebar=$c1 login=$c2\n");
    exit(1);
}
file_put_contents($kfunctionsPath, $kfunctions);

passthru('php -l ' . escapeshellarg($kfunctionsPath), $code);
echo "Patched styles.css, logo.html, kfunctions.php\n";
exit($code);
