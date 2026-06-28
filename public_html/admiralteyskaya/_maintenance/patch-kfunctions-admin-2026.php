<?php
$path = dirname( __DIR__ ) . '/couch/addons/kfunctions.php';
$content = file_get_contents( $path );
if ( $content === false ) {
    fwrite( STDERR, "Cannot read kfunctions.php\n" );
    exit( 1 );
}

if ( strpos( $content, "'preloader-settings.php'" ) === false ) {
    $content = preg_replace(
        "/(\s*'age-gate-settings\.php' => array\('field'=>'label_age_gate_settings')/",
        "\n        'preloader-settings.php' => array('field'=>'label_preloader_settings', 'title'=>'Прелоадер', 'weight'=>3),$1",
        $content,
        1,
        $count
    );
    if ( !$count ) {
        fwrite( STDERR, "preloader-settings label not inserted\n" );
        exit( 1 );
    }
}

if ( strpos( $content, "'preloader-settings.php' ), true )" ) === false ) {
    $content = str_replace(
        "elseif ( \$name === 'admin-labels.php' || \$name === 'booking-settings.php' || \$name === 'age-gate-settings.php' ){",
        "elseif ( in_array( \$name, array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php' ), true ) ){",
        $content,
        $count2
    );
    if ( !$count2 ) {
        fwrite( STDERR, "parent menu rule not updated\n" );
        exit( 1 );
    }
}

if ( strpos( $content, '#menu-content{display:flex' ) === false ) {
    $content = preg_replace(
        '/function garden_admin_sidebar_css\(\)\{.*?\n\}/s',
        "function garden_admin_sidebar_css(){\n    global \$FUNCS;\n\n    \$css = <<<'CSS'\n#menu-wrap .garden-admin-brand{padding:10px 8px 6px}\n#menu-wrap .garden-admin-brand__logo,#menu-wrap #logo{max-width:198px!important;max-height:74px!important;width:100%!important}\n.garden-admin-brand__subtitle{display:none!important}\n#menu-content{display:flex;flex-direction:column;height:calc(100% - 96px)}\n#scroll-sidebar{position:relative!important;top:auto!important;flex:1 1 auto;min-height:0}\n#sidebar-greeting,#sidebar-top{border-top:1px solid #000;border-bottom:none;padding:10px 14px 8px;flex:0 0 auto}\n#sidebar-greeting>p,#sidebar-top>p{color:#999;margin:0;font-size:12px;line-height:1.45}\n#sidebar-greeting>p>a,#sidebar-top>p>a{color:#ddd}\n#sidebar-btns{flex:0 0 auto}\nCSS;\n\n    \$FUNCS->add_css( \$css );\n}",
        $content,
        1,
        $count3
    );
    if ( !$count3 ) {
        fwrite( STDERR, "sidebar css not updated\n" );
        exit( 1 );
    }
}

if ( strpos( $content, 'insertBefore($btns)' ) === false ) {
    $content = preg_replace(
        '/function garden_admin_sidebar_js\(\)\{.*?\n\}/s',
        "function garden_admin_sidebar_js(){\n    global \$FUNCS;\n\n    \$js = <<<'JS'\n(function(\$){\n    \$(function(){\n        var \$greeting = \$('#sidebar-top');\n        var \$btns = \$('#sidebar-btns');\n        if (\$greeting.length && \$btns.length) {\n            \$greeting.attr('id', 'sidebar-greeting');\n            \$greeting.insertBefore(\$btns);\n        }\n\n        if ( typeof COUCH === 'undefined' || !COUCH.state ) return;\n        if ( \$.hasCookie('collapsed_groups') ) return;\n\n        var ids = [];\n        \$('#sidebar .nav-heading-toggle').each(function(){\n            ids.push(String(\$(this).data('id')));\n        });\n        COUCH.state.collapsedGroups = ids;\n    });\n})(jQuery);\nJS;\n\n    \$FUNCS->add_js( \$js );\n}",
        $content,
        1,
        $count4
    );
    if ( !$count4 ) {
        fwrite( STDERR, "sidebar js not updated\n" );
        exit( 1 );
    }
}

if ( strpos( $content, 'rel=["\'](?:shortcut )?icon' ) === false ) {
    $content = preg_replace(
        '/function garden_admin_branding_output\( &\$html \)\{.*?\n\}/s',
        "function garden_admin_branding_output( &\$html ){\n    \$html = preg_replace( '#<title>[^<]*</title>#', '<title>Garden Lounge</title>', \$html, 1 );\n    \$favicon = '<link rel=\"icon\" type=\"image/png\" href=\"/favicon.png\">' . \"\\n    \"\n        . '<link rel=\"shortcut icon\" type=\"image/png\" href=\"/favicon.png\">';\n    if ( preg_match( '#<link[^>]+rel=[\"\\'](?:shortcut )?icon[\"\\'][^>]*/>#i', \$html ) ) {\n        \$html = preg_replace( '#<link[^>]+rel=[\"\\'](?:shortcut )?icon[\"\\'][^>]*/>#i', \$favicon, \$html, 1 );\n    } else {\n        \$html = preg_replace( '#</head>#', \$favicon . \"\\n</head>\", \$html, 1 );\n    }\n    \$fonts = '<link rel=\"preconnect\" href=\"https://fonts.googleapis.com\"><link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin><link href=\"https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=Montserrat:wght@400;500;600&display=swap\" rel=\"stylesheet\">';\n    if ( strpos( \$html, 'fonts.googleapis.com' ) === false ) {\n        \$html = preg_replace( '#</head>#', \$fonts . \"\\n</head>\", \$html, 1 );\n    }\n    if ( defined( 'K_THEME_URL' ) && defined( 'K_THEME_DIR' ) && K_THEME_URL && is_file( K_THEME_DIR . 'styles.css' ) ) {\n        \$ver = filemtime( K_THEME_DIR . 'styles.css' );\n        \$html = str_replace( K_THEME_URL . 'styles.css', K_THEME_URL . 'styles.css?v=' . \$ver, \$html );\n    }\n}",
        $content,
        1,
        $count5
    );
    if ( !$count5 ) {
        fwrite( STDERR, "branding not updated\n" );
        exit( 1 );
    }
}

if ( strpos( $content, 'login-remember' ) === false ) {
    $content = preg_replace(
        '/(function garden_admin_login_css\(\)\{.*?\$css = <<<\'CSS\'\n)(.*?)(\nCSS;\n\n    \$FUNCS->add_css\( \$css \);\n\})/s',
        '$1$2' . "\n#simple-page .login-remember{margin:0 0 14px;text-align:left}\n#simple-page .login-remember label{display:inline-flex;align-items:center;gap:8px;color:#bbb;font-size:12px;font-weight:500;cursor:pointer;margin:0}\n#simple-page .login-remember input[type=checkbox]{margin:0}" . '$3',
        $content,
        1,
        $count6
    );
    if ( !$count6 ) {
        fwrite( STDERR, "login css not updated\n" );
        exit( 1 );
    }
}

file_put_contents( $path, $content );
echo "Patched kfunctions.php\n";
