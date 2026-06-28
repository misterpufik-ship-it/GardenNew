<?php
$path = dirname(__DIR__) . '/couch/addons/kfunctions.php';
$content = file_get_contents($path);

$content = preg_replace(
    '/\}\)\(jQuery\);\r?\nJS;\r?\n\r?\n    \$FUNCS->add_js\( \$js \);\r?\n\}/',
    '}',
    $content,
    1,
    $count
);

if (!$count) {
    fwrite(STDERR, "sidebar js duplicate not found\n");
    exit(1);
}

if (strpos($content, 'login-remember') === false) {
    $content = str_replace(
        "#simple-page #simple-wrap .panel-heading.simple-heading .login-heading{display:block!important;width:100%!important;color:#C5A059!important;text-align:center!important;text-shadow:none!important;font-size:15px;letter-spacing:.04em}\nCSS;",
        "#simple-page #simple-wrap .panel-heading.simple-heading .login-heading{display:block!important;width:100%!important;color:#C5A059!important;text-align:center!important;text-shadow:none!important;font-size:15px;letter-spacing:.04em}\n#simple-page .login-remember{margin:0 0 14px;text-align:left}\n#simple-page .login-remember label{display:inline-flex;align-items:center;gap:8px;color:#bbb;font-size:12px;font-weight:500;cursor:pointer;margin:0}\n#simple-page .login-remember input[type=checkbox]{margin:0}\nCSS;",
        $content,
        $count2
    );
    echo "login css updated: {$count2}\n";
}

file_put_contents($path, $content);
passthru('php -l ' . escapeshellarg($path), $code);
exit($code);
