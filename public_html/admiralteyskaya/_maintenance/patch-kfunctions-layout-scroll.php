<?php
/**
 * Add layout-scroll.php to kfunctions admin menu under "Общие".
 * Run on server: php public_html/admiralteyskaya/_maintenance/patch-kfunctions-layout-scroll.php
 */
$path = dirname(__DIR__) . '/couch/addons/kfunctions.php';
if (!is_file($path)) {
    fwrite(STDERR, "kfunctions.php not found: {$path}\n");
    exit(1);
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
    fwrite(STDERR, "kfunctions patch failed (labels={$count1}, menu={$count2})\n");
    exit(1);
}

file_put_contents($path, $content);
echo "Patched kfunctions.php for layout-scroll.php\n";
