<?php
$dir = dirname(__DIR__) . '/admiralteyskaya/couch/uploads/image';
$only = array();
foreach ( $argv as $arg ) {
    if ( preg_match('/\.(jpg|jpeg|png)$/i', $arg) ) {
        $only[] = basename($arg);
    }
}

$files = $only
    ? array_map(function ($name) use ($dir) { return $dir . '/' . $name; }, $only)
    : glob($dir . '/*.{jpg,jpeg,png}', GLOB_BRACE);

foreach ( $files as $source ) {
    if ( !is_file($source) ) {
        fwrite(STDERR, "missing: " . basename($source) . "\n");
        continue;
    }

    $img = null;
    $ext = strtolower(pathinfo($source, PATHINFO_EXTENSION));
    if ( $ext === 'jpg' || $ext === 'jpeg' ) {
        $img = @imagecreatefromjpeg($source);
    } elseif ( $ext === 'png' ) {
        $img = @imagecreatefrompng($source);
    }

    if ( !$img ) {
        fwrite(STDERR, "skip: " . basename($source) . "\n");
        continue;
    }

    imagepalettetotruecolor($img);
    imagealphablending($img, true);
    imagesavealpha($img, true);

    $webp = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $source);
    imagewebp($img, $webp, 98);
    imagedestroy($img);

    echo basename($source) . ' -> ' . basename($webp) . ' (' . filesize($webp) . " bytes)\n";
}
