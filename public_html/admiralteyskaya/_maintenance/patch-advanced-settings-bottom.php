<?php
$root = dirname(__DIR__);
require __DIR__ . '/patch-content-form-advanced.php';

$css = <<<'CSS'

/* Advanced settings next to bottom action buttons */
.ctrl-bot{display:flex!important;flex-wrap:wrap;align-items:center;gap:10px 12px}
.ctrl-bot>#top{position:absolute!important;top:21px;right:0;margin:0}
.ctrl-bot #settings-panel{position:relative;flex:0 0 auto;margin:0;padding:0}
.ctrl-bot #settings-panel-toggle{position:static;top:auto;right:auto;margin:0}
.ctrl-bot #settings-panel>.panel-body{position:absolute;right:0;bottom:calc(100% + 6px);top:auto;float:none;width:min(440px,calc(100vw - 320px));z-index:5}
.ctrl-bot .ctrl-right{margin-left:auto}
CSS;

foreach (array(
    $root . '/couch/theme/garden/styles.css',
) as $path) {
    $content = file_get_contents($path);
    if (strpos($content, '/* Advanced settings next to bottom action buttons */') !== false) {
        echo "styles.css already patched\n";
        continue;
    }
    file_put_contents($path, rtrim($content) . "\n" . $css);
    echo "Appended advanced settings ctrl-bot CSS to styles.css\n";
}

$kfnPath = $root . '/couch/addons/kfunctions.php';
$kfn = file_get_contents($kfnPath);
$kfnCss = str_replace('var(--gl-black)', '#0a0a0a', $css);
if (strpos($kfn, '/* Advanced settings next to bottom action buttons */') === false) {
    $kfn = preg_replace(
        '/(\$FUNCS->add_css\( \$css \);\s*\}\s*\n\$FUNCS->add_event_listener\( \'add_admin_css\', \'garden_admin_sidebar_css\' \);)/',
        "\$FUNCS->add_css( \$css );\n}\n\nfunction garden_admin_form_css(){\n    global \$FUNCS;\n\n    \$css = <<<'CSS'\n" . trim($kfnCss) . "\nCSS;\n\n    \$FUNCS->add_css( \$css );\n}\n\n\$FUNCS->add_event_listener( 'add_admin_css', 'garden_admin_form_css' );",
        $kfn,
        1,
        $count
    );
    if (!$count) {
        fwrite(STDERR, "kfunctions form css insert failed\n");
        exit(1);
    }
    file_put_contents($kfnPath, $kfn);
    passthru('php -l ' . escapeshellarg($kfnPath), $code);
    echo "Added garden_admin_form_css to kfunctions.php\n";
    exit($code ?? 0);
}

echo "Done\n";
