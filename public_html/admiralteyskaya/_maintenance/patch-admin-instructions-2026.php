<?php
/**
 * Admin UX patch: instructions landing, toolbar layout, hide legacy import.
 */
$root = dirname(__DIR__);

$newCtrlBotCss = <<<'CSS'
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

function replace_ctrl_bot_block($content, $newBlock) {
    $marker = '/* Advanced settings next to bottom action buttons */';
    $pos = strpos($content, $marker);
    if ($pos === false) {
        return false;
    }
    if (!preg_match('/\/\* Advanced settings next to bottom action buttons \*\/.*?(?=\n\/\*|\nCSS;|\z)/s', $content, $m, 0, $pos)) {
        return false;
    }
    return substr($content, 0, $pos) . rtrim($newBlock) . "\n" . substr($content, $pos + strlen($m[0]));
}

$menuFunction = <<<'PHP'
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
PHP;

$kfnPath = $root . '/couch/addons/kfunctions.php';
$content = file_get_contents($kfnPath);

if ( strpos($content, "'admin-instructions.php'") === false ) {
    $content = preg_replace(
        '/(function garden_admin_label_defaults\(\)\{\s*return array\(\s*)/',
        "$1\n        'admin-instructions.php' => array('field'=>'', 'title'=>'Инструкции', 'weight'=>-10),\n",
        $content,
        1,
        $cLabel
    );
    if (!$cLabel) {
        fwrite(STDERR, "Failed to insert admin-instructions label\n");
        exit(1);
    }
}

$content = preg_replace(
    '/function garden_alter_admin_menuitems\( &\$items \)\{.*?\n\}/s',
    trim($menuFunction),
    $content,
    1,
    $cMenu
);
if (!$cMenu) {
    fwrite(STDERR, "Failed to replace garden_alter_admin_menuitems\n");
    exit(1);
}

if ( strpos($content, 'gardenAdminLandingRedirect') === false ) {
    $landingFn = <<<'JS'

    function gardenAdminLandingRedirect(){
        var path = window.location.pathname || '';
        if ( !/\/couch\/admin\.php$/i.test(path) ) return;
        var params = new URLSearchParams(window.location.search || '');
        if ( params.has('o') ) return;
        window.location.replace(path + '?o=admin-instructions.php&q=list');
    }
JS;
    $content = preg_replace(
        '/(\$\(function\(\)\{\s*ensureSidebarVisible\(\);)/',
        $landingFn . "\n$1",
        $content,
        1,
        $cFn
    );
    if (!$cFn) {
        fwrite(STDERR, "Failed to insert landing redirect function\n");
        exit(1);
    }
    $content = preg_replace(
        '/(COUCH\.state\.collapsedGroups = ids;)/',
        "$1\n        gardenAdminLandingRedirect();",
        $content,
        1,
        $cCall
    );
    if (!$cCall) {
        fwrite(STDERR, "Failed to insert landing redirect call\n");
        exit(1);
    }
}

$updated = replace_ctrl_bot_block($content, $newCtrlBotCss);
if ($updated === false) {
    fwrite(STDERR, "ctrl-bot block not found in kfunctions.php\n");
    exit(1);
}
$content = $updated;

file_put_contents($kfnPath, $content);
echo "Updated kfunctions.php\n";

foreach (array(
    $root . '/couch/theme/garden/styles.css',
) as $path) {
    $css = file_get_contents($path);
    $updatedCss = replace_ctrl_bot_block($css, $newCtrlBotCss);
    if ($updatedCss === false) {
        fwrite(STDERR, basename($path) . ": ctrl-bot block not found\n");
        exit(1);
    }
    file_put_contents($path, $updatedCss);
    echo "Updated " . basename($path) . "\n";
}

passthru('php -l ' . escapeshellarg($kfnPath), $code);
exit($code ?: 0);
