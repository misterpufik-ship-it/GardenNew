<?php
/**
 * Remove globe/menu icons from admin sidebar items.
 * Web: /admiralteyskaya/_maintenance/patch-admin-menu-icons-web.php?token=gl-cache-clear-20260623
 */
$root = dirname(__DIR__);
$path = $root . '/couch/addons/kfunctions.php';
if (!is_file($path)) {
    fwrite(STDERR, "kfunctions.php not found\n");
    exit(1);
}

$stripBlock = <<<'PHP'
    $stripMenuIcons = array(
        'menu.php',
        'menu/text/index.php',
        'menu/english/index.php',
        'menu/visual/index.php',
        'udelnaya/menu.php',
        'udelnaya/menu/text/index.php',
        'udelnaya/menu/english/index.php',
        'udelnaya/menu/visual/index.php',
    );
    foreach ( $stripMenuIcons as $tplName ) {
        if ( isset($items[$tplName]) ) {
            unset($items[$tplName]['icon']);
            $items[$tplName]['icon'] = '';
        }
    }
PHP;

$content = file_get_contents($path);

if (strpos($content, '$stripMenuIcons') !== false) {
    echo "Menu icon strip already present\n";
} else {
    $content = preg_replace(
        '/(\s*foreach \( \$defaults as \$name=>\$info \)\{.*?\n\s*\}\n)(\})/s',
        '$1' . $stripBlock . "\n$2",
        $content,
        1,
        $count
    );
    if (!$count) {
        fwrite(STDERR, "Could not inject stripMenuIcons into garden_alter_admin_menuitems\n");
        exit(1);
    }
    file_put_contents($path, $content);
    echo "Injected menu icon strip into garden_alter_admin_menuitems\n";
}

passthru('php -l ' . escapeshellarg($path), $code);
exit($code);
