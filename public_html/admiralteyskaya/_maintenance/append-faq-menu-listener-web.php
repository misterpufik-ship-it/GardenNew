<?php
/**
 * Append FAQ menu fix listener to kfunctions.php (runs after branch rules).
 * https://garden-lounge.pro/admiralteyskaya/_maintenance/append-faq-menu-listener-web.php?token=gl-cache-clear-20260623
 */
$token = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($token !== 'gl-cache-clear-20260623') {
    http_response_code(403);
    exit("Forbidden\n");
}

header('Content-Type: text/plain; charset=utf-8');

$path = dirname(__DIR__) . '/couch/addons/kfunctions.php';
if (!is_file($path)) {
    exit("kfunctions.php not found\n");
}

$content = file_get_contents($path);
if (strpos($content, 'function garden_faq_admin_menu_fix') !== false) {
    echo "FAQ menu listener already installed\n";
    exit(0);
}

$block = <<<'PHP'

function garden_faq_admin_menu_fix( &$items ){
    if ( !isset($items['_garden_faq_']) ) {
        $items['_garden_faq_'] = garden_admin_menu_header( '_garden_faq_', 'Вопросы и ответы', 25 );
    }

    $faqItems = array(
        'faq.php' => array( 'title' => 'Адмирал', 'weight' => 26 ),
        'udelnaya/faq.php' => array( 'title' => 'Уделка', 'weight' => 27 ),
    );

    foreach ( $faqItems as $name => $info ) {
        if ( !isset($items[$name]) ) {
            continue;
        }
        $items[$name]['title'] = $info['title'];
        $items[$name]['weight'] = $info['weight'];
        $items[$name]['parent'] = '_garden_faq_';
    }
}

$FUNCS->add_event_listener( 'alter_admin_menuitems', 'garden_faq_admin_menu_fix' );
PHP;

$marker = "\$FUNCS->add_event_listener( 'alter_admin_menuitems', 'garden_alter_admin_menuitems' );";
if (strpos($content, $marker) === false) {
    exit("Could not find alter_admin_menuitems listener marker\n");
}

$content = str_replace($marker, $marker . $block, $content);
file_put_contents($path, $content);

$cacheDir = dirname(__DIR__) . '/couch/cache';
if (is_dir($cacheDir)) {
    foreach (glob($cacheDir . '/*') as $file) {
        if (is_file($file) && basename($file) !== '.htaccess') {
            @unlink($file);
        }
    }
}

echo "Installed garden_faq_admin_menu_fix listener\n";
