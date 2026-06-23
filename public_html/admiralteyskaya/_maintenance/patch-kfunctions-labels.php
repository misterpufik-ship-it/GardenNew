<?php
if ( php_sapi_name() !== 'cli' ) die( 'CLI only' );

$path = dirname( __DIR__ ) . '/couch/addons/kfunctions.php';
$content = file_get_contents( $path );
if ( $content === false ) die( "Cannot read $path\n" );

$new_fn = <<<'PHP'
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

$content = preg_replace(
    '/function garden_admin_label_defaults\(\)\{.*?\n\}/s',
    trim( $new_fn ),
    $content,
    1,
    $count
);

if ( !$count ) {
    fwrite( STDERR, "garden_admin_label_defaults not replaced\n" );
    exit( 1 );
}

// Fix section headers in garden_alter_admin_menuitems if garbled
$headers = array(
    "'_garden_home_', 'Главная'" => "'_garden_home_', 'Главная'",
    "'_garden_admiral_', 'Адмиралтейская'" => "'_garden_admiral_', 'Адмиралтейская'",
    "'_garden_udelnaya_', 'Удельная'" => "'_garden_udelnaya_', 'Удельная'",
);
$content = preg_replace(
    "/\\$items\\['_garden_home_'\\] = garden_admin_menu_header\\( '_garden_home_', '[^']*', -1 \\);/",
    "\$items['_garden_home_'] = garden_admin_menu_header( '_garden_home_', 'Главная', -1 );",
    $content
);
$content = preg_replace(
    "/\\$items\\['_garden_admiral_'\\] = garden_admin_menu_header\\( '_garden_admiral_', '[^']*', 0 \\);/",
    "\$items['_garden_admiral_'] = garden_admin_menu_header( '_garden_admiral_', 'Адмиралтейская', 0 );",
    $content
);
$content = preg_replace(
    "/\\$items\\['_garden_udelnaya_'\\] = garden_admin_menu_header\\( '_garden_udelnaya_', '[^']*', 1 \\);/",
    "\$items['_garden_udelnaya_'] = garden_admin_menu_header( '_garden_udelnaya_', 'Удельная', 1 );",
    $content
);
$content = preg_replace(
    "/\\$items\\['_templates_'\\]\\['title'\\] = '[^']*';/",
    "\$items['_templates_']['title'] = 'Общие';",
    $content
);

file_put_contents( $path, $content );
echo "Patched: $path\n";
