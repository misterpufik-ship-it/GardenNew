<?php
$path = dirname( __DIR__ ) . '/couch/theme/garden/styles.css';
$content = file_get_contents( $path );

if ( strpos( $content, '#menu-content{display:flex' ) !== false ) {
    echo "styles.css already patched\n";
    exit( 0 );
}

$content = preg_replace(
    '/\/\* Sidebar stack:.*?\*\/\s*#menu-wrap\{background-color:var\(--gl-panel\)\}\s*#sidebar-top\{position:relative;z-index:2;min-height:35px;background-color:var\(--gl-panel\)\}\s*#sidebar-top>p\{color:var\(--gl-muted\)\}\s*#sidebar-top>p>a\{color:var\(--gl-text\)\}\s*#scroll-sidebar\{top:182px!important\}\s*@media \(max-height:540px\)\{#scroll-sidebar\{top:152px!important\}\}/s',
    "/* Sidebar stack: logo + menu + greeting at bottom */\n#menu-wrap{background-color:var(--gl-panel)}\n#menu-content{display:flex;flex-direction:column;height:calc(100% - 96px)}\n#scroll-sidebar{position:relative!important;top:auto!important;flex:1 1 auto;min-height:0}\n#sidebar-greeting,#sidebar-top{border-top:1px solid #000;border-bottom:none;padding:10px 14px 8px;flex:0 0 auto}\n#sidebar-greeting>p,#sidebar-top>p{color:var(--gl-muted);margin:0;font-size:12px;line-height:1.45}\n#sidebar-greeting>p>a,#sidebar-top>p>a{color:var(--gl-text)}\n#sidebar-btns{flex:0 0 auto}",
    $content,
    1,
    $count
);

if ( !$count ) {
    fwrite( STDERR, "sidebar block not replaced\n" );
    exit( 1 );
}

$content = preg_replace(
    '/\/\* Sidebar logo \+ collapsed layout \*\/\s*#menu-wrap \.garden-admin-brand\{padding:10px 8px 6px\}\s*#menu-wrap \.garden-admin-brand__logo,#menu-wrap #logo\{max-width:198px!important;max-height:74px!important;width:100%!important\}\s*#scroll-sidebar\{top:158px!important\}\s*@media \(max-height:540px\)\{#scroll-sidebar\{top:138px!important\}\}/s',
    "/* Sidebar logo + collapsed layout */\n#menu-wrap .garden-admin-brand{padding:10px 8px 6px}\n#menu-wrap .garden-admin-brand__logo,#menu-wrap #logo{max-width:198px!important;max-height:74px!important;width:100%!important}",
    $content,
    1,
    $count2
);

file_put_contents( $path, $content );
echo "Patched styles.css ($count/$count2)\n";
