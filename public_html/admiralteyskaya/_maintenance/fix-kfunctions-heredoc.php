<?php
$path = dirname(__DIR__) . '/couch/addons/kfunctions.php';
$content = file_get_contents($path);

$fixed = preg_replace(
    '/background:#fff\}CSS;\s*\n\s*\$FUNCS->add_css\( \$css \);/s',
    "background:#fff}\nCSS;\n\n    \$FUNCS->add_css( \$css );",
    $content,
    1,
    $count
);
if (!$count) {
    fwrite(STDERR, "heredoc pattern not found\n");
    exit(1);
}
file_put_contents($path, $fixed);
passthru('php -l ' . escapeshellarg($path), $code);
echo "Fixed garden_admin_sidebar_css heredoc terminator\n";
exit($code);
