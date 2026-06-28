<?php
$path = dirname(__DIR__) . '/couch/theme/garden/login.html';
$content = file_get_contents($path);
$fixed = preg_replace('/^cms:capture/', '<cms:capture', $content, 1);
if ($fixed === $content) {
    echo "login.html: no change needed\n";
    exit(0);
}
file_put_contents($path, $fixed);
echo "login.html: fixed opening capture tag\n";
