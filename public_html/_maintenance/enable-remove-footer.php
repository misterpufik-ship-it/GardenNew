<?php
$config = __DIR__ . '/../admiralteyskaya/couch/config.php';
if ( !file_exists($config) ) {
    fwrite(STDERR, "config.php not found\n");
    exit(1);
}

$contents = file_get_contents($config);
$updated = preg_replace(
    "/define\\(\\s*'K_REMOVE_FOOTER_LINK'\\s*,\\s*0\\s*\\)/",
    "define( 'K_REMOVE_FOOTER_LINK', 1 )",
    $contents,
    1,
    $count
);

if ( !$count ) {
    echo "K_REMOVE_FOOTER_LINK already enabled or not found\n";
    exit(0);
}

file_put_contents($config, $updated);
echo "K_REMOVE_FOOTER_LINK enabled\n";

$cacheDir = __DIR__ . '/../admiralteyskaya/couch/cache';
foreach ( glob($cacheDir . '/*.dat') as $file ) {
    @unlink($file);
}
echo "Cache cleared\n";
