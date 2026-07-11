<?php
if ( !defined('K_COUCH_DIR') ) die();

function garden_admin_label_defaults(){
    return array(
        
        'admin-instructions.php' => array('field'=>'', 'title'=>'Инструкции', 'weight'=>-10),
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
    if ( isset($items['menu/text/import.php']) ){
        unset($items['menu/text/import.php']);
    }
    if ( isset($items['menu/import.php']) ){
        unset($items['menu/import.php']);
    }

    $defaults = garden_admin_label_defaults();
    $overrides = garden_admin_label_overrides();

    $items['_garden_instructions_'] = garden_admin_menu_header( '_garden_instructions_', 'Инструкции', -2 );
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
            if ( $name === 'admin-instructions.php' ){
                $items[$name]['parent'] = '_garden_instructions_';
            }
            elseif ( strpos($name, 'udelnaya/') === 0 ){
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
function garden_is_couch_admin_context(){
    if ( defined( 'K_ADMIN' ) ) {
        return true;
    }
    $uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
    return ( strpos( $uri, '/couch/' ) !== false );
}

function garden_admin_branding_output( &$html ){
    if ( !garden_is_couch_admin_context() ) {
        return;
    }

    $html = preg_replace( '#<title>[^<]*</title>#', '<title>Garden Lounge</title>', $html, 1 );
    $html = preg_replace( '#<link[^>]*\brel\s*=\s*["\'](?:shortcut\s+)?icon["\'][^>]*/?>#i', '', $html );
    $verPng = @filemtime( $_SERVER['DOCUMENT_ROOT'] . '/favicon.png' ) ?: time();
    $favicon = '<link rel="icon" type="image/png" sizes="32x32" href="/favicon.png?v=' . $verPng . '">' . "\n    "
        . '<link rel="shortcut icon" type="image/png" href="/favicon.png?v=' . $verPng . '">' . "\n    "
        . '<link rel="apple-touch-icon" href="/favicon.png?v=' . $verPng . '">';
    $html = preg_replace( '#</head>#', $favicon . "\n</head>", $html, 1 );

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
    function addDumpLink(){
        var $adv = $('.group-wrapper').filter(function(){
            return $(this).find('[name="k_publish_date"], [name="k_access_level"], [name="k_show_in_menu"]').length > 0;
        }).find('> .panel-collapse > .panel-body, > .panel-body').first();
        if (!$adv.length || $('.gl-admin-dump-link').length) return;
        var m = window.location.pathname.match(/^(.*\/couch\/)/);
        var href = m ? (m[1] + 'gen_dump.php') : 'gen_dump.php';
        $adv.prepend('<p class="gl-admin-dump-link"><a href="' + href + '">Download Dump</a></p>');
    }

    function gardenAdminLandingRedirect(){
        var path = window.location.pathname || '';
        if ( !/\/couch\/admin\.php$/i.test(path) ) return;
        var params = new URLSearchParams(window.location.search || '');
        if ( params.has('o') ) return;
        window.location.replace(path + '?o=admin-instructions.php&q=list');
    }

    function setupSidebarGreetingRow(){
        var $greeting = $('#sidebar-top');
        var $btns = $('#sidebar-btns');
        if ($greeting.length && $btns.length) {
            $greeting.attr('id', 'sidebar-greeting');
            $greeting.insertBefore($btns);
        }
    }

    function setupSidebarToggle(){
        var $toggle = $('#sidebar-toggle');
        if (!$toggle.length) return;

        var $menuContent = $('#menu-content');
        var $greeting = $('#sidebar-greeting, #sidebar-top').first();
        if ($menuContent.length) {
            $toggle.appendTo($menuContent);
        } else if ($greeting.length) {
            $toggle.insertBefore($greeting);
        }

        $toggle.addClass('gl-sidebar-toggle-btn');
        $toggle.find('>.i').attr('aria-hidden', 'true');

        if (!$toggle.find('.gl-sidebar-toggle-arrow').length) {
            $toggle.contents().filter(function(){
                return this.nodeType === 3;
            }).remove();
            $toggle.append('<span class="gl-sidebar-toggle-arrow" aria-hidden="true"></span>');
        }

        syncSidebarToggleArrow();
    }

    function syncSidebarToggleArrow(){
        var $toggle = $('#sidebar-toggle');
        var $arrow = $toggle.find('.gl-sidebar-toggle-arrow');
        if (!$arrow.length) return;

        var collapsed = $('#sidebar').hasClass('collapsed');
        $arrow.text(collapsed ? '\u203A' : '\u2039');
        $toggle.attr('title', collapsed ? 'Показать меню' : 'Скрыть меню');
        $toggle.attr('aria-label', collapsed ? 'Показать боковое меню' : 'Скрыть боковое меню');
    }

    $(function(){
        setupSidebarGreetingRow();
        setupSidebarToggle();

        $('#sidebar-toggle').on('click', function(){
            window.setTimeout(syncSidebarToggleArrow, 0);
        });

        $('#gl-header-user').remove();
        addDumpLink();

        if ( typeof COUCH === 'undefined' || !COUCH.state ) return;
        if ( $.hasCookie('collapsed_groups') ) return;

        var ids = [];
        $('#sidebar .nav-heading-toggle').each(function(){
            ids.push(String($(this).data('id')));
        });
        COUCH.state.collapsedGroups = ids;
        gardenAdminLandingRedirect();
    });
})(jQuery);
JS;

    $FUNCS->add_js( $js );
}

$FUNCS->add_event_listener( 'add_admin_js', 'garden_admin_sidebar_js' );

function garden_admin_sidebar_css(){
    global $FUNCS;

    $css = <<<'CSS'
/* === Garden admin shell v2 === */

/* Sidebar column: footer visible, aligned to bottom */
#sidebar{
  display:flex!important;
  flex-direction:column!important;
  position:fixed!important;
  top:0!important;
  left:0!important;
  bottom:0!important;
  width:240px!important;
  min-width:240px!important;
  max-width:240px!important;
  height:100%!important;
  z-index:200!important;
  overflow:hidden!important;
  visibility:visible!important;
}
#sidebar.collapsed{
  left:-240px!important;
}
#menu-wrap{
  flex:0 0 auto!important;
}
#menu-content{
  flex:1 1 auto!important;
  position:relative!important;
  height:auto!important;
  min-height:0!important;
  overflow:visible!important;
}
#sidebar-greeting,#sidebar-top{
  display:block!important;
  visibility:visible!important;
}
#sidebar-btns{
  display:flex!important;
  visibility:visible!important;
}
#scroll-sidebar{bottom:176px!important}
@media (max-height:540px){#scroll-sidebar{bottom:168px!important}}
#sidebar-toggle.gl-sidebar-toggle-btn{
  position:absolute!important;
  right:0!important;
  left:auto!important;
  bottom:124px!important;
  top:auto!important;
  display:flex!important;
  align-items:center!important;
  justify-content:center!important;
  width:26px!important;
  height:52px!important;
  margin:0!important;
  padding:0!important;
  border:1px solid rgba(197,160,89,.35)!important;
  border-right:0!important;
  border-radius:6px 0 0 6px!important;
  background:#0a0a0a!important;
  background-image:none!important;
  fill:var(--gl-gold,#C5A059)!important;
  color:var(--gl-gold,#C5A059)!important;
  cursor:pointer!important;
  z-index:220!important;
  box-shadow:none!important;
  transform:none!important;
}
#sidebar-toggle.gl-sidebar-toggle-btn:hover,
#sidebar-toggle.gl-sidebar-toggle-btn:focus{
  background:rgba(197,160,89,.12)!important;
  border-color:rgba(197,160,89,.55)!important;
  color:#fff!important;
  fill:#fff!important;
}
#sidebar-toggle.gl-sidebar-toggle-btn>.i{display:none!important}
.gl-sidebar-toggle-arrow{
  display:block!important;
  font-size:20px!important;
  line-height:1!important;
  font-weight:600!important;
  color:inherit!important;
  pointer-events:none!important;
}
@media (max-height:540px){
  #sidebar-greeting{display:block!important}
}
@media (min-width:762px){
  #menu-content,#sidebar-toggle,#sidebar-top,#sidebar-greeting,#sidebar-btns{display:block!important}
  #sidebar-btns{display:flex!important}
  #scroll-content{
    position:absolute!important;
    top:0!important;
    right:0!important;
    bottom:0!important;
    left:240px!important;
    width:auto!important;
  }
  #sidebar.collapsed+#scroll-content{left:0!important}
}
:root{--gl-admin-header:0;--gl-admin-sidebar-footer:119px}
#sidebar,#menu-wrap,#logo-wrap{background-color:#0a0a0a!important;background-image:none!important}
#logo-wrap{border-bottom:1px solid rgba(197,160,89,.28)}
#menu-wrap .garden-admin-brand{padding:8px 10px 4px}
#menu-wrap .garden-admin-brand__logo,#menu-wrap #logo{max-width:210px!important;max-height:82px!important;width:100%!important}
#menu-content{flex:1 1 auto!important;position:relative!important;height:auto!important;min-height:0!important}
#scroll-sidebar{position:absolute!important;top:0!important;right:0;left:0;bottom:176px!important;overflow-y:auto}
@media (max-height:540px){#scroll-sidebar{top:0!important;bottom:168px!important}}
#nav-links{display:none!important}
#sidebar-greeting,#sidebar-top{position:absolute!important;right:0;bottom:84px;left:0;z-index:2;box-sizing:border-box;border-top:1px solid #000;border-bottom:none;padding:10px 12px 8px;background-color:var(--gl-black)!important;box-shadow:0 -1px 0 rgba(197,160,89,.08)}
#sidebar-greeting>p,#sidebar-top>p{color:#999;margin:0;font-size:12px;line-height:1.45}
#sidebar-greeting>p>a,#sidebar-top>p>a{color:#ddd}
#sidebar-btns{position:absolute!important;right:0;bottom:24px;left:0;box-sizing:border-box;display:flex!important;align-items:stretch;height:60px!important;padding:11px 12px 10px!important;border-top:1px solid #000!important;background-color:var(--gl-black)!important}
#sidebar-btns>.btn{flex:1 1 0;width:auto!important;min-width:0;float:none!important}
#sidebar-btns>#log-out,#sidebar-btns>#view-site{width:auto!important}
#sidebar-bot{display:block!important;position:absolute!important;right:0;bottom:0;left:0;height:24px!important;visibility:hidden;overflow:hidden;padding:0!important;margin:0!important;border:0!important}
#header{display:none!important;padding:0!important;border:0!important;background:transparent!important}
#header:has(.btn-group a.btn){display:block!important;padding:10px 24px 8px!important;background:#0a0a0a!important;border-bottom:1px solid rgba(197,160,89,.28)!important}
#header-inner{padding:0!important;min-height:0!important}
#header-title,#header ul#tabs,#tabs{display:none!important}
#header .subtitle{display:none!important}
#gl-header-user{display:none!important}
#scroll-content{background:#fff}
#content{background:#fff;padding:18px 24px 0;border-top:0}
body #tabs-page #content{background:#fff;padding-bottom:28px}
.panel,.group-wrapper .panel{border-color:rgba(197,160,89,.28)}
.gl-admin-dump-link{margin:0 0 12px;font-size:12px}
.gl-admin-dump-link a{color:#C5A059}
div.kcfinder-iframe .mfp-content{max-width:968px;width:96vw;height:auto!important;max-height:90vh}
.kcfinder-iframe .mfp-iframe-scaler{padding-top:0!important;height:min(72vh,640px)!important;position:relative;overflow:hidden}
.kcfinder-iframe .mfp-iframe-scaler iframe{position:absolute;top:0;left:0;width:100%;height:100%;background:#fff}
CSS;

    $FUNCS->add_css( $css );
}

function garden_admin_form_css(){
    global $FUNCS;

    $css = <<<'CSS'

/* Full-height main panel + scroll-to-top placement */
html,body{
  overflow:hidden!important;
  height:100%!important;
}
#scroll-content{
  display:flex!important;
  flex-direction:column!important;
  position:absolute!important;
  top:0!important;
  right:0!important;
  bottom:0!important;
  left:240px!important;
  width:auto!important;
  height:100%!important;
  min-height:0!important;
  overflow-x:hidden!important;
  overflow-y:auto!important;
  background:#fff!important;
  z-index:1!important;
}
#sidebar.collapsed+#scroll-content{left:0!important}
#content{
  flex:1 1 auto!important;
  display:flex!important;
  flex-direction:column!important;
  min-height:0!important;
  background:#fff!important;
  padding:18px 24px 0!important;
  box-sizing:border-box;
}
body #tabs-page #content{
  padding-bottom:0!important;
  background:#fff!important;
}
#tabs-page{
  flex:1 1 auto!important;
  display:flex!important;
  flex-direction:column!important;
  min-height:0!important;
  background:#fff!important;
}
#content form,
#content>.tab-pane.active,
#tabs-page>.tab-pane.active,
#tabs-page>form{
  flex:1 1 auto!important;
  display:flex!important;
  flex-direction:column!important;
  min-height:0!important;
}
#content .ctrl-bot,
#tabs-page .ctrl-bot{
  margin-top:auto!important;
}

