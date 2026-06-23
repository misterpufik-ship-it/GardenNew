<?php
$path = dirname( __DIR__ ) . '/couch/addons/kfunctions.php';
$content = file_get_contents( $path );

$content = preg_replace(
    "/function garden_alter_admin_menuitems\\( &\\$items \\)\\{.*?(?=\\nfunction garden_)/s",
    <<<'PHP'
function garden_alter_admin_menuitems( &$items ){
    if ( isset($items['site-home.php']) ){
        unset($items['site-home.php']);
    }

    $defaults = garden_admin_label_defaults();
    $overrides = garden_admin_label_overrides();

    $items['_garden_home_'] = garden_admin_menu_header( '_garden_home_', 'Главная', -1 );
    $items['_garden_admiral_'] = garden_admin_menu_header( '_garden_admiral_', 'Адмиралтейская', 0 );
    $items['_garden_udelnaya_'] = garden_admin_menu_header( '_garden_udelnaya_', 'Удельная', 1 );

    if ( isset($items['_templates_']) ){
        $items['_templates_']['title'] = 'Общие';
        $items['_templates_']['weight'] = 2;
        $items['_templates_']['class'] = 'separator';
    }

    foreach ( $defaults as $name=>$info ){
        if ( isset($items[$name]) ){
            $field = $info['field'];
            $items[$name]['title'] = ( $field && isset($overrides[$field]) ) ? $overrides[$field] : $info['title'];
            $items[$name]['weight'] = $info['weight'];
            if ( strpos($name, 'udelnaya/') === 0 ){
                $items[$name]['parent'] = '_garden_udelnaya_';
            }
            elseif ( $name === 'home.php' ){
                $items[$name]['parent'] = '_garden_home_';
            }
            elseif ( $name === 'admin-labels.php' || $name === 'booking-settings.php' ){
                $items[$name]['parent'] = '_templates_';
            }
            else{
                $items[$name]['parent'] = '_garden_admiral_';
            }
        }
    }
}


PHP
    ,
    $content,
    1,
    $count
);

if ( !$count ) {
    fwrite( STDERR, "garden_alter_admin_menuitems not replaced\n" );
    exit( 1 );
}

file_put_contents( $path, $content );
echo "Patched menu headers in $path\n";
