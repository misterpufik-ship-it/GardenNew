<?php
/**
 * Add layout-* templates to admin labels and "Общие" menu group.
 */
if (php_sapi_name() !== 'cli') {
    die("CLI only\n");
}

$path = dirname(__DIR__) . '/couch/addons/kfunctions.php';
$content = file_get_contents($path);
if ($content === false) {
    die("Cannot read kfunctions.php\n");
}

$entries = array(
    "'layout-spacing.php' => array('field'=>'label_layout_spacing', 'title'=>'Отступы между блоками', 'weight'=>6),",
    "'layout-scroll.php' => array('field'=>'label_layout_scroll', 'title'=>'Прокрутка по меню', 'weight'=>7),",
    "'layout-hero.php' => array('field'=>'label_layout_hero', 'title'=>'Hero — мобильный лого', 'weight'=>8),",
    "'layout-mobile-menu.php' => array('field'=>'label_layout_mobile_menu', 'title'=>'Гамбургер-меню', 'weight'=>9),",
);

foreach ($entries as $line) {
    $key = preg_match("/^'([^']+)'/", $line, $m) ? $m[1] : '';
    if ($key && strpos($content, "'{$key}'") !== false) {
        continue;
    }
    $anchor = "'preloader-settings.php' => array('field'=>'label_preloader_settings', 'title'=>'Прелоадер', 'weight'=>3),";
    if (strpos($content, $anchor) === false) {
        $anchor = "'age-gate-settings.php' => array('field'=>'label_age_gate_settings', 'title'=>'Заглушка 18+', 'weight'=>4),";
    }
    if (strpos($content, $anchor) === false) {
        die("Anchor not found for {$key}\n");
    }
    $content = str_replace($anchor, $line . "\n        " . $anchor, $content);
    echo "Added {$key} to label defaults\n";
}

$oldLists = array(
    "array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php' )",
    "array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php' )",
    "array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php', 'layout-scroll.php' )",
);

$newList = "array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php', 'layout-scroll.php', 'layout-hero.php', 'layout-mobile-menu.php' )";

foreach ($oldLists as $old) {
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $newList, $content);
        echo "Updated _templates_ parent list\n";
        break;
    }
}

if (file_put_contents($path, $content) === false) {
    die("Write failed\n");
}
echo "Patched kfunctions.php\n";
