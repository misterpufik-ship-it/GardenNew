<?php
/**
 * Add layout-mobile-menu.php to kfunctions admin menu under "Общие".
 * Run on server: php public_html/admiralteyskaya/_maintenance/patch-kfunctions-layout-mobile-menu.php
 */
$path = dirname(__DIR__) . '/couch/addons/kfunctions.php';
if (!is_file($path)) {
    fwrite(STDERR, "kfunctions.php not found: {$path}\n");
    exit(1);
}
$content = file_get_contents($path);

$changed = false;

$name = 'layout-mobile-menu.php';
$info = array('field' => 'label_layout_mobile_menu', 'title' => 'Гамбургер-меню', 'weight' => 9);

if (strpos($content, "'{$name}'") !== false) {
    echo "{$name} already present\n";
} else {
    $insertAfter = "'layout-hero.php' => array('field'=>'label_layout_hero', 'title'=>'Hero — мобильный лого', 'weight'=>8),";
    if (strpos($content, $insertAfter) === false) {
        $insertAfter = "'preloader-settings.php' => array('field'=>'label_preloader_settings', 'title'=>'Прелоадер', 'weight'=>3),";
    }
    $insertLine = "'{$name}' => array('field'=>'{$info['field']}', 'title'=>'{$info['title']}', 'weight'=>{$info['weight']}),\n        {$insertAfter}";
    if (strpos($content, $insertAfter) === false) {
        fwrite(STDERR, "Insert anchor not found for {$name}\n");
        exit(1);
    }
    $content = str_replace($insertAfter, $insertLine, $content, $count);
    if ($count) {
        $changed = true;
        echo "Added {$name} to garden_admin_label_defaults()\n";
    }
}

$parentVariants = array(
    "elseif ( in_array( \$name, array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php', 'layout-scroll.php', 'layout-hero.php' ), true ) ){" =>
        "elseif ( in_array( \$name, array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php', 'layout-scroll.php', 'layout-hero.php', 'layout-mobile-menu.php' ), true ) ){",
    "elseif ( in_array( \$name, array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php' ), true ) ){" =>
        "elseif ( in_array( \$name, array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php', 'layout-scroll.php', 'layout-hero.php', 'layout-mobile-menu.php' ), true ) ){",
);

foreach ($parentVariants as $oldParents => $newParents) {
    if (strpos($content, "'layout-mobile-menu.php'") !== false && strpos($content, "'layout-hero.php', 'layout-mobile-menu.php'") === false && strpos($content, $oldParents) !== false) {
        $content = str_replace($oldParents, $newParents, $content, $count);
        if ($count) {
            $changed = true;
            echo "Updated Общие parent list for layout templates\n";
            break;
        }
    }
}

if (!$changed) {
    echo "No changes needed\n";
    exit(0);
}

file_put_contents($path, $content);
echo "Patched kfunctions.php\n";
