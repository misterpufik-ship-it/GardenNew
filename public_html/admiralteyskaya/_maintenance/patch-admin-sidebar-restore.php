<?php
/**
 * Restore admin left sidebar (fixed column + scroll offset).
 */
$root = dirname(__DIR__);

$oldBlock = <<<'CSS'
/* Sidebar column: footer visible, aligned to bottom */
#sidebar{
  display:flex!important;
  flex-direction:column!important;
  overflow:hidden!important;
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
#sidebar-toggle{
  display:block!important;
  z-index:30!important;
}
@media (max-height:540px){
  #sidebar-greeting{display:block!important}
}
CSS;

$newBlock = <<<'CSS'
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
#sidebar-toggle{
  display:block!important;
  z-index:210!important;
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
CSS;

$oldScroll = <<<'CSS'
#scroll-content{
  display:flex!important;
  flex-direction:column!important;
  height:100%!important;
  min-height:0!important;
  overflow-x:hidden!important;
  overflow-y:auto!important;
  background:#fff!important;
}
CSS;

$newScroll = <<<'CSS'
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
CSS;

$oldJs = <<<'JS'
    $(function(){
        var $greeting = $('#sidebar-top');
        var $btns = $('#sidebar-btns');
        if ($greeting.length && $btns.length) {
            $greeting.attr('id', 'sidebar-greeting');
            $greeting.insertBefore($btns);
        }
        $('#gl-header-user').remove();
JS;

$newJs = <<<'JS'
    function ensureSidebarVisible(){
        var $sidebar = $('#sidebar');
        if (!$sidebar.length) return;
        $sidebar.removeClass('collapsed');
        if (typeof $.removeCookie === 'function') {
            $.removeCookie('collapsed_sidebar');
        }
    }

    $(function(){
        ensureSidebarVisible();

        var $greeting = $('#sidebar-top');
        var $btns = $('#sidebar-btns');
        if ($greeting.length && $btns.length) {
            $greeting.attr('id', 'sidebar-greeting');
            $greeting.insertBefore($btns);
        }
        $('#gl-header-user').remove();
JS;

$paths = array(
    $root . '/couch/theme/garden/styles.css',
    $root . '/couch/addons/kfunctions.php',
);

foreach ($paths as $path) {
    $content = file_get_contents($path);
    $original = $content;
    foreach (array(
        array($oldBlock, $newBlock),
        array($oldScroll, $newScroll),
    ) as $pair) {
        list($old, $new) = $pair;
        if (strpos($content, $old) !== false) {
            $content = str_replace($old, $new, $content);
            continue;
        }
        $oldCrlf = str_replace("\n", "\r\n", $old);
        $newCrlf = str_replace("\n", "\r\n", $new);
        if (strpos($content, $oldCrlf) !== false) {
            $content = str_replace($oldCrlf, $newCrlf, $content);
        }
    }
    if ($content === $original) {
        if (strpos($content, 'position:fixed!important') !== false && strpos($content, 'left:240px!important') !== false) {
            echo basename($path) . " already patched\n";
            continue;
        }
        fwrite(STDERR, basename($path) . ": block not found\n");
        exit(1);
    }
    file_put_contents($path, $content);
    echo "Updated " . basename($path) . "\n";
}

$kfn = file_get_contents($root . '/couch/addons/kfunctions.php');
if (strpos($kfn, 'ensureSidebarVisible') === false) {
    if (strpos($kfn, $oldJs) !== false) {
        $kfn = str_replace($oldJs, $newJs, $kfn);
    } else {
        $oldJsCrlf = str_replace("\n", "\r\n", $oldJs);
        $newJsCrlf = str_replace("\n", "\r\n", $newJs);
        $kfn = str_replace($oldJsCrlf, $newJsCrlf, $kfn);
    }
    file_put_contents($root . '/couch/addons/kfunctions.php', $kfn);
    echo "Updated kfunctions.js\n";
}

passthru('php -l ' . escapeshellarg($root . '/couch/addons/kfunctions.php'), $code);
exit($code ?? 0);
