<?php
if ( !defined('K_COUCH_DIR') ) die();

require_once K_ADDONS_DIR . 'garden-cache/cache-lib.php';

class KGardenCacheAdmin {
    function index_action(){
        global $FUNCS;

        $index_link = $FUNCS->generate_route('garden-cache', 'index');
        $FUNCS->set_admin_title('Очистить кэш', $index_link);
        $FUNCS->set_admin_subtitle('Кэш CouchCMS', 'cog');

        $message = '';

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['garden_clear_cache']) ){
            $FUNCS->validate_nonce('garden_clear_cache');
            $removed = garden_clear_couch_cache();
            $message =
                '<div class="alert alert-success alert-icon" style="margin-bottom:18px;">' .
                'Кэш очищен. Удалено файлов: <strong>' . intval($removed) . '</strong>.' .
                '</div>';
        }

        $nonce = $FUNCS->create_nonce('garden_clear_cache');

        $html = $message;
        $html .= '<p style="max-width:640px;line-height:1.6;">';
        $html .= 'Удаляет сгенерированные файлы кэша CouchCMS из папки <code>cache/</code>. ';
        $html .= 'Служебная папка <code>booking-throttle</code> не затрагивается.';
        $html .= '</p>';
        $html .= '<form method="post" action="' . htmlspecialchars($index_link, ENT_QUOTES, 'UTF-8') . '">';
        $html .= '<input type="hidden" name="nonce" value="' . htmlspecialchars($nonce, ENT_QUOTES, 'UTF-8') . '">';
        $html .= '<input type="hidden" name="garden_clear_cache" value="1">';
        $html .= '<button type="submit" class="btn btn-primary">Очистить кэш</button>';
        $html .= '</form>';

        return $html;
    }
}

function garden_cache_register_routes(){
    global $FUNCS;

    $FUNCS->register_route('garden-cache', array(
        'name' => 'index',
        'action' => array(new KGardenCacheAdmin(), 'index_action'),
        'module' => 'garden-cache',
    ));
}

function garden_cache_register_admin_menuitems(){
    global $FUNCS;

    $FUNCS->register_admin_menuitem(array(
        'name' => 'garden_clear_cache',
        'title' => 'Очистить кэш',
        'desc' => 'Удалить сгенерированные файлы кэша CouchCMS',
        'weight' => 40,
        'icon' => 'cog',
        'parent' => '_modules_',
        'route' => array(
            'masterpage' => 'garden-cache',
            'name' => 'index',
        ),
    ));
}

if ( defined('K_ADMIN') ){
    $FUNCS->add_event_listener('register_admin_routes', 'garden_cache_register_routes');
    $FUNCS->add_event_listener('register_admin_menuitems', 'garden_cache_register_admin_menuitems');
}
