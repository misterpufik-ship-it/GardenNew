<?php
/**
 * Sidebar collapse toggle: arrow button to the right of the greeting line.
 * Run: php _maintenance/patch-admin-sidebar-arrow-toggle.php
 * Web: /admiralteyskaya/_maintenance/patch-admin-sidebar-arrow-toggle-web.php?token=gl-cache-clear-20260623
 */
$root = dirname(__DIR__);
$path = $root . '/couch/addons/kfunctions.php';
if (!is_file($path)) {
    fwrite(STDERR, "kfunctions.php not found\n");
    exit(1);
}

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

    function gardenAdminLandingRedirect(){
        var path = window.location.pathname || '';
        if ( !/\/couch\/admin\.php$/i.test(path) ) return;
        var params = new URLSearchParams(window.location.search || '');
        if ( params.has('o') ) return;
        window.location.replace(path + '?o=admin-instructions.php&q=list');
    }

    function setupSidebarGreetingRow(){
        var $greeting = $('#sidebar-top');
        var $btns = $('#sidebar-btns');
        if ($greeting.length && $btns.length) {
            $greeting.attr('id', 'sidebar-greeting');
            $greeting.insertBefore($btns);
        }
    }

    function setupSidebarToggle(){
        var $toggle = $('#sidebar-toggle');
        if (!$toggle.length) return;

        var $menuContent = $('#menu-content');
        var $greeting = $('#sidebar-greeting, #sidebar-top').first();
        if ($menuContent.length) {
            $toggle.appendTo($menuContent);
        } else if ($greeting.length) {
            $toggle.insertBefore($greeting);
        }

        $toggle.addClass('gl-sidebar-toggle-btn');
        $toggle.find('>.i').attr('aria-hidden', 'true');

        if (!$toggle.find('.gl-sidebar-toggle-arrow').length) {
            $toggle.contents().filter(function(){
                return this.nodeType === 3;
            }).remove();
            $toggle.append('<span class="gl-sidebar-toggle-arrow" aria-hidden="true"></span>');
        }

        syncSidebarToggleArrow();
    }

    function syncSidebarToggleArrow(){
        var $toggle = $('#sidebar-toggle');
        var $arrow = $toggle.find('.gl-sidebar-toggle-arrow');
        if (!$arrow.length) return;

        var collapsed = $('#sidebar').hasClass('collapsed');
        $arrow.text(collapsed ? '\u203A' : '\u2039');
        $toggle.attr('title', collapsed ? 'РџРѕРєР°Р·Р°С‚СЊ РјРµРЅСЋ' : 'РЎРєСЂС‹С‚СЊ РјРµРЅСЋ');
        $toggle.attr('aria-label', collapsed ? 'РџРѕРєР°Р·Р°С‚СЊ Р±РѕРєРѕРІРѕРµ РјРµРЅСЋ' : 'РЎРєСЂС‹С‚СЊ Р±РѕРєРѕРІРѕРµ РјРµРЅСЋ');
    }

    function syncSidebarTogglePosition(){
        var $sidebar = $('#sidebar');
        var $toggle = $('#sidebar-toggle');
        var $greeting = $('#sidebar-greeting, #sidebar-top').first();
        if (!$toggle.length || !$greeting.length) return;

        var rect = $greeting[0].getBoundingClientRect();
        var top = rect.top - $toggle.outerHeight() - 4;
        $toggle.css({
            top: Math.round(top) + 'px',
            bottom: 'auto',
            left: '0',
            transform: 'none'
        });
    }

    $(function(){
        setupSidebarGreetingRow();
        setupSidebarToggle();

        $('#sidebar-toggle').on('click', function(){
            window.setTimeout(function(){
                syncSidebarToggleArrow();
                syncSidebarTogglePosition();
            }, 0);
        });

        $(window).on('resize', syncSidebarTogglePosition);

        addDumpLink();
        gardenAdminLandingRedirect();

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

$newToggleCssFn = <<<'PHP'
function garden_admin_sidebar_toggle_css(){
    global $FUNCS;

    $css = <<<'CSS'
/* gl-sidebar-arrow-toggle */
#scroll-sidebar{bottom:176px!important}
@media (max-height:540px){#scroll-sidebar{bottom:168px!important}}
#sidebar-toggle.gl-sidebar-toggle-btn{
  position:absolute!important;
  right:0!important;
  left:auto!important;
  bottom:124px!important;
  top:auto!important;
  display:flex!important;
  align-items:center!important;
  justify-content:center!important;
  width:26px!important;
  height:52px!important;
  margin:0!important;
  padding:0!important;
  border:1px solid rgba(197,160,89,.35)!important;
  border-right:0!important;
  border-radius:6px 0 0 6px!important;
  background:#0a0a0a!important;
  background-image:none!important;
  fill:var(--gl-gold,#C5A059)!important;
  color:var(--gl-gold,#C5A059)!important;
  cursor:pointer!important;
  z-index:220!important;
  box-shadow:none!important;
  transform:none!important;
}
#sidebar-toggle.gl-sidebar-toggle-btn:hover,
#sidebar-toggle.gl-sidebar-toggle-btn:focus{
  background:rgba(197,160,89,.12)!important;
  border-color:rgba(197,160,89,.55)!important;
  color:#fff!important;
  fill:#fff!important;
}
#sidebar-toggle.gl-sidebar-toggle-btn>.i{display:none!important}
.gl-sidebar-toggle-arrow{
  display:block!important;
  font-size:20px!important;
  line-height:1!important;
  font-weight:600!important;
  color:inherit!important;
  pointer-events:none!important;
}
#sidebar #nav a[href*="menu"] .i{display:none!important}
CSS;

    $FUNCS->add_css( $css );
}

$FUNCS->add_event_listener( 'add_admin_css', 'garden_admin_sidebar_toggle_css' );
PHP;

$content = file_get_contents($path);

$content = preg_replace(
    '/function garden_admin_sidebar_js\(\)\{.*?function garden_admin_sidebar_css\(\)\{/s',
    trim($newSidebarJs) . "\n\n\$FUNCS->add_event_listener( 'add_admin_js', 'garden_admin_sidebar_js' );\n\nfunction garden_admin_sidebar_css(){",
    $content,
    1,
    $jsCount
);

if (strpos($content, 'function garden_admin_sidebar_toggle_css()') !== false) {
    $content = preg_replace(
        '/function garden_admin_sidebar_toggle_css\(\)\{.*?\n\}\s*\n\$FUNCS->add_event_listener\( \'add_admin_css\', \'garden_admin_sidebar_toggle_css\' \);/s',
        trim($newToggleCssFn),
        $content,
        1,
        $cssFnCount
    );
} else {
    $content = rtrim($content) . "\n\n" . trim($newToggleCssFn) . "\n";
    $cssFnCount = 1;
}

if (!$jsCount) {
    fwrite(STDERR, "garden_admin_sidebar_js replace failed\n");
    exit(1);
}

if (!$cssFnCount) {
    fwrite(STDERR, "garden_admin_sidebar_toggle_css replace failed\n");
    exit(1);
}

file_put_contents($path, $content);

passthru('php -l ' . escapeshellarg($path), $code);
echo "Patched sidebar arrow toggle next to greeting\n";
exit($code);
