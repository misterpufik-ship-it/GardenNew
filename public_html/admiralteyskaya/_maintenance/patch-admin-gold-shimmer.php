<?php
$root = dirname(__DIR__);
$stylesPath = $root . '/couch/theme/garden/styles.css';
$kfunctionsPath = $root . '/couch/addons/kfunctions.php';

$shimmerCss = <<<'CSS'
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
.group-wrapper .panel-heading.panel-toggle,fieldset.row_fieldset legend{font-size:18px!important}
.group-wrapper .panel-heading.panel-toggle:after,fieldset.row_fieldset legend:after{
-webkit-text-fill-color:initial;color:var(--gl-gold)!important;background:none!important;animation:none!important}
.group-wrapper .panel-heading.panel-toggle:hover:after,.group-wrapper .panel-heading.panel-toggle:focus:after,
fieldset.row_fieldset legend:hover:after,fieldset.row_fieldset legend:focus:after{background-color:var(--gl-gold)!important;color:#111!important}

CSS;

$styles = file_get_contents($stylesPath);
$styles = preg_replace('/@keyframes glGoldShine\{.*?\}\s*\.gl-gold-shimmer.*?(?=\n#nav>\.nav-heading:first-child|\n\/\*|$)/s', '', $styles);
$styles = preg_replace(
    '/(#nav>\.nav-heading:first-child:before\{padding-top:0;border-top:0\})/',
    $shimmerCss . '$1',
    $styles,
    1,
    $count
);
if (!$count) {
    $styles = preg_replace(
        '/(\.nav-heading,\.nav-heading-toggle\{font-family:)/',
        $shimmerCss . '$1',
        $styles,
        1,
        $count2
    );
}
$styles = str_replace('.nav-heading-toggle{color:var(--gl-gold)}', '.nav-heading-toggle{color:transparent}', $styles);
$styles = str_replace('#header-title,#header-title a{color:var(--gl-gold)!important;text-shadow:none!important}', '', $styles);
$styles = str_replace('.group-wrapper .panel-heading.panel-toggle,fieldset.row_fieldset legend{color:var(--gl-gold)!important;', '.group-wrapper .panel-heading.panel-toggle,fieldset.row_fieldset legend{', $styles);
$styles = str_replace('.group-wrapper .panel-heading.panel-toggle:hover,.group-wrapper .panel-heading.panel-toggle:focus{color:#d4b06a!important}', '', $styles);
file_put_contents($stylesPath, $styles);

$newTypography = <<<'PHP'
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
PHP;

$kfunctions = file_get_contents($kfunctionsPath);
$kfunctions = preg_replace('/function garden_admin_typography_css\(\)\{.*?\n\}/s', trim($newTypography), $kfunctions, 1, $c1);
if (!$c1) {
    fwrite(STDERR, "kfunctions typography replace failed\n");
    exit(1);
}
file_put_contents($kfunctionsPath, $kfunctions);

passthru('php -l ' . escapeshellarg($kfunctionsPath), $code);
echo "Patched gold shimmer typography\n";
exit($code);
