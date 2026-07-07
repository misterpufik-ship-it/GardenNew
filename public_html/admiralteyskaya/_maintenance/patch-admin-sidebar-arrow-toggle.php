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
        $('#sidebar-greeting, #sidebar-top').addClass('gl-sidebar-greeting-row');
    }

    function setupSidebarToggle(){
        var $toggle = $('#sidebar-toggle');
        if (!$toggle.length) return;

        var $greeting = $('#sidebar-greeting, #sidebar-top').first();
        if ($greeting.length) {
            $toggle.appendTo($greeting);
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
        $toggle.attr('title', collapsed ? 'Показать меню' : 'Скрыть меню');
        $toggle.attr('aria-label', collapsed ? 'Показать боковое меню' : 'Скрыть боковое меню');
    }

    $(function(){
        setupSidebarGreetingRow();
        setupSidebarToggle();

        $('#sidebar-toggle').on('click', function(){
            window.setTimeout(syncSidebarToggleArrow, 0);
        });

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
#sidebar-greeting.gl-sidebar-greeting-row,
#sidebar-top.gl-sidebar-greeting-row{
  display:flex!important;
  align-items:center!important;
  justify-content:space-between!important;
  gap:10px!important;
  padding:10px 12px 8px!important;
  border-top:1px solid rgba(197,160,89,.18)!important;
}
#sidebar-greeting.gl-sidebar-greeting-row>p,
#sidebar-top.gl-sidebar-greeting-row>p{
  flex:1 1 auto!important;
  min-width:0!important;
  margin:0!important;
  font-size:12px!important;
  line-height:1.45!important;
  white-space:nowrap!important;
  overflow:hidden!important;
  text-overflow:ellipsis!important;
}
#sidebar-toggle.gl-sidebar-toggle-btn{
  position:static!important;
  flex:0 0 28px!important;
  width:28px!important;
  height:28px!important;
  margin:0!important;
  padding:0!important;
  display:inline-flex!important;
  align-items:center!important;
  justify-content:center!important;
  border:1px solid rgba(197,160,89,.35)!important;
  border-radius:4px!important;
  background:rgba(197,160,89,.08)!important;
  background-image:none!important;
  fill:var(--gl-gold,#C5A059)!important;
  color:var(--gl-gold,#C5A059)!important;
  cursor:pointer!important;
  z-index:3!important;
  box-shadow:none!important;
  bottom:auto!important;
  left:auto!important;
  right:auto!important;
  top:auto!important;
  transform:none!important;
}
#sidebar-toggle.gl-sidebar-toggle-btn:hover,
#sidebar-toggle.gl-sidebar-toggle-btn:focus{
  background:rgba(197,160,89,.18)!important;
  border-color:rgba(197,160,89,.55)!important;
  color:#fff!important;
  fill:#fff!important;
}
#sidebar-toggle.gl-sidebar-toggle-btn>.i{
  display:none!important;
}
.gl-sidebar-toggle-arrow{
  display:block!important;
  font-size:20px!important;
  line-height:1!important;
  font-weight:600!important;
  color:inherit!important;
  pointer-events:none!important;
}
#sidebar.collapsed #sidebar-toggle.gl-sidebar-toggle-btn{
  position:fixed!important;
  left:0!important;
  top:50%!important;
  right:auto!important;
  bottom:auto!important;
  transform:translateY(-50%)!important;
  width:26px!important;
  height:52px!important;
  border-radius:0 6px 6px 0!important;
  border-left:0!important;
  z-index:220!important;
  background:#0a0a0a!important;
}
CSS;

    $FUNCS->add_css( $css );
}

$FUNCS->add_event_listener( 'add_admin_css', 'garden_admin_sidebar_toggle_css' );
PHP;

$content = file_get_contents($path);

$content = preg_replace(
    '/function garden_admin_sidebar_js\(\)\{.*?\n\}/s',
    trim($newSidebarJs),
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
