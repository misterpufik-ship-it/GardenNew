<?php
/**
 * Login page favicon: garden/simple.html + stronger icon regex in kfunctions.
 */
$root = dirname(__DIR__);

$simplePath = $root . '/couch/theme/garden/simple.html';
$systemSimple = $root . '/couch/theme/_system/simple.html';
if (!is_file($simplePath)) {
    copy($systemSimple, $simplePath);
}
$simple = file_get_contents($simplePath);
$simple = str_replace(
    '<link href="<cms:show k_system_theme_link />includes/admin/images/favicon.ico" rel="shortcut icon"/>',
    '<link rel="icon" type="image/png" href="/favicon.png">' . "\n    "
        . '<link rel="shortcut icon" type="image/png" href="/favicon.png">',
    $simple,
    $c1
);
if (!$c1 && strpos($simple, '/favicon.png') === false) {
    fwrite(STDERR, "simple.html favicon line not found\n");
    exit(1);
}
file_put_contents($simplePath, $simple);

$kfnPath = $root . '/couch/addons/kfunctions.php';
$kfn = file_get_contents($kfnPath);
$kfn = str_replace(
    "    \$html = preg_replace( '#<link[^>]+rel=[\"\\'](?:shortcut )?icon[\"\\'][^>]*/>#i', '', \$html );",
    "    \$html = preg_replace( '#<link[^>]*\\brel\\s*=\\s*[\"\\'](?:shortcut\\s+)?icon[\"\\'][^>]*/?>#i', '', \$html );",
    $kfn,
    $c2
);
if (!$c2) {
    if (strpos($kfn, 'shortcut\\s+)?icon') !== false) {
        echo "kfunctions icon regex already updated\n";
    } else {
        fwrite(STDERR, "kfunctions icon regex not replaced\n");
        exit(1);
    }
} else {
    file_put_contents($kfnPath, $kfn);
}

passthru('php -l ' . escapeshellarg($kfnPath), $code);
echo "Patched garden/simple.html and kfunctions.php\n";
exit($code ?: 0);
