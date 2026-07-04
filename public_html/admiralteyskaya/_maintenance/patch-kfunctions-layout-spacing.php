<?php
/**
 * Add layout-spacing.php to kfunctions admin menu under "Общие".
 * Run on server: php public_html/admiralteyskaya/_maintenance/patch-kfunctions-layout-spacing.php
 */
$path = dirname(__DIR__) . '/couch/addons/kfunctions.php';
if (!is_file($path)) {
    fwrite(STDERR, "kfunctions.php not found: {$path}\n");
    exit(1);
}
$content = file_get_contents($path);

if (strpos($content, "'layout-spacing.php'") !== false) {
    echo "layout-spacing.php already present in kfunctions.php\n";
    exit(0);
}

$content = str_replace(
    "'preloader-settings.php' => array('field'=>'label_preloader_settings', 'title'=>'Прелоадер', 'weight'=>3),",
    "'layout-spacing.php' => array('field'=>'label_layout_spacing', 'title'=>'Отступы между блоками', 'weight'=>6),\n        'preloader-settings.php' => array('field'=>'label_preloader_settings', 'title'=>'Прелоадер', 'weight'=>3),",
    $content,
    $count1
);

$content = str_replace(
    "array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php' )",
    "array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php' )",
    $content,
    $count2
);

if (!$count1 || !$count2) {
    fwrite(STDERR, "kfunctions patch failed (labels={$count1}, menu={$count2})\n");
    exit(1);
}

file_put_contents($path, $content);
echo "Patched kfunctions.php for layout-spacing.php\n";
