<?php
$root = dirname(__DIR__);
$stylesPath = $root . '/couch/theme/garden/styles.css';
$kfnPath = $root . '/couch/addons/kfunctions.php';

$footerCss = <<<'CSS'
#sidebar-greeting,#sidebar-top{position:absolute!important;right:0;bottom:84px;left:0;z-index:2;box-sizing:border-box;border-top:1px solid #000;border-bottom:none;padding:10px 12px 8px;background-color:var(--gl-black)!important;box-shadow:0 -1px 0 rgba(197,160,89,.08)}
#sidebar-greeting>p,#sidebar-top>p{color:var(--gl-muted);margin:0;font-size:12px;line-height:1.45}
#sidebar-greeting>p>a,#sidebar-top>p>a{color:var(--gl-text)}
#sidebar-btns{position:absolute!important;right:0;bottom:24px;left:0;box-sizing:border-box;display:flex!important;align-items:stretch;height:60px!important;padding:11px 12px 10px!important;border-top:1px solid #000!important;background-color:var(--gl-black)!important}
#sidebar-btns>.btn{flex:1 1 0;width:auto!important;min-width:0;float:none!important}
#sidebar-btns>#log-out,#sidebar-btns>#view-site{width:auto!important}
CSS;

$footerCssKfn = str_replace(
    array('var(--gl-black)', 'var(--gl-muted)', 'var(--gl-text)'),
    array('#0a0a0a', '#999', '#ddd'),
    $footerCss
);

foreach (array($stylesPath, $kfnPath) as $path) {
    $content = file_get_contents($path);
    $content = preg_replace(
        '/#sidebar-greeting,#sidebar-top\{position:absolute!important;right:0;bottom:84px;left:0;z-index:2;[^}]+\}/',
        explode("\n", $footerCss)[0],
        $content
    );
    $content = preg_replace(
        '/#sidebar-btns\{position:absolute!important;right:0;bottom:24px;left:0;[^}]+\}/',
        explode("\n", $footerCss)[3],
        $content
    );
    $content = preg_replace(
        '/#sidebar-btns>#log-out\{width:\d+px!important\}\s*#sidebar-btns>#view-site\{width:\d+px!important\}/',
        "#sidebar-btns>.btn{flex:1 1 0;width:auto!important;min-width:0;float:none!important}\n#sidebar-btns>#log-out,#sidebar-btns>#view-site{width:auto!important}",
        $content
    );
    // Older duplicate blocks without !important
    $content = preg_replace(
        '/#sidebar-greeting,#sidebar-top\{position:absolute;right:0;bottom:84px;left:0;z-index:2;[^}]+\}/',
        str_replace('!important', '', explode("\n", $footerCss)[0]),
        $content
    );
    file_put_contents($path, $content);
}

passthru('php -l ' . escapeshellarg($kfnPath), $code);
echo "Aligned sidebar footer greeting and buttons\n";
exit($code);
