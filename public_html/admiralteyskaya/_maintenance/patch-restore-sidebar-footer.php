<?php
$root = dirname(__DIR__);
$stylesPath = $root . '/couch/theme/garden/styles.css';
$kfunctionsPath = $root . '/couch/addons/kfunctions.php';

$shellV2 = <<<'CSS'
/* === Garden admin shell v2 === */
#sidebar,#menu-wrap,#logo-wrap{background-color:var(--gl-black)!important;background-image:none!important}
#logo-wrap{border-bottom:1px solid var(--gl-border)}
#menu-wrap .garden-admin-brand{padding:10px 10px 6px}
#menu-wrap .garden-admin-brand__logo,#menu-wrap #logo{max-width:210px!important;max-height:82px!important;width:100%!important}
#menu-content{position:relative;height:100%}
#scroll-sidebar{position:absolute!important;top:76px!important;right:0;left:0;bottom:132px!important;overflow-y:auto}
@media (max-height:540px){#scroll-sidebar{top:70px!important;bottom:124px!important}}
#nav-links,#sidebar-bot{display:none!important}
#sidebar-greeting,#sidebar-top{position:absolute!important;right:0;bottom:84px;left:0;z-index:2;border-top:1px solid #000;border-bottom:none;padding:10px 14px 8px;background-color:var(--gl-black)!important;box-shadow:0 -1px 0 rgba(197,160,89,.08)}
#sidebar-greeting>p,#sidebar-top>p{color:var(--gl-muted);margin:0;font-size:12px;line-height:1.45}
#sidebar-greeting>p>a,#sidebar-top>p>a{color:var(--gl-text)}
#sidebar-btns{position:absolute!important;right:0;bottom:24px;left:0;height:60px!important;padding:11px 10px 10px!important;border-top:1px solid #000!important;background-color:var(--gl-black)!important}
#sidebar-btns>#log-out{width:110px!important}
#sidebar-btns>#view-site{width:109px!important}
#header{background:var(--gl-black)!important;border-bottom:1px solid var(--gl-border)!important}
#header-title,#header ul#tabs,#tabs{display:none!important}
#header .subtitle{display:none!important}
#gl-header-user{display:none!important}
#scroll-content{background:#f3f3f3}
#content{background:#fff;min-height:calc(100vh - 58px);padding:18px 24px 28px;border-top:0}
body #tabs-page #content{background:#fff;padding-bottom:28px}
.panel,.group-wrapper .panel{border-color:var(--gl-border)}
.gl-admin-dump-link{margin:0 0 12px;font-size:12px}
.gl-admin-dump-link a{color:var(--gl-gold)}

CSS;

$styles = file_get_contents($stylesPath);
$styles = preg_replace('/\/\* === Garden admin shell v2 === \*\/.*$/s', rtrim($shellV2), $styles);
file_put_contents($stylesPath, $styles);

$newSidebarJs = <<<'PHP'
function garden_admin_sidebar_js(){
    global $FUNCS;

    $js = <<<'JS'
(function($){
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
        var $greeting = $('#sidebar-top');
        var $btns = $('#sidebar-btns');
        if ($greeting.length && $btns.length) {
            $greeting.attr('id', 'sidebar-greeting');
            $greeting.insertBefore($btns);
        }
        $('#gl-header-user').remove();

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

$newSidebarCss = <<<'PHP'
function garden_admin_sidebar_css(){
    global $FUNCS;

    $css = <<<'CSS'
#sidebar,#menu-wrap,#logo-wrap{background-color:#0a0a0a!important;background-image:none!important}
#menu-wrap .garden-admin-brand{padding:10px 10px 6px}
#menu-wrap .garden-admin-brand__logo,#menu-wrap #logo{max-width:210px!important;max-height:82px!important;width:100%!important}
.garden-admin-brand__subtitle{display:none!important}
#menu-content{position:relative;height:100%}
#scroll-sidebar{position:absolute!important;top:76px!important;right:0;left:0;bottom:132px!important;overflow-y:auto}
@media (max-height:540px){#scroll-sidebar{top:70px!important;bottom:124px!important}}
#nav-links,#sidebar-bot{display:none!important}
#sidebar-greeting,#sidebar-top{position:absolute!important;right:0;bottom:84px;left:0;z-index:2;border-top:1px solid #000;border-bottom:none;padding:10px 14px 8px;background-color:#0a0a0a!important;box-shadow:0 -1px 0 rgba(197,160,89,.08)}
#sidebar-greeting>p,#sidebar-top>p{color:#999;margin:0;font-size:12px;line-height:1.45}
#sidebar-greeting>p>a,#sidebar-top>p>a{color:#ddd}
#sidebar-btns{position:absolute!important;right:0;bottom:24px;left:0;height:60px!important;padding:11px 10px 10px!important;border-top:1px solid #000!important;background-color:#0a0a0a!important}
#sidebar-btns>#log-out{width:110px!important}
#sidebar-btns>#view-site{width:109px!important}
#header{background:#0a0a0a!important;border-bottom:1px solid rgba(197,160,89,.28)!important}
#header-title,#header ul#tabs,#tabs{display:none!important}
#header .subtitle{display:none!important}
#gl-header-user{display:none!important}
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

$kfunctions = file_get_contents($kfunctionsPath);
$kfunctions = preg_replace('/function garden_admin_sidebar_js\(\)\{.*?\n\}/s', trim($newSidebarJs), $kfunctions, 1, $c1);
$kfunctions = preg_replace('/function garden_admin_sidebar_css\(\)\{.*?\n\}/s', trim($newSidebarCss), $kfunctions, 1, $c2);
if (!$c1 || !$c2) {
    fwrite(STDERR, "kfunctions replace failed: js=$c1 css=$c2\n");
    exit(1);
}
file_put_contents($kfunctionsPath, $kfunctions);

passthru('php -l ' . escapeshellarg($kfunctionsPath), $code);
echo "Restored sidebar footer (greeting + buttons at bottom)\n";
exit($code);
