<?php
$path = dirname( __DIR__ ) . '/couch/addons/kfunctions.php';
$src = dirname( __DIR__ ) . '/_maintenance/kfunctions-eff576a.php';
if ( !file_exists( $src ) ) {
    $src = $path;
}
$content = file_get_contents( $src );

$labels = <<<'PHP'
function garden_admin_label_defaults(){
    return array(
        'header.php' => array('field'=>'label_header', 'title'=>'Шапка сайта (Header)', 'weight'=>100),
        'about.php' => array('field'=>'label_about', 'title'=>'Концепция', 'weight'=>110),
        'akzii.php' => array('field'=>'label_akzii', 'title'=>'Акции', 'weight'=>120),
        'menu.php' => array('field'=>'label_menu', 'title'=>'Меню (общие настройки)', 'weight'=>130),
        'menu/text/index.php' => array('field'=>'label_menu_text', 'title'=>'Меню RU', 'weight'=>140),
        'menu/english/index.php' => array('field'=>'label_menu_en', 'title'=>'Меню EN', 'weight'=>150),
        'menu/visual/index.php' => array('field'=>'label_menu_visual', 'title'=>'Меню визуальное', 'weight'=>160),
        'sticky-sticker.php' => array('field'=>'label_sticky_sticker', 'title'=>'Липкий стикер — Адмиралтейская', 'weight'=>165),
        'gallery.php' => array('field'=>'label_gallery', 'title'=>'Галерея', 'weight'=>170),
        'reservation.php' => array('field'=>'label_reservation', 'title'=>'Бронирование', 'weight'=>180),
        'contacts.php' => array('field'=>'label_contacts', 'title'=>'Контакты', 'weight'=>190),
        'filial.php' => array('field'=>'label_filial', 'title'=>'Филиал', 'weight'=>200),
        'globals.php' => array('field'=>'label_globals', 'title'=>'Футер и SEO', 'weight'=>210),
        'index.php' => array('field'=>'label_index', 'title'=>'Общая страница', 'weight'=>220),
        'home.php' => array('field'=>'label_home', 'title'=>'Главная', 'weight'=>1),
        'booking-settings.php' => array('field'=>'label_booking_settings', 'title'=>'Бронирование Telegram', 'weight'=>5),
        'admin-labels.php' => array('field'=>'', 'title'=>'Названия разделов', 'weight'=>230),

        'udelnaya/header.php' => array('field'=>'label_u_header', 'title'=>'Шапка сайта (Header)', 'weight'=>100),
        'udelnaya/about.php' => array('field'=>'label_u_about', 'title'=>'Концепция', 'weight'=>110),
        'udelnaya/akzii.php' => array('field'=>'label_u_akzii', 'title'=>'Акции', 'weight'=>120),
        'udelnaya/menu.php' => array('field'=>'label_u_menu', 'title'=>'Меню (общие настройки)', 'weight'=>130),
        'udelnaya/menu/text/index.php' => array('field'=>'label_u_menu_text', 'title'=>'Меню RU', 'weight'=>140),
        'udelnaya/menu/english/index.php' => array('field'=>'label_u_menu_en', 'title'=>'Меню EN', 'weight'=>150),
        'udelnaya/menu/visual/index.php' => array('field'=>'label_u_menu_visual', 'title'=>'Меню визуальное', 'weight'=>160),
        'udelnaya/sticky-sticker.php' => array('field'=>'label_u_sticky_sticker', 'title'=>'Липкий стикер — Удельная', 'weight'=>165),
        'udelnaya/gallery.php' => array('field'=>'label_u_gallery', 'title'=>'Галерея', 'weight'=>170),
        'udelnaya/reservation.php' => array('field'=>'label_u_reservation', 'title'=>'Бронирование', 'weight'=>180),
        'udelnaya/contacts.php' => array('field'=>'label_u_contacts', 'title'=>'Контакты', 'weight'=>190),
        'udelnaya/filial.php' => array('field'=>'label_u_filial', 'title'=>'Филиал', 'weight'=>200),
        'udelnaya/globals.php' => array('field'=>'label_u_globals', 'title'=>'Футер и SEO', 'weight'=>210),
        'udelnaya/index.php' => array('field'=>'label_u_index', 'title'=>'Общая страница', 'weight'=>220),
    );
}
PHP;

$menu = <<<'PHP'
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
PHP;

$content = preg_replace( '/function garden_admin_label_defaults\(\)\{.*?\n\}/s', trim( $labels ), $content, 1, $c1 );
$content = preg_replace( '/function garden_alter_admin_menuitems\( &\$items \)\{.*?\n\}/s', trim( $menu ), $content, 1, $c2 );

if ( !$c1 || !$c2 ) {
    fwrite( STDERR, "Replace failed: labels=$c1 menu=$c2\n" );
    exit( 1 );
}

// Cache-bust theme CSS in admin HTML
$branding = <<<'PHP'
function garden_admin_branding_output( &$html ){
    $html = preg_replace( '#<title>[^<]*</title>#', '<title>Garden Lounge</title>', $html, 1 );
    $html = preg_replace(
        '#<link href="[^"]*favicon\.ico" rel="shortcut icon"/>#',
        '<link rel="icon" type="image/png" href="/favicon.png">' . "\n    " . '<link rel="shortcut icon" type="image/png" href="/favicon.png">',
        $html,
        1
    );
    if ( defined( 'K_THEME_URL' ) && defined( 'K_THEME_DIR' ) && K_THEME_URL && is_file( K_THEME_DIR . 'styles.css' ) ) {
        $ver = filemtime( K_THEME_DIR . 'styles.css' );
        $html = str_replace( K_THEME_URL . 'styles.css', K_THEME_URL . 'styles.css?v=' . $ver, $html );
    }
}
PHP;

$content = preg_replace( '/function garden_admin_branding_output\( &\$html \)\{.*?\n\}/s', trim( $branding ), $content, 1, $c3 );

file_put_contents( $path, $content );
echo "kfunctions.php repaired ($c1/$c2/$c3)\n";
