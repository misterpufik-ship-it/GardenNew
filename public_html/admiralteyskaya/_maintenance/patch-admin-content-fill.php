<?php
/**
 * Fill admin content area to bottom; move scroll-to-top away from action buttons.
 */
$root = dirname(__DIR__);

$insertAfter = '/* Advanced settings next to bottom action buttons */';

$newBlock = <<<'CSS'

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

$paths = array(
    $root . '/couch/theme/garden/styles.css',
    $root . '/couch/addons/kfunctions.php',
);

$oldContentRule = '#content{background:#fff;min-height:calc(100vh - var(--gl-admin-sidebar-footer));padding:18px 24px 28px;border-top:0}';
$newContentRule = '#content{background:#fff;min-height:100vh;padding:18px 24px 0;border-top:0}';

$oldScrollRule = '#scroll-content{background:#f3f3f3}';
$newScrollRule = '#scroll-content{background:#fff}';

foreach ($paths as $path) {
    $content = file_get_contents($path);
    $original = $content;

    if (strpos($content, $oldContentRule) !== false) {
        $content = str_replace($oldContentRule, $newContentRule, $content);
    } else {
        $oldCrlf = str_replace("\n", "\r\n", $oldContentRule);
        $newCrlf = str_replace("\n", "\r\n", $newContentRule);
        if (strpos($content, $oldCrlf) !== false) {
            $content = str_replace($oldCrlf, $newCrlf, $content);
        }
    }

    if (strpos($content, $oldScrollRule) !== false) {
        $content = str_replace($oldScrollRule, $newScrollRule, $content);
    }

    if (strpos($content, '/* Full-height main panel + scroll-to-top placement */') === false) {
        $pos = strpos($content, $insertAfter);
        if ($pos === false) {
            fwrite(STDERR, basename($path) . ": anchor not found\n");
            exit(1);
        }
        $content = substr($content, 0, $pos) . rtrim($newBlock) . "\n" . substr($content, $pos);
    }

    if ($content === $original) {
        if (strpos($content, '/* Full-height main panel + scroll-to-top placement */') !== false) {
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
