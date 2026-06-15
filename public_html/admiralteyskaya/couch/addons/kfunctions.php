<?php
if ( !defined('K_COUCH_DIR') ) die();

function garden_admin_label_defaults(){
    return array(
        'index.php' => array('field'=>'label_index', 'title'=>'А. Общая страница', 'weight'=>100),
        'akzii.php' => array('field'=>'label_akzii', 'title'=>'А. Акции', 'weight'=>110),
        'reservation.php' => array('field'=>'label_reservation', 'title'=>'А. Бронирование', 'weight'=>120),
        'gallery.php' => array('field'=>'label_gallery', 'title'=>'А. Галерея', 'weight'=>130),
        'contacts.php' => array('field'=>'label_contacts', 'title'=>'А. Контакты', 'weight'=>140),
        'menu.php' => array('field'=>'label_menu', 'title'=>'А. Меню', 'weight'=>150),
        'menu/english/index.php' => array('field'=>'label_menu_en', 'title'=>'А. Меню EN', 'weight'=>160),
        'menu/text/index.php' => array('field'=>'label_menu_text', 'title'=>'А. Меню текст', 'weight'=>170),
        'menu/visual/index.php' => array('field'=>'label_menu_visual', 'title'=>'А. Меню визуальное', 'weight'=>180),
        'globals.php' => array('field'=>'label_globals', 'title'=>'А. Настройки сайта (Футер и SEO)', 'weight'=>190),
        'filial.php' => array('field'=>'label_filial', 'title'=>'А. Филиал', 'weight'=>200),
        'about.php' => array('field'=>'label_about', 'title'=>'А. Философия', 'weight'=>210),
        'header.php' => array('field'=>'label_header', 'title'=>'А. Шапка сайта (Header)', 'weight'=>220),
        'admin-labels.php' => array('field'=>'', 'title'=>'А. Названия разделов', 'weight'=>230),

        'udelnaya/index.php' => array('field'=>'label_u_index', 'title'=>'У. Общая страница', 'weight'=>300),
        'udelnaya/akzii.php' => array('field'=>'label_u_akzii', 'title'=>'У. Акции', 'weight'=>310),
        'udelnaya/reservation.php' => array('field'=>'label_u_reservation', 'title'=>'У. Бронирование', 'weight'=>320),
        'udelnaya/gallery.php' => array('field'=>'label_u_gallery', 'title'=>'У. Галерея', 'weight'=>330),
        'udelnaya/contacts.php' => array('field'=>'label_u_contacts', 'title'=>'У. Контакты', 'weight'=>340),
        'udelnaya/menu.php' => array('field'=>'label_u_menu', 'title'=>'У. Меню', 'weight'=>350),
        'udelnaya/menu/english/index.php' => array('field'=>'label_u_menu_en', 'title'=>'У. Меню EN', 'weight'=>360),
        'udelnaya/menu/text/index.php' => array('field'=>'label_u_menu_text', 'title'=>'У. Меню текст', 'weight'=>370),
        'udelnaya/menu/visual/index.php' => array('field'=>'label_u_menu_visual', 'title'=>'У. Меню визуальное', 'weight'=>380),
        'udelnaya/globals.php' => array('field'=>'label_u_globals', 'title'=>'У. Настройки сайта (Футер и SEO)', 'weight'=>390),
        'udelnaya/filial.php' => array('field'=>'label_u_filial', 'title'=>'У. Филиал', 'weight'=>400),
        'udelnaya/about.php' => array('field'=>'label_u_about', 'title'=>'У. Философия', 'weight'=>410),
        'udelnaya/header.php' => array('field'=>'label_u_header', 'title'=>'У. Шапка сайта (Header)', 'weight'=>420),
    );
}

function garden_admin_label_overrides(){
    global $DB;

    static $overrides = null;
    if ( !is_null($overrides) ) return $overrides;

    $overrides = array();
    $sql =
        "SELECT f.name, dt.value " .
        "FROM " . K_TBL_TEMPLATES . " t " .
        "INNER JOIN " . K_TBL_PAGES . " p ON p.template_id=t.id AND p.is_master='1' " .
        "INNER JOIN " . K_TBL_FIELDS . " f ON f.template_id=t.id " .
        "INNER JOIN " . K_TBL_DATA_TEXT . " dt ON dt.page_id=p.id AND dt.field_id=f.id " .
        "WHERE t.name='admin-labels.php'";

    $rs = $DB->raw_select($sql);
    if ( is_array($rs) ){
        foreach ( $rs as $row ){
            $value = trim($row['value']);
            if ( strlen($value) ){
                $overrides[$row['name']] = $value;
            }
        }
    }

    return $overrides;
}

function garden_admin_menu_header( $name, $title, $weight ){
    return array(
        'id'=>$weight + 10000,
        'name'=>$name,
        'title'=>$title,
        'desc'=>'',
        'parent'=>'',
        'is_header'=>1,
        'icon'=>null,
        'show_in_menu'=>1,
        'weight'=>$weight,
        'href'=>'',
        'access_callback'=>null,
        'access_callback_params'=>null,
        'is_current_callback'=>null,
        'route'=>array(),
        'type'=>'menu',
        'class'=>'separator',
        'target'=>'',
        'confirmation_msg'=>'',
        'no_wrapper'=>0,
        'is_custom'=>1,
        'html'=>'',
        'render'=>'',
        'args'=>'',
        'is_compound'=>0,
        'hide'=>0,
        'required'=>0,
        'content'=>'',
        'obj'=>null,
    );
}

function garden_alter_admin_menuitems( &$items ){
    $defaults = garden_admin_label_defaults();
    $overrides = garden_admin_label_overrides();

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
            elseif ( $name === 'admin-labels.php' ){
                $items[$name]['parent'] = '_templates_';
            }
            else{
                $items[$name]['parent'] = '_garden_admiral_';
            }
        }
    }
}

$FUNCS->add_event_listener( 'alter_admin_menuitems', 'garden_alter_admin_menuitems' );
