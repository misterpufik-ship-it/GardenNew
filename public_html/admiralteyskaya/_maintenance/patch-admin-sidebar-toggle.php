<?php
/**
 * Restore sidebar hide/show toggle above greeting block.
 * Run: php _maintenance/patch-admin-sidebar-toggle.php
 * Web: /admiralteyskaya/_maintenance/patch-admin-sidebar-toggle-web.php?token=gl-cache-clear-20260623
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

    function positionSidebarToggle(){
        var $toggle = $('#sidebar-toggle');
        if (!$toggle.length) return;

        var $greeting = $('#sidebar-greeting, #sidebar-top').first();
        if ($greeting.length) {
            $toggle.insertBefore($greeting);
        }

        if (!$toggle.hasClass('gl-sidebar-toggle-bar')) {
            $toggle.addClass('gl-sidebar-toggle-bar');
        }
        if (!$toggle.find('.gl-sidebar-toggle-label').length) {
            $toggle.append('<span class="gl-sidebar-toggle-label">Скрыть меню</span>');
        }

        $toggle.attr('title', 'Скрыть боковое меню').attr('aria-label', 'Скрыть боковое меню');
    }

    function syncSidebarToggleLabel(){
        var $toggle = $('#sidebar-toggle');
        var $label = $toggle.find('.gl-sidebar-toggle-label');
        if (!$label.length) return;
        $label.text($('#sidebar').hasClass('collapsed') ? 'Показать меню' : 'Скрыть меню');
        $toggle.attr(
            'title',
            $('#sidebar').hasClass('collapsed') ? 'Показать боковое меню' : 'Скрыть боковое меню'
        );
    }

    $(function(){
        var $greeting = $('#sidebar-top');
        var $btns = $('#sidebar-btns');
        if ($greeting.length && $btns.length) {
            $greeting.attr('id', 'sidebar-greeting');
            $greeting.insertBefore($btns);
        }

        positionSidebarToggle();
        syncSidebarToggleLabel();

        $('#sidebar-toggle').on('click', function(){
            window.setTimeout(syncSidebarToggleLabel, 0);
        });

        $('#gl-header-user').remove();
        addDumpLink();

        if ( typeof COUCH === 'undefined' || !COUCH.state ) return;
        if ( $.hasCookie('collapsed_groups') ) return;
        var ids = [];
        $('#sidebar .nav-heading-toggle').each(function(){
            ids.push(String($(this).data('id')));
        });
        COUCH.state.collapsedGroups = ids;
        gardenAdminLandingRedirect();
    });
})(jQuery);
JS;

    $FUNCS->add_js( $js );
}
PHP;

$content = file_get_contents($path);
$content = preg_replace(
    '/function garden_admin_sidebar_js\(\)\{.*?\n\}/s',
    trim($newSidebarJs),
    $content,
    1,
    $jsCount
);

$toggleCss = <<<'CSS'
#scroll-sidebar{bottom:152px!important}
@media (max-height:540px){#scroll-sidebar{bottom:144px!important}}
#sidebar-toggle{
  position:absolute!important;
  right:0!important;
  left:0!important;
  bottom:120px!important;
  display:flex!important;
  align-items:center;
  justify-content:center;
  gap:8px;
  width:100%!important;
  height:32px!important;
  margin:0!important;
  padding:0 12px!important;
  border:0!important;
  border-top:1px solid rgba(197,160,89,.22)!important;
  border-radius:0!important;
  background:rgba(197,160,89,.1)!important;
  background-image:none!important;
  fill:var(--gl-gold)!important;
  color:var(--gl-gold)!important;
  cursor:pointer;
  z-index:3!important;
  box-shadow:none!important;
}
#sidebar-toggle:hover,#sidebar-toggle:focus{
  background:rgba(197,160,89,.18)!important;
  border-color:rgba(197,160,89,.45)!important;
  fill:#fff!important;
  color:#fff!important;
}
#sidebar-toggle>.i{position:static!important;top:auto!important}
.gl-sidebar-toggle-label{
  font-family:'Montserrat',Arial,sans-serif;
  font-size:11px;
  font-weight:600;
  letter-spacing:.08em;
  text-transform:uppercase;
  color:inherit;
  line-height:1;
}
#sidebar.collapsed #sidebar-toggle{
  position:fixed!important;
  left:0!important;
  right:auto!important;
  top:50%!important;
  bottom:auto!important;
  transform:translateY(-50%);
  width:28px!important;
  height:56px!important;
  padding:0!important;
  border:1px solid rgba(197,160,89,.45)!important;
  border-left:0!important;
  border-radius:0 6px 6px 0!important;
  background:var(--gl-black)!important;
}
#sidebar.collapsed .gl-sidebar-toggle-label{display:none!important}
CSS;

$content = preg_replace(
    '/#scroll-sidebar\{position:absolute!important;top:0!important;right:0;left:0;bottom:132px!important;overflow-y:auto\}/',
    "#scroll-sidebar{position:absolute!important;top:0!important;right:0;left:0;bottom:152px!important;overflow-y:auto}",
    $content,
    1,
    $scrollCount
);

$content = preg_replace(
    '/@media \(max-height:540px\)\{#scroll-sidebar\{top:0!important;bottom:124px!important\}\}/',
    '@media (max-height:540px){#scroll-sidebar{top:0!important;bottom:144px!important}}',
    $content,
    1,
    $scrollMobCount
);

$content = preg_replace(
    '/#sidebar-toggle\{\s*display:block!important;\s*z-index:210!important;\s*\}/s',
    trim($toggleCss),
    $content,
    1,
    $toggleCount
);

if (!$jsCount) {
    fwrite(STDERR, "garden_admin_sidebar_js replace failed\n");
    exit(1);
}

if (!$toggleCount) {
    fwrite(STDERR, "sidebar-toggle CSS replace failed\n");
    exit(1);
}

file_put_contents($path, $content);

$stylesPath = $root . '/couch/theme/garden/styles.css';
if (is_file($stylesPath)) {
    $styles = file_get_contents($stylesPath);
    $styles = preg_replace(
        '/#scroll-sidebar\{position:absolute!important;top:0!important;right:0;left:0;bottom:132px!important;overflow-y:auto\}/',
        "#scroll-sidebar{position:absolute!important;top:0!important;right:0;left:0;bottom:152px!important;overflow-y:auto}",
        $styles
    );
    $styles = preg_replace(
        '/@media \(max-height:540px\)\{#scroll-sidebar\{top:0!important;bottom:124px!important\}\}/',
        '@media (max-height:540px){#scroll-sidebar{top:0!important;bottom:144px!important}}',
        $styles
    );
    $styles = preg_replace(
        '/#sidebar-toggle\{\s*display:block!important;\s*z-index:210!important;\s*\}/s',
        trim($toggleCss),
        $styles,
        1,
        $stylesToggleCount
    );
    if ($stylesToggleCount) {
        file_put_contents($stylesPath, $styles);
        echo "Updated styles.css\n";
    }
}

passthru('php -l ' . escapeshellarg($path), $code);
echo "Restored sidebar toggle above greeting\n";
exit($code);
