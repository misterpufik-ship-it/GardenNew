<?php
/**
 * Restore sidebar footer, fix ctrl-bot single row, stop #top overlap.
 */
$root = dirname(__DIR__);

$sidebarFix = <<<'CSS'

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

$oldTopConflict = <<<'CSS'
.ctrl-bot:has(#settings-panel)>#top{
  position:absolute!important;
  top:21px!important;
  right:0;
  margin:0;
}
CSS;

$newTopRule = <<<'CSS'
.ctrl-bot>#top,
.ctrl-bot:has(#settings-panel)>#top{
  position:fixed!important;
  top:12px!important;
  right:24px!important;
  bottom:auto!important;
  left:auto!important;
  margin:0!important;
  z-index:30!important;
}
CSS;

$oldGridBlock = <<<'CSS'
.ctrl-bot:has(#settings-panel){
  display:grid!important;
  grid-template-columns:minmax(0,1fr) auto minmax(0,1fr);
  align-items:center!important;
  gap:0 12px;
  font-size:12px!important;
  min-height:38px;
}
CSS;

$newFlexBlock = <<<'CSS'
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
}
CSS;

$oldSubmitRule = <<<'CSS'
.ctrl-bot:has(#settings-panel)>#btn_submit{
  grid-column:2;
  justify-self:center;
  margin:0!important;
  height:38px;
  line-height:36px;
  vertical-align:middle!important;
}
CSS;

$newSubmitRule = <<<'CSS'
.ctrl-bot:has(#settings-panel)>#btn_submit{
  position:absolute!important;
  left:50%!important;
  transform:translateX(-50%)!important;
  margin:0!important;
  height:38px;
  line-height:36px;
  vertical-align:middle!important;
}
CSS;

$oldViewRule = <<<'CSS'
.ctrl-bot:has(#settings-panel)>#btn_view{
  grid-column:3;
  justify-self:end;
  margin:0!important;
  height:38px;
  line-height:36px;
  vertical-align:middle!important;
}
CSS;

$newViewRule = <<<'CSS'
.ctrl-bot:has(#settings-panel)>#btn_view{
  margin-left:auto!important;
  margin-right:0!important;
  height:38px;
  line-height:36px;
  vertical-align:middle!important;
}
CSS;

$oldSettingsPanel = <<<'CSS'
.ctrl-bot:has(#settings-panel) #settings-panel{
  position:relative!important;
  grid-column:1;
  justify-self:start;
  margin:0!important;
  padding:0!important;
}
CSS;

$newSettingsPanel = <<<'CSS'
.ctrl-bot:has(#settings-panel) #settings-panel{
  position:relative!important;
  flex:0 0 auto!important;
  margin:0!important;
  padding:0!important;
}
CSS;

$oldCtrlRight = <<<'CSS'
.ctrl-bot:has(#settings-panel)>.ctrl-right{
  grid-column:3;
  justify-self:end;
  margin:0!important;
}
CSS;

$newCtrlRight = <<<'CSS'
.ctrl-bot:has(#settings-panel)>.ctrl-right{
  margin-left:auto!important;
  margin-right:0!important;
}
CSS;

$oldMenuContent = '#menu-content{position:relative;height:100%}';

$paths = array(
    $root . '/couch/theme/garden/styles.css',
    $root . '/couch/addons/kfunctions.php',
);

foreach ($paths as $path) {
    $content = file_get_contents($path);
    $original = $content;

    $pairs = array(
        array($oldTopConflict, $newTopRule),
        array($oldGridBlock, $newFlexBlock),
        array($oldSubmitRule, $newSubmitRule),
        array($oldViewRule, $newViewRule),
        array($oldSettingsPanel, $newSettingsPanel),
        array($oldCtrlRight, $newCtrlRight),
    );

    foreach ($pairs as $pair) {
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

    if (strpos($content, $oldMenuContent) !== false) {
        $content = str_replace($oldMenuContent, trim($oldMenuContent) . '}', $content);
        $content = str_replace(
            '#menu-content{position:relative;height:100%}}',
            '#menu-content{flex:1 1 auto!important;position:relative!important;height:auto!important;min-height:0!important}',
            $content
        );
    }

    // Fix botched replace - do menu-content properly
    $content = preg_replace(
        '/#menu-content\{position:relative;height:100%\}\}?/',
        '#menu-content{flex:1 1 auto!important;position:relative!important;height:auto!important;min-height:0!important}',
        $content
    );

    if (strpos($content, '/* Sidebar column: footer visible, aligned to bottom */') === false) {
        $anchor = '/* === Garden admin shell v2 === */';
        $pos = strpos($content, $anchor);
        if ($pos === false) {
            fwrite(STDERR, basename($path) . ": shell anchor missing\n");
            exit(1);
        }
        $insertAt = strpos($content, "\n", $pos);
        $content = substr($content, 0, $insertAt + 1) . $sidebarFix . substr($content, $insertAt + 1);
    }

    // Remove duplicate .ctrl-bot>#top block if we now have unified rule
    $content = preg_replace(
        '/\.ctrl-bot>#top\{\s*position:fixed!important;\s*top:12px!important;\s*right:24px!important;\s*bottom:auto!important;\s*left:auto!important;\s*z-index:25;\s*margin:0!important;\s*\}\s*\.ctrl-bot:has\(#settings-panel\)>#top\{\s*top:12px!important;\s*right:24px!important;\s*\}/s',
        '',
        $content,
        1
    );

    if ($content === $original && strpos($content, '/* Sidebar column: footer visible, aligned to bottom */') !== false) {
        echo basename($path) . " already patched\n";
        continue;
    }

    file_put_contents($path, $content);
    echo "Updated " . basename($path) . "\n";
}

passthru('php -l ' . escapeshellarg($root . '/couch/addons/kfunctions.php'), $code);
exit($code ?? 0);
