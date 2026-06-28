<?php
if ( !defined('K_COUCH_DIR') ) die();

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
        'preloader-settings.php' => array('field'=>'label_preloader_settings', 'title'=>'Прелоадер', 'weight'=>3),
        'age-gate-settings.php' => array('field'=>'label_age_gate_settings', 'title'=>'Заглушка 18+', 'weight'=>4),
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
            elseif ( in_array( $name, array( 'admin-labels.php', 'booking-settings.php', 'age-gate-settings.php', 'preloader-settings.php' ), true ) ){
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
#simple-page .login-remember{margin:0 0 14px;text-align:left}
#simple-page .login-remember label{display:inline-flex;align-items:center;gap:8px;color:#bbb;font-size:12px;font-weight:500;cursor:pointer;margin:0}
#simple-page .login-remember input[type=checkbox]{margin:0}
CSS;

    $FUNCS->add_css( $css );
}

$FUNCS->add_event_listener( 'add_admin_css', 'garden_admin_login_css' );
function garden_admin_branding_output( &$html ){
    if ( !defined( 'K_ADMIN' ) ) {
        return;
    }

    $html = preg_replace( '#<title>[^<]*</title>#', '<title>Garden Lounge</title>', $html, 1 );
    $favicon = '<link rel="icon" type="image/png" href="/favicon.png">' . "\n    "
        . '<link rel="shortcut icon" type="image/png" href="/favicon.png">';
    if ( preg_match( '#<link[^>]+rel=["\'](?:shortcut )?icon["\'][^>]*/>#i', $html ) ) {
        $html = preg_replace( '#<link[^>]+rel=["\'](?:shortcut )?icon["\'][^>]*/>#i', $favicon, $html, 1 );
    } else {
        $html = preg_replace( '#</head>#', $favicon . "\n</head>", $html, 1 );
    }
    $fonts = '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">';
    if ( strpos( $html, 'fonts.googleapis.com' ) === false ) {
        $html = preg_replace( '#</head>#', $fonts . "\n</head>", $html, 1 );
    }
    if ( defined( 'K_THEME_URL' ) && defined( 'K_THEME_DIR' ) && K_THEME_URL && is_file( K_THEME_DIR . 'styles.css' ) ) {
        $ver = filemtime( K_THEME_DIR . 'styles.css' );
        $html = str_replace( K_THEME_URL . 'styles.css', K_THEME_URL . 'styles.css?v=' . $ver, $html );
    }
}

$FUNCS->add_event_listener( 'alter_final_page_output', 'garden_admin_branding_output' );

function garden_admin_sidebar_js(){
    global $FUNCS;

    $js = <<<'JS'
(function($){
    $(function(){
        var $greeting = $('#sidebar-top');
        var $btns = $('#sidebar-btns');
        if ($greeting.length && $btns.length) {
            $greeting.attr('id', 'sidebar-greeting');
            $greeting.insertBefore($btns);
        }

        if ( typeof COUCH === 'undefined' || !COUCH.state ) return;
        if ( $.hasCookie('collapsed_groups') ) return;

        var ids = [];
        $('#sidebar .nav-heading-toggle').each(function(){
            ids.push(String($(this).data('id')));
        });
        COUCH.state.collapsedGroups = ids;
    });
})(jQuery);
JS;

    $FUNCS->add_js( $js );
}

$FUNCS->add_event_listener( 'add_admin_js', 'garden_admin_sidebar_js' );

function garden_admin_sidebar_css(){
    global $FUNCS;

    $css = <<<'CSS'
#menu-wrap .garden-admin-brand{padding:8px 8px 4px}
#menu-wrap .garden-admin-brand__logo,#menu-wrap #logo{max-width:198px!important;max-height:74px!important;width:100%!important}
.garden-admin-brand__subtitle{display:none!important}
#menu-content{position:relative;height:100%}
#scroll-sidebar{position:absolute!important;top:88px!important;right:0;left:0;bottom:132px!important;overflow-y:auto}
@media (max-height:540px){#scroll-sidebar{top:80px!important;bottom:124px!important}}
#sidebar-greeting,#sidebar-top{position:absolute;right:0;bottom:84px;left:0;z-index:2;border-top:1px solid #000;border-bottom:none;padding:10px 14px 8px;background-color:#1a1a1a;box-shadow:0 -1px 0 rgba(197,160,89,.08)}
#sidebar-greeting>p,#sidebar-top>p{color:#999;margin:0;font-size:12px;line-height:1.45}
#sidebar-greeting>p>a,#sidebar-top>p>a{color:#ddd}
div.kcfinder-iframe .mfp-content{max-width:968px;width:96vw;height:auto!important;max-height:90vh}
.kcfinder-iframe .mfp-iframe-scaler{padding-top:0!important;height:min(72vh,640px)!important;position:relative;overflow:hidden}
.kcfinder-iframe .mfp-iframe-scaler iframe{position:absolute;top:0;left:0;width:100%;height:100%;background:#fff}
CSS;

    $FUNCS->add_css( $css );
}

$FUNCS->add_event_listener( 'add_admin_css', 'garden_admin_sidebar_css' );

function garden_admin_typography_css(){
    global $FUNCS;

    $css = <<<'CSS'
body,input,select,textarea,.btn,.label,.field-label,.table,.tab>a{font-family:'Montserrat',Arial,sans-serif}
#header-title,#header-title a,.group-wrapper .panel-heading.panel-toggle,fieldset.row_fieldset legend,#content .panel>.panel-heading:not(.simple-heading):not(.panel-primary){font-family:'Cormorant Garamond',Georgia,serif!important;font-style:italic;font-weight:600;letter-spacing:.02em}
#header-title,#header-title a{color:#C5A059!important;text-shadow:none!important}
.group-wrapper .panel-heading.panel-toggle,fieldset.row_fieldset legend{color:#C5A059!important;background-color:#1a1a1a!important;background-image:none!important;border-color:rgba(197,160,89,.28)!important;text-shadow:none!important;font-size:22px;line-height:1.2;padding-top:11px;padding-bottom:11px}
.group-wrapper .panel-heading.panel-toggle:hover,.group-wrapper .panel-heading.panel-toggle:focus{color:#d4b06a!important}
.group-wrapper .panel-heading.panel-toggle .desc,.group-wrapper .panel-heading.panel-toggle .k_desc{color:rgba(197,160,89,.72)!important;font-family:'Montserrat',Arial,sans-serif!important;font-style:normal!important;font-size:12px;font-weight:500}
.group-wrapper .panel-heading.panel-toggle:after{color:#C5A059!important;text-shadow:none!important}
.group-wrapper .panel-heading.panel-toggle:hover:after,.group-wrapper .panel-heading.panel-toggle:focus:after{background-color:#C5A059!important;color:#111!important;box-shadow:none!important}
fieldset.row_fieldset legend:after{color:#C5A059!important}
fieldset.row_fieldset legend:hover:after,fieldset.row_fieldset legend:focus:after{background-color:#C5A059!important;color:#111!important}
.nav-heading,.nav-heading-toggle{font-family:'Cormorant Garamond',Georgia,serif!important;font-style:italic;letter-spacing:.08em}
CSS;

    $FUNCS->add_css( $css );
}

$FUNCS->add_event_listener( 'add_admin_css', 'garden_admin_typography_css' );



require_once K_ADDONS_DIR . 'garden-cache/garden-cache.php';



