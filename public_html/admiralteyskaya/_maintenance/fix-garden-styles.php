<?php
$path = dirname(__DIR__) . '/couch/theme/garden/styles.css';
$c = file_get_contents($path);

$c = preg_replace(
    '/#scroll-sidebar\{top:158px!important\}\s*@media \(max-height:540px\)\{#scroll-sidebar\{top:138px!important\}\}/',
    '',
    $c,
    1,
    $count
);

if (!$count) {
    fwrite(STDERR, "duplicate scroll-sidebar not removed\n");
    exit(1);
}

if (strpos($c, '.login-remember') === false) {
    $c = str_replace(
        "/* Login page */",
        "/* Login page */\n#simple-page .login-remember{margin:0 0 14px;text-align:left}\n#simple-page .login-remember label{display:inline-flex;align-items:center;gap:8px;color:#bbb;font-size:12px;font-weight:500;cursor:pointer;margin:0}\n#simple-page .login-remember input[type=checkbox]{margin:0}",
        $c,
        $count2
    );
}

file_put_contents($path, $c);
echo "styles.css cleaned\n";
