<?php
$path = dirname( __DIR__ ) . '/couch/addons/kfunctions.php';
$content = file_get_contents( $path );
if ( strpos( $content, 'insertBefore($btns)' ) !== false ) {
    echo "sidebar js already patched\n";
    exit( 0 );
}

$content = preg_replace(
    '/function garden_admin_sidebar_js\(\)\{.*?\n\}/s',
    "function garden_admin_sidebar_js(){\n    global \$FUNCS;\n\n    \$js = <<<'JS'\n(function(\$){\n    \$(function(){\n        var \$greeting = \$('#sidebar-top');\n        var \$btns = \$('#sidebar-btns');\n        if (\$greeting.length && \$btns.length) {\n            \$greeting.attr('id', 'sidebar-greeting');\n            \$greeting.insertBefore(\$btns);\n        }\n\n        if ( typeof COUCH === 'undefined' || !COUCH.state ) return;\n        if ( \$.hasCookie('collapsed_groups') ) return;\n\n        var ids = [];\n        \$('#sidebar .nav-heading-toggle').each(function(){\n            ids.push(String(\$(this).data('id')));\n        });\n        COUCH.state.collapsedGroups = ids;\n    });\n})(jQuery);\nJS;\n\n    \$FUNCS->add_js( \$js );\n}",
    $content,
    1,
    $count
);

if ( !$count ) {
    fwrite( STDERR, "sidebar js not updated\n" );
    exit( 1 );
}

file_put_contents( $path, $content );
echo "Patched sidebar js\n";