/* Advanced settings + actions bar */
.ctrl-bot{
  box-sizing:border-box!important;
  padding:11px 24px!important;
}
.ctrl-bot .ctrl-right{
  position:static!important;
  top:auto!important;
  bottom:auto!important;
  right:auto!important;
  display:inline-flex!important;
  align-items:center;
}
.ctrl-bot:not(:has(#settings-panel)) .ctrl-right{
  margin-left:auto!important;
}
.ctrl-bot .ctrl-right>.btn-group{
  display:inline-flex!important;
  align-items:center;
  margin:0!important;
  vertical-align:middle!important;
}
.ctrl-bot:has(#settings-panel){
  display:flex!important;
  flex-direction:row!important;
  flex-wrap:nowrap!important;
  align-items:center!important;
  justify-content:flex-start!important;
  gap:12px;
  font-size:12px!important;
  min-height:60px;
  position:relative!important;
  padding-left:24px!important;
  padding-right:24px!important;
}
.ctrl-bot:has(#settings-panel)>#top,
.ctrl-bot>#top{
  position:static!important;
  top:auto!important;
  right:auto!important;
  bottom:auto!important;
  left:auto!important;
  margin:0 0 0 auto!important;
  flex:0 0 auto!important;
  z-index:1!important;
}
.ctrl-bot:has(#settings-panel) #settings-panel{
  position:relative!important;
  flex:0 0 auto!important;
  margin:0!important;
  padding:0!important;
}
.ctrl-bot:has(#settings-panel) #settings-panel-toggle{
  position:static!important;
  top:auto!important;
  right:auto!important;
  margin:0!important;
  height:38px;
  line-height:36px;
  display:inline-flex;
  align-items:center;
}
.ctrl-bot:has(#settings-panel) #settings-panel>.panel-body{
  position:absolute!important;
  left:0;
  right:auto;
  bottom:calc(100% + 6px);
  top:auto!important;
  float:none!important;
  width:min(440px,calc(100vw - 320px));
  z-index:5;
}
.ctrl-bot:has(#settings-panel)>#btn_submit,
.ctrl-bot:has(#settings-panel)>#btn_view{
  position:static!important;
  left:auto!important;
  transform:none!important;
  margin:0!important;
  flex:0 0 auto!important;
  height:38px;
  line-height:36px;
  vertical-align:middle!important;
}
.ctrl-bot:has(#settings-panel)>.ctrl-right{
  margin:0 0 0 auto!important;
}
.ctrl-bot:has(#settings-panel)>.btn:not(#top),
.ctrl-bot:has(#settings-panel)>a.btn{
  align-self:center;
  margin-top:0!important;
  margin-bottom:0!important;
}

CSS;

    $FUNCS->add_css( $css );
}

$FUNCS->add_event_listener( 'add_admin_css', 'garden_admin_form_css' );

function garden_admin_typography_css(){
    global $FUNCS;

    $css = <<<'CSS'
body,input,select,textarea,.btn,.label,.field-label,.table,.tab>a{font-family:'Montserrat',Arial,sans-serif}
#header-title,#header-title a,.group-wrapper .panel-heading.panel-toggle,fieldset.row_fieldset legend,#content .panel>.panel-heading:not(.simple-heading):not(.panel-primary){font-family:'Cormorant Garamond',Georgia,serif!important;font-style:italic;font-weight:600;letter-spacing:.02em}
@keyframes glGoldShine{to{background-position:200% center}}
.gl-gold-shimmer,#header-title,#header-title a,.nav-heading-toggle,.group-wrapper .panel-heading.panel-toggle,fieldset.row_fieldset legend{
background:linear-gradient(to right,#8e7037 0%,#C5A059 40%,#FFEebb 50%,#C5A059 60%,#8e7037 100%);
background-size:200% auto;-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;color:transparent!important;
animation:glGoldShine 5s linear infinite;text-shadow:none!important}
.nav-heading-toggle .nav-heading-btn{-webkit-text-fill-color:#aaa;color:#aaa!important;background:none!important;animation:none!important}
#header-title{font-size:20px!important;height:auto!important;line-height:1.25!important;padding:4px 0}
.nav-heading,.nav-heading-toggle{font-size:10px!important;font-weight:600!important;letter-spacing:.14em!important;text-transform:uppercase!important}
.nav-heading-toggle:hover,.nav-heading-toggle:focus,.group-wrapper .panel-heading.panel-toggle:hover,.group-wrapper .panel-heading.panel-toggle:focus{
-webkit-text-fill-color:transparent;color:transparent!important}
.group-wrapper .panel-heading.panel-toggle,fieldset.row_fieldset legend{
background-color:#1a1a1a!important;background-image:none!important;border-color:rgba(197,160,89,.28)!important;
font-size:18px!important;line-height:1.2;padding-top:11px;padding-bottom:11px}
.group-wrapper .panel-heading.panel-toggle .desc,.group-wrapper .panel-heading.panel-toggle .k_desc{color:rgba(197,160,89,.72)!important;font-family:'Montserrat',Arial,sans-serif!important;font-style:normal!important;font-size:12px;font-weight:500;-webkit-text-fill-color:initial;background:none;animation:none}
.group-wrapper .panel-heading.panel-toggle:after,fieldset.row_fieldset legend:after{color:#C5A059!important;text-shadow:none!important;-webkit-text-fill-color:initial;background:none!important;animation:none!important}
.group-wrapper .panel-heading.panel-toggle:hover:after,.group-wrapper .panel-heading.panel-toggle:focus:after,fieldset.row_fieldset legend:hover:after,fieldset.row_fieldset legend:focus:after{background-color:#C5A059!important;color:#111!important}
.nav-heading,.nav-heading-toggle{font-family:'Cormorant Garamond',Georgia,serif!important;font-style:italic;letter-spacing:.08em}
CSS;

    $FUNCS->add_css( $css );
}

$FUNCS->add_event_listener( 'add_admin_css', 'garden_admin_typography_css' );



require_once K_ADDONS_DIR . 'garden-cache/garden-cache.php';



