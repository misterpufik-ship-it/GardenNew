<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

$root = '/home/m/mrpuffch/garden-lounge.pro/public_html';
$targets = [
    [
        'dir' => $root . '/admiralteyskaya',
        'script' => 'sticky-sticker.php',
        'uri' => '/admiralteyskaya/sticky-sticker.php',
    ],
    [
        'dir' => $root . '/udelnaya',
        'script' => 'sticky-sticker.php',
        'uri' => '/udelnaya/sticky-sticker.php',
    ],
];

foreach ($targets as $target) {
    chdir($target['dir']);
    $_SERVER['HTTP_HOST'] = 'garden-lounge.pro';
    $_SERVER['REQUEST_URI'] = $target['uri'];
    $_SERVER['SCRIPT_NAME'] = $target['uri'];
    $_SERVER['SCRIPT_FILENAME'] = $target['dir'] . '/' . $target['script'];
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

    require $target['script'];
    echo "Registered: {$target['uri']}\n";
}
