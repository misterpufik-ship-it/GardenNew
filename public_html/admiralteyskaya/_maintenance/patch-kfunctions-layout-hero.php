<?php
/**
 * Add layout-hero.php (+ layout-spacing/scroll if missing) to kfunctions admin menu under "Общие".
 * Run on server: php public_html/admiralteyskaya/_maintenance/patch-kfunctions-layout-hero.php
 */
$path = dirname(__DIR__) . '/couch/addons/kfunctions.php';
if (!is_file($path)) {
    fwrite(STDERR, "kfunctions.php not found: {$path}\n");
    exit(1);
}
$content = file_get_contents($path);

$changed = false;

$layoutDefaults = array(
    'layout-spacing.php' => array('field' => 'label_layout_spacing', 'title' => 'Отступы между блоками', 'weight' => 6),
    'layout-scroll.php' => array('field' => 'label_layout_scroll', 'title' => 'Прокрутка по меню', 'weight' => 7),
    'layout-hero.php' => array('field' => 'label_layout_hero', 'title' => 'Hero — мобильный лого', 'weight' => 8),
);

foreach ($layoutDefaults as $name => $info) {
    if (strpos($content, "'{$name}'") !== false) {
        echo "{$name} already present\n";
        continue;
    }

    $insertAfter = "'preloader-settings.php' => array('field'=>'label_preloader_settings', 'title'=>'Прелоадер', 'weight'=>3),";
    $insertLine = "'{$name}' => array('field'=>'{$info['field']}', 'title'=>'{$info['title']}', 'weight'=>{$info['weight']}),\n        {$insertAfter}";
    if (strpos($content, $insertAfter) === false) {
        fwrite(STDERR, "Insert anchor not found for {$name}\n");
        continue;
    }
    $content = str_replace($insertAfter, $insertLine, $content, $count);
    if ($count) {
        $changed = true;
        echo "Added {$name} to garden_admin_label_defaults()\n";
    }
}

$oldParents = "elseif ( in_array( \$name, array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php' ), true ) ){";
$newParents = "elseif ( in_array( \$name, array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php', 'layout-spacing.php', 'layout-scroll.php', 'layout-hero.php' ), true ) ){";

if (strpos($content, "'layout-hero.php'") !== false && strpos($content, "'layout-spacing.php', 'layout-scroll.php', 'layout-hero.php'") === false) {
    $content = str_replace($oldParents, $newParents, $content, $count);
    if ($count) {
        $changed = true;
        echo "Updated Общие parent list for layout templates\n";
    }
}

if (!$changed) {
    echo "No changes needed\n";
    exit(0);
}

file_put_contents($path, $content);
echo "Patched kfunctions.php\n";
