<?php
$path = dirname(__DIR__) . '/couch/theme/garden/content_form.html';
if (!is_file($path)) {
    copy(dirname(__DIR__) . '/couch/theme/_system/content_form.html', $path);
}
$content = file_get_contents($path);

$content = preg_replace(
    '/\s*<!-- advance settings dropdown -->\s*<cms:render \'group_advanced_settings\' \/>\s*/',
    "\n",
    $content,
    1,
    $countRemoved
);

if (!$countRemoved && strpos($content, "group_advanced_settings") !== false && strpos($content, 'ctrl-bot') !== false) {
    echo "Advanced settings already moved or pattern missing\n";
    exit(strpos($content, "group_advanced_settings") < strpos($content, 'ctrl-bot') ? 0 : 1);
}

$content = preg_replace(
    '/(\s*<div class="ctrl-bot">)/',
    "$1\n            <cms:render 'group_advanced_settings' />\n",
    $content,
    1,
    $countAdded
);

if (!$countAdded) {
    fwrite(STDERR, "Failed to insert advanced settings before ctrl-bot\n");
    exit(1);
}

file_put_contents($path, $content);
echo "Moved advanced settings above ctrl-bot in garden/content_form.html\n";
