<?php
$root = dirname(__DIR__);
$marker = '/* Advanced settings next to bottom action buttons */';

$newBlock = <<<'CSS'
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

foreach (array(
    $root . '/couch/theme/garden/styles.css',
    $root . '/couch/addons/kfunctions.php',
) as $path) {
    $content = file_get_contents($path);
    $start = strpos($content, $marker);
    if ($start === false) {
        fwrite(STDERR, basename($path) . ": marker missing\n");
        exit(1);
    }
    if (!preg_match('/\/\* Advanced settings next to bottom action buttons \*\/[\s\S]*?(?=\n\/\*|$)/', $content, $m, 0, $start)) {
        fwrite(STDERR, basename($path) . ": block parse failed\n");
        exit(1);
    }
    $content = substr($content, 0, $start) . $newBlock . substr($content, $start + strlen($m[0]));
    file_put_contents($path, $content);
    echo "Updated " . basename($path) . "\n";
}

passthru('php -l ' . escapeshellarg($root . '/couch/addons/kfunctions.php'), $code);
exit($code ?? 0);
