<?php
/**
 * Fix double scrollbar in admin: single scroll on #scroll-content only.
 */
$root = dirname(__DIR__);

$marker = '/* Full-height main panel + scroll-to-top placement */';

$oldBlock = <<<'CSS'
/* Full-height main panel + scroll-to-top placement */
#scroll-content{
  display:flex!important;
  flex-direction:column!important;
  min-height:100vh!important;
  background:#fff!important;
}
#content{
  flex:1 1 auto!important;
  display:flex!important;
  flex-direction:column!important;
  min-height:100vh!important;
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
  min-height:100%!important;
  background:#fff!important;
}
#content form,
#content>.tab-pane.active,
#tabs-page>.tab-pane.active,
#tabs-page>form{
  flex:1 1 auto!important;
  display:flex!important;
  flex-direction:column!important;
  min-height:100%!important;
}
#content .ctrl-bot,
#tabs-page .ctrl-bot{
  margin-top:auto!important;
}
.ctrl-bot>#top{
  position:fixed!important;
  top:12px!important;
  right:24px!important;
  bottom:auto!important;
  left:auto!important;
  z-index:25;
  margin:0!important;
}
.ctrl-bot:has(#settings-panel)>#top{
  top:12px!important;
  right:24px!important;
}
CSS;

$newBlock = <<<'CSS'
/* Full-height main panel + scroll-to-top placement */
html,body{
  overflow:hidden!important;
  height:100%!important;
}
#scroll-content{
  display:flex!important;
  flex-direction:column!important;
  height:100%!important;
  min-height:0!important;
  overflow-x:hidden!important;
  overflow-y:auto!important;
  background:#fff!important;
}
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
.ctrl-bot>#top{
  position:fixed!important;
  top:12px!important;
  right:24px!important;
  bottom:auto!important;
  left:auto!important;
  z-index:25;
  margin:0!important;
}
.ctrl-bot:has(#settings-panel)>#top{
  top:12px!important;
  right:24px!important;
}
CSS;

$replacements = array(
    '#content{background:#fff;min-height:100vh;padding:18px 24px 0;border-top:0}' =>
        '#content{background:#fff;padding:18px 24px 0;border-top:0}',
);

$paths = array(
    $root . '/couch/theme/garden/styles.css',
    $root . '/couch/addons/kfunctions.php',
);

foreach ($paths as $path) {
    $content = file_get_contents($path);
    $original = $content;

    if (strpos($content, $oldBlock) !== false) {
        $content = str_replace($oldBlock, $newBlock, $content);
    } else {
        $oldCrlf = str_replace("\n", "\r\n", $oldBlock);
        $newCrlf = str_replace("\n", "\r\n", $newBlock);
        if (strpos($content, $oldCrlf) !== false) {
            $content = str_replace($oldCrlf, $newCrlf, $content);
        } elseif (strpos($content, 'html,body{') === false && strpos($content, $marker) !== false) {
            fwrite(STDERR, basename($path) . ": block mismatch\n");
            exit(1);
        }
    }

    foreach ($replacements as $old => $new) {
        if (strpos($content, $old) !== false) {
            $content = str_replace($old, $new, $content);
        }
    }

    if ($content === $original) {
        if (strpos($content, 'html,body{') !== false && strpos($content, 'overflow:hidden!important') !== false) {
            echo basename($path) . " already patched\n";
            continue;
        }
        fwrite(STDERR, basename($path) . ": nothing changed\n");
        exit(1);
    }

    file_put_contents($path, $content);
    echo "Updated " . basename($path) . "\n";
}

passthru('php -l ' . escapeshellarg($root . '/couch/addons/kfunctions.php'), $code);
exit($code ?? 0);
