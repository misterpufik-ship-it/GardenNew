<?php
/**
 * Add layout-hero.php to kfunctions admin menu under "Общие".
 * Run on server: php public_html/admiralteyskaya/_maintenance/patch-kfunctions-layout-hero.php
 */
$path = dirname(__DIR__) . '/couch/addons/kfunctions.php';
if (!is_file($path)) {
    fwrite(STDERR, "kfunctions.php not found: {$path}\n");
    exit(1);
}
$content = file_get_contents($path);

if (strpos($content, "'layout-hero.php'") !== false) {
    echo "layout-hero.php already present in kfunctions.php\n";
    exit(0);
}

$changed = false;

if (strpos($content, "'layout-scroll.php'") !== false) {
    $content = str_replace(
        "'layout-scroll.php' => array('field'=>'label_layout_scroll', 'title'=>'Прокрутка по меню', 'weight'=>7),",
        "'layout-hero.php' => array('field'=>'label_layout_hero', 'title'=>'Hero — мобильный лого', 'weight'=>8),\n        'layout-scroll.php' => array('field'=>'label_layout_scroll', 'title'=>'Прокрутка по меню', 'weight'=>7),",
        $content,
        $count
    );
    $changed = $changed || $count;
}

$menuPatterns = array(
    "array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php', 'layout-scroll.php' )" =>
        "array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php', 'layout-scroll.php', 'layout-hero.php' )",
    "array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php' )" =>
        "array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php', 'layout-scroll.php', 'layout-hero.php' )",
);

foreach ($menuPatterns as $from => $to) {
    if (strpos($content, "'layout-hero.php'") !== false) {
        break;
    }
    $content = str_replace($from, $to, $content, $count);
    if ($count) {
        $changed = true;
        break;
    }
}

if (strpos($content, "'layout-hero.php'") !== false && strpos($content, "layout-hero.php' )") === false) {
    $content = str_replace(
        "in_array( \$name, array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php', 'layout-scroll.php' ), true )",
        "in_array( \$name, array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php', 'layout-scroll.php', 'layout-hero.php' ), true )",
        $content,
        $count
    );
    $changed = $changed || $count;
}

if (!$changed && strpos($content, "'layout-hero.php'") === false) {
    fwrite(STDERR, "kfunctions patch failed: no matching patterns\n");
    exit(1);
}

file_put_contents($path, $content);
echo "Patched kfunctions.php for layout-hero.php\n";
