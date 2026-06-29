<?php
/**
 * Restore Garden favicon on Couch admin login and all /couch/ pages.
 */
$root = dirname(__DIR__);
$kfunctionsPath = $root . '/couch/addons/kfunctions.php';
$mainHtmlPath = $root . '/couch/theme/garden/main.html';

$newBranding = <<<'PHP'
function garden_is_couch_admin_context(){
    if ( defined( 'K_ADMIN' ) ) {
        return true;
    }
    $uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
    return ( strpos( $uri, '/couch/' ) !== false );
}

function garden_admin_branding_output( &$html ){
    if ( !garden_is_couch_admin_context() ) {
        return;
    }

    $html = preg_replace( '#<title>[^<]*</title>#', '<title>Garden Lounge</title>', $html, 1 );
    $html = preg_replace( '#<link[^>]+rel=["\'](?:shortcut )?icon["\'][^>]*/>#i', '', $html );
    $verPng = @filemtime( $_SERVER['DOCUMENT_ROOT'] . '/favicon.png' ) ?: time();
    $favicon = '<link rel="icon" type="image/png" sizes="32x32" href="/favicon.png?v=' . $verPng . '">' . "\n    "
        . '<link rel="shortcut icon" type="image/png" href="/favicon.png?v=' . $verPng . '">' . "\n    "
        . '<link rel="apple-touch-icon" href="/favicon.png?v=' . $verPng . '">';
    $html = preg_replace( '#</head>#', $favicon . "\n</head>", $html, 1 );

    $fonts = '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">';
    if ( strpos( $html, 'fonts.googleapis.com' ) === false ) {
        $html = preg_replace( '#</head>#', $fonts . "\n</head>", $html, 1 );
    }
    if ( defined( 'K_THEME_URL' ) && defined( 'K_THEME_DIR' ) && K_THEME_URL && is_file( K_THEME_DIR . 'styles.css' ) ) {
        $ver = filemtime( K_THEME_DIR . 'styles.css' );
        $html = str_replace( K_THEME_URL . 'styles.css', K_THEME_URL . 'styles.css?v=' . $ver, $html );
    }
}
PHP;

$kfunctions = file_get_contents($kfunctionsPath);
$kfunctions = preg_replace(
    '/function garden_admin_branding_output\( &\$html \)\{.*?\n\}/s',
    trim($newBranding),
    $kfunctions,
    1,
    $c1
);
if (!$c1) {
    fwrite(STDERR, "Failed to replace garden_admin_branding_output\n");
    exit(1);
}
file_put_contents($kfunctionsPath, $kfunctions);

$mainHtml = file_get_contents($mainHtmlPath);
$mainHtml = str_replace(
    '<link href="<cms:show k_system_theme_link />includes/admin/images/favicon.ico" rel="shortcut icon"/>',
    '<link rel="icon" type="image/png" href="/favicon.png">' . "\n    "
        . '<link rel="shortcut icon" type="image/png" href="/favicon.png">',
    $mainHtml,
    $c2
);
if (!$c2) {
    fwrite(STDERR, "garden/main.html favicon line not found\n");
    exit(1);
}
file_put_contents($mainHtmlPath, $mainHtml);

passthru('php -l ' . escapeshellarg($kfunctionsPath), $code);
echo "Patched kfunctions.php and garden/main.html\n";
exit($code ?: 0);
