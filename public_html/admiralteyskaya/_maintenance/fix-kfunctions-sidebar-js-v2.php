<?php
$path = dirname(__DIR__) . '/couch/addons/kfunctions.php';
$content = file_get_contents($path);

$fixed = <<<'PHP'
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

$content = preg_replace('/function garden_admin_sidebar_js\(\)\{.*?\n\}\s*\n\$FUNCS->add_event_listener\( \'add_admin_js\', \'garden_admin_sidebar_js\' \);/s', trim($fixed) . "\n\n\$FUNCS->add_event_listener( 'add_admin_js', 'garden_admin_sidebar_js' );", $content, 1, $count);
if (!$count) {
    fwrite(STDERR, "sidebar_js fix failed\n");
    exit(1);
}
file_put_contents($path, $content);
passthru('php -l ' . escapeshellarg($path), $code);
echo "Fixed garden_admin_sidebar_js\n";
exit($code);
