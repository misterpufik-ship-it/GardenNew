<?php
/**
 * Add layout-scroll.php (and layout-spacing.php if missing) to kfunctions admin menu under "Общие".
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

$changed = false;

if (strpos($content, "'layout-spacing.php'") !== false) {
    $content = str_replace(
        "'layout-spacing.php' => array('field'=>'label_layout_spacing', 'title'=>'Отступы между блоками', 'weight'=>6),",
        "'layout-scroll.php' => array('field'=>'label_layout_scroll', 'title'=>'Прокрутка по меню', 'weight'=>7),\n        'layout-spacing.php' => array('field'=>'label_layout_spacing', 'title'=>'Отступы между блоками', 'weight'=>6),",
        $content,
        $count
    );
    $changed = $changed || $count;
} else {
    $content = str_replace(
        "'preloader-settings.php' => array('field'=>'label_preloader_settings', 'title'=>'Прелоадер', 'weight'=>3),",
        "'layout-scroll.php' => array('field'=>'label_layout_scroll', 'title'=>'Прокрутка по меню', 'weight'=>7),\n        'layout-spacing.php' => array('field'=>'label_layout_spacing', 'title'=>'Отступы между блоками', 'weight'=>6),\n        'preloader-settings.php' => array('field'=>'label_preloader_settings', 'title'=>'Прелоадер', 'weight'=>3),",
        $content,
        $count
    );
    $changed = $changed || $count;
}

$menuPatterns = array(
    "array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php' )" =>
        "array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php', 'layout-scroll.php' )",
    "array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php' )" =>
        "array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php', 'layout-scroll.php' )",
    "elseif ( \$name === 'admin-labels.php' || \$name === 'booking-settings.php' )" =>
        "elseif ( in_array( \$name, array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php', 'layout-scroll.php' ), true ) )",
);

foreach ($menuPatterns as $from => $to) {
    if (strpos($content, "'layout-scroll.php' )") !== false || strpos($content, "'layout-scroll.php'") !== false && strpos($content, $to) !== false) {
        continue;
    }
    $content = str_replace($from, $to, $content, $count);
    if ($count) {
        $changed = true;
        break;
    }
}

if (!$changed) {
    fwrite(STDERR, "kfunctions patch failed: no matching patterns\n");
    exit(1);
}

file_put_contents($path, $content);
echo "Patched kfunctions.php for layout-scroll.php\n";
