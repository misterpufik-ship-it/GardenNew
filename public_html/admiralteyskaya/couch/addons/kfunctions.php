<?php
if ( !defined('K_COUCH_DIR') ) die();

function garden_admin_label_defaults(){
    return array(
        'header.php' => array('field'=>'label_header', 'title'=>'РЁР°РїРєР° СЃР°Р№С‚Р° (Header)', 'weight'=>100),
        'about.php' => array('field'=>'label_about', 'title'=>'РљРѕРЅС†РµРїС†РёСЏ', 'weight'=>110),
        'akzii.php' => array('field'=>'label_akzii', 'title'=>'РђРєС†РёРё', 'weight'=>120),
        'menu.php' => array('field'=>'label_menu', 'title'=>'РњРµРЅСЋ (РѕР±С‰РёРµ РЅР°СЃС‚СЂРѕР№РєРё)', 'weight'=>130),
        'menu/text/index.php' => array('field'=>'label_menu_text', 'title'=>'РњРµРЅСЋ RU', 'weight'=>140),
        'menu/english/index.php' => array('field'=>'label_menu_en', 'title'=>'РњРµРЅСЋ EN', 'weight'=>150),
        'menu/visual/index.php' => array('field'=>'label_menu_visual', 'title'=>'РњРµРЅСЋ РІРёР·СѓР°Р»СЊРЅРѕРµ', 'weight'=>160),
        'sticky-sticker.php' => array('field'=>'label_sticky_sticker', 'title'=>'Р›РёРїРєРёР№ СЃС‚РёРєРµСЂ вЂ” РђРґРјРёСЂР°Р»С‚РµР№СЃРєР°СЏ', 'weight'=>165),
        'gallery.php' => array('field'=>'label_gallery', 'title'=>'Р“Р°Р»РµСЂРµСЏ', 'weight'=>170),
        'reservation.php' => array('field'=>'label_reservation', 'title'=>'Р‘СЂРѕРЅРёСЂРѕРІР°РЅРёРµ', 'weight'=>180),
        'contacts.php' => array('field'=>'label_contacts', 'title'=>'РљРѕРЅС‚Р°РєС‚С‹', 'weight'=>190),
        'filial.php' => array('field'=>'label_filial', 'title'=>'Р¤РёР»РёР°Р»', 'weight'=>200),
        'globals.php' => array('field'=>'label_globals', 'title'=>'Р¤СѓС‚РµСЂ Рё SEO', 'weight'=>210),
        'index.php' => array('field'=>'label_index', 'title'=>'РћР±С‰Р°СЏ СЃС‚СЂР°РЅРёС†Р°', 'weight'=>220),
        'home.php' => array('field'=>'label_home', 'title'=>'Р“Р»Р°РІРЅР°СЏ', 'weight'=>1),
        'booking-settings.php' => array('field'=>'label_booking_settings', 'title'=>'Р‘СЂРѕРЅРёСЂРѕРІР°РЅРёРµ Telegram', 'weight'=>5),
        'admin-labels.php' => array('field'=>'', 'title'=>'РќР°Р·РІР°РЅРёСЏ СЂР°Р·РґРµР»РѕРІ', 'weight'=>230),

        'udelnaya/header.php' => array('field'=>'label_u_header', 'title'=>'РЁР°РїРєР° СЃР°Р№С‚Р° (Header)', 'weight'=>100),
        'udelnaya/about.php' => array('field'=>'label_u_about', 'title'=>'РљРѕРЅС†РµРїС†РёСЏ', 'weight'=>110),
        'udelnaya/akzii.php' => array('field'=>'label_u_akzii', 'title'=>'РђРєС†РёРё', 'weight'=>120),
        'udelnaya/menu.php' => array('field'=>'label_u_menu', 'title'=>'РњРµРЅСЋ (РѕР±С‰РёРµ РЅР°СЃС‚СЂРѕР№РєРё)', 'weight'=>130),
        'udelnaya/menu/text/index.php' => array('field'=>'label_u_menu_text', 'title'=>'РњРµРЅСЋ RU', 'weight'=>140),
        'udelnaya/menu/english/index.php' => array('field'=>'label_u_menu_en', 'title'=>'РњРµРЅСЋ EN', 'weight'=>150),
        'udelnaya/menu/visual/index.php' => array('field'=>'label_u_menu_visual', 'title'=>'РњРµРЅСЋ РІРёР·СѓР°Р»СЊРЅРѕРµ', 'weight'=>160),
        'udelnaya/sticky-sticker.php' => array('field'=>'label_u_sticky_sticker', 'title'=>'Р›РёРїРєРёР№ СЃС‚РёРєРµСЂ вЂ” РЈРґРµР»СЊРЅР°СЏ', 'weight'=>165),
        'udelnaya/gallery.php' => array('field'=>'label_u_gallery', 'title'=>'Р“Р°Р»РµСЂРµСЏ', 'weight'=>170),
        'udelnaya/reservation.php' => array('field'=>'label_u_reservation', 'title'=>'Р‘СЂРѕРЅРёСЂРѕРІР°РЅРёРµ', 'weight'=>180),
        'udelnaya/contacts.php' => array('field'=>'label_u_contacts', 'title'=>'РљРѕРЅС‚Р°РєС‚С‹', 'weight'=>190),
        'udelnaya/filial.php' => array('field'=>'label_u_filial', 'title'=>'Р¤РёР»РёР°Р»', 'weight'=>200),
        'udelnaya/globals.php' => array('field'=>'label_u_globals', 'title'=>'Р¤СѓС‚РµСЂ Рё SEO', 'weight'=>210),
        'udelnaya/index.php' => array('field'=>'label_u_index', 'title'=>'РћР±С‰Р°СЏ СЃС‚СЂР°РЅРёС†Р°', 'weight'=>220),
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
    if ( isset($items['site-home.php']) ){
        unset($items['site-home.php']);
    }

    $defaults = garden_admin_label_defaults();
    $overrides = garden_admin_label_overrides();

    $items['_garden_home_'] = garden_admin_menu_header( '_garden_home_', 'Р“Р»Р°РІРЅР°СЏ', -1 );
    $items['_garden_admiral_'] = garden_admin_menu_header( '_garden_admiral_', 'РђРґРјРёСЂР°Р»С‚РµР№СЃРєР°СЏ', 0 );
    $items['_garden_udelnaya_'] = garden_admin_menu_header( '_garden_udelnaya_', 'РЈРґРµР»СЊРЅР°СЏ', 1 );

    if ( isset($items['_templates_']) ){
        $items['_templates_']['title'] = 'РћР±С‰РёРµ';
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

$FUNCS->add_event_listener( 'alter_admin_menuitems', 'garden_alter_admin_menuitems' );

function garden_remove_couch_footer( &$html ){
    $html = preg_replace(
        '#<div style="clear:both;[^"]*">.*?Powered by CouchCMS.*?</div>\s*</div>#is',
        '',
        $html
    );
    $html = preg_replace(
        '#\n<!-- (?:Cached page served by |Page generated by )CouchCMS.*?-->\n#is',
        "\n",
        $html
    );
}

$FUNCS->add_event_listener( 'alter_final_page_output', 'garden_remove_couch_footer' );

function garden_admin_login_css(){
    global $FUNCS;

    $css = <<<'CSS'
#simple-page .garden-admin-brand--logo-only{padding:22px 16px 10px}
#simple-page .garden-admin-brand__subtitle{display:none!important}
#simple-page .garden-admin-brand__logo,#simple-page #simple-logo{max-width:340px!important;max-height:110px!important;width:min(78vw,340px)!important}
#simple-page #simple-wrap .panel-heading.simple-heading{display:flex!important;justify-content:center!important;align-items:center!important;padding:12px 15px!important;color:#C5A059!important;text-align:center!important;background-color:#141414!important;background-image:none!important;text-shadow:none!important;font-weight:600;border-color:rgba(197,160,89,.28)!important}
#simple-page #simple-wrap .panel-heading.simple-heading .login-heading{display:block!important;width:100%!important;color:#C5A059!important;text-align:center!important;text-shadow:none!important;font-size:15px;letter-spacing:.04em}
CSS;

    $FUNCS->add_css( $css );
}

$FUNCS->add_event_listener( 'add_admin_css', 'garden_admin_login_css' );

require_once K_ADDONS_DIR . 'garden-cache/garden-cache.php';



