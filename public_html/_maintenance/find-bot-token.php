<?php
$roots = array(
    '/home/m/mrpuffch/garden-lounge.pro',
    '/home/m/mrpuffch/misterpufik.ru',
);
$it = new RegexIterator(
    new RecursiveIteratorIterator(new RecursiveDirectoryIterator('/home/m/mrpuffch/garden-lounge.pro', FilesystemIterator::SKIP_DOTS)),
    '/8:[0-9A-Za-z_-]{20,}/'
);
foreach ($it as $file) {
    if (!preg_match('/\.(php|js|env|json|toml)$/i', $file->getPathname())) continue;
    $content = @file_get_contents($file->getPathname());
    if ($content && preg_match('/8:[0-9A-Za-z_-]{20,}/', $content, $m)) {
        echo $file->getPathname() . ' => ' . substr($m[0], 0, 14) . "...\n";
    }
}
