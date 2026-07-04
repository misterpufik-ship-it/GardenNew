<?php
/**
 * Web runner for patch-kfunctions-layout-scroll.php
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/patch-kfunctions-layout-scroll-web.php?key=<md5>
 * key = md5('garden-lounge-patch-kfunctions-layout-scroll')
 */
$expectedKey = md5('garden-lounge-patch-kfunctions-layout-scroll');
if ((isset($_GET['key']) ? $_GET['key'] : '') !== $expectedKey) {
    http_response_code(403);
    exit("Forbidden\n");
}
header('Content-Type: text/plain; charset=utf-8');

$path = dirname(__DIR__) . '/couch/addons/kfunctions.php';
if (!is_file($path)) {
    exit("kfunctions.php not found: {$path}\n");
}
$content = file_get_contents($path);

if (strpos($content, "'layout-scroll.php'") !== false) {
    echo "layout-scroll.php already present in kfunctions.php\n";
    exit(0);
}

$content = str_replace(
    "'layout-spacing.php' => array('field'=>'label_layout_spacing', 'title'=>'Отступы между блоками', 'weight'=>6),",
    "'layout-scroll.php' => array('field'=>'label_layout_scroll', 'title'=>'Прокрутка по меню', 'weight'=>7),\n        'layout-spacing.php' => array('field'=>'label_layout_spacing', 'title'=>'Отступы между блоками', 'weight'=>6),",
    $content,
    $count1
);

$content = str_replace(
    "array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php' )",
    "array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php', 'layout-scroll.php' )",
    $content,
    $count2
);

if (!$count1 || !$count2) {
    exit("kfunctions patch failed (labels={$count1}, menu={$count2})\n");
}

file_put_contents($path, $content);
echo "Patched kfunctions.php for layout-scroll.php\n";
