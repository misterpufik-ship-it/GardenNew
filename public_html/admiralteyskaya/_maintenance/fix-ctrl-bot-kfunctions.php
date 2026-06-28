<?php
$root = dirname(__DIR__);
$kfnPath = $root . '/couch/addons/kfunctions.php';

$old = <<<'CSS'
/* Advanced settings next to bottom action buttons */
.ctrl-bot{display:flex!important;flex-wrap:wrap;align-items:center;gap:10px 12px}
.ctrl-bot>#top{position:absolute!important;top:21px;right:0;margin:0}
.ctrl-bot #settings-panel{position:relative;flex:0 0 auto;margin:0;padding:0}
.ctrl-bot #settings-panel-toggle{position:static;top:auto;right:auto;margin:0}
.ctrl-bot #settings-panel>.panel-body{position:absolute;right:0;bottom:calc(100% + 6px);top:auto;float:none;width:min(440px,calc(100vw - 320px));z-index:5}
.ctrl-bot .ctrl-right{margin-left:auto}
CSS;

$new = <<<'CSS'
/* Advanced settings next to bottom action buttons */
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
  display:grid!important;
  grid-template-columns:minmax(0,1fr) auto minmax(0,1fr);
  align-items:center!important;
  gap:0 12px;
  font-size:12px!important;
  min-height:38px;
}
.ctrl-bot:has(#settings-panel)>#top{
  position:absolute!important;
  top:21px!important;
  right:0;
  margin:0;
}
.ctrl-bot:has(#settings-panel) #settings-panel{
  position:relative!important;
  grid-column:1;
  justify-self:start;
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
  right:0;
  bottom:calc(100% + 6px);
  top:auto!important;
  float:none!important;
  width:min(440px,calc(100vw - 320px));
  z-index:5;
}
.ctrl-bot:has(#settings-panel)>#btn_submit{
  grid-column:2;
  justify-self:center;
  margin:0!important;
  height:38px;
  line-height:36px;
  vertical-align:middle!important;
}
.ctrl-bot:has(#settings-panel)>#btn_view{
  grid-column:3;
  justify-self:end;
  margin:0!important;
  height:38px;
  line-height:36px;
  vertical-align:middle!important;
}
.ctrl-bot:has(#settings-panel)>.ctrl-right{
  grid-column:3;
  justify-self:end;
  margin:0!important;
}
.ctrl-bot:has(#settings-panel)>.btn:not(#top),
.ctrl-bot:has(#settings-panel)>a.btn{
  align-self:center;
  margin-top:0!important;
  margin-bottom:0!important;
}
CSS;

$kfn = file_get_contents($kfnPath);
$oldCrlf = str_replace("\n", "\r\n", $old);
$newCrlf = str_replace("\n", "\r\n", $new);
if (strpos($kfn, $oldCrlf) !== false) {
    $kfn = str_replace($oldCrlf, $newCrlf, $kfn);
} elseif (strpos($kfn, $old) !== false) {
    $kfn = str_replace($old, $new, $kfn);
} elseif (strpos($kfn, '.ctrl-bot:has(#settings-panel)') !== false) {
    echo "kfunctions.php already updated\n";
    exit(0);
} else {
    fwrite(STDERR, "old block not found in kfunctions.php\n");
    exit(1);
}
file_put_contents($kfnPath, $kfn);
passthru('php -l ' . escapeshellarg($kfnPath), $code);
echo "kfunctions.php updated\n";
exit($code ?? 0);
