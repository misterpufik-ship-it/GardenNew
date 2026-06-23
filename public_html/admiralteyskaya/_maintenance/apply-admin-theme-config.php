<?php
/**
 * Включает тему garden и язык RU в couch/config.php на сервере.
 * CLI: php apply-admin-theme-config.php [path/to/config.php]
 */
if ( php_sapi_name() !== 'cli' ) {
    die( 'CLI only' );
}

$config = isset( $argv[1] ) ? $argv[1] : dirname( __DIR__ ) . '/couch/config.php';
if ( !file_exists( $config ) ) {
    fwrite( STDERR, "Config not found: $config\n" );
    exit( 1 );
}

$contents = file_get_contents( $config );
if ( $contents === false ) {
    fwrite( STDERR, "Cannot read: $config\n" );
    exit( 1 );
}

$replacements = array(
    "/define\\(\\s*'K_ADMIN_LANG'\\s*,\\s*'[^']*'\\s*\\)\\s*;/" => "define( 'K_ADMIN_LANG', 'RU' );",
);

if ( preg_match( "/define\\(\\s*'K_ADMIN_THEME'/", $contents ) ) {
    $replacements["/define\\(\\s*'K_ADMIN_THEME'\\s*,\\s*'[^']*'\\s*\\)\\s*;/"] = "define( 'K_ADMIN_THEME', 'garden' );";
}
else {
    $needle = "define( 'K_ADMIN_LANG', 'RU' );";
    $insert = $needle . "\n    define( 'K_ADMIN_THEME', 'garden' );";
    if ( strpos( $contents, $needle ) === false ) {
        $contents = preg_replace(
            "/define\\(\\s*'K_ADMIN_LANG'\\s*,\\s*'[^']*'\\s*\\)\\s*;/",
            $insert,
            $contents,
            1,
            $count
        );
        if ( !$count ) {
            fwrite( STDERR, "K_ADMIN_LANG not found in config.\n" );
            exit( 1 );
        }
        unset( $replacements["/define\\(\\s*'K_ADMIN_LANG'\\s*,\\s*'[^']*'\\s*\\)\\s*;/"] );
    }
}

foreach ( $replacements as $pattern => $replacement ) {
    $contents = preg_replace( $pattern, $replacement, $contents, 1, $count );
    if ( !$count ) {
        fwrite( STDERR, "Pattern not matched: $pattern\n" );
        exit( 1 );
    }
}

if ( file_put_contents( $config, $contents ) === false ) {
    fwrite( STDERR, "Cannot write: $config\n" );
    exit( 1 );
}

echo "OK: K_ADMIN_LANG=RU, K_ADMIN_THEME=garden in $config\n";
