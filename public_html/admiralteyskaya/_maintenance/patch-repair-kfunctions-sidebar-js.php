<?php
/**
 * Fix broken garden_admin_sidebar_js after arrow-toggle patch.
 * Web: /admiralteyskaya/_maintenance/patch-repair-kfunctions-sidebar-js-web.php?token=gl-cache-clear-20260623
 */
$root = dirname(__DIR__);
$path = $root . '/couch/addons/kfunctions.php';
if (!is_file($path)) {
    fwrite(STDERR, "kfunctions.php not found\n");
    exit(1);
}

$content = file_get_contents($path);
$broken = <<<'PHP'
})(jQuery);
JS;

    $FUNCS->add_js( $js );
})(jQuery);
JS;

    $FUNCS->add_js( $js );
}
PHP;

$fixed = <<<'PHP'
})(jQuery);
JS;

    $FUNCS->add_js( $js );
}
PHP;

if (strpos($content, $broken) !== false) {
    $content = str_replace($broken, $fixed, $content);
    file_put_contents($path, $content);
    echo "Removed duplicate sidebar JS tail\n";
} else {
    echo "Duplicate sidebar JS tail not found (maybe already fixed)\n";
}

passthru('php -l ' . escapeshellarg($path), $code);
exit($code);
