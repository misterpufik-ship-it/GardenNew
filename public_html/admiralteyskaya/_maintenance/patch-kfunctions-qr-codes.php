<?php
/**
 * Add qr-codes.php to Couch admin as a separate "QR коды" section.
 * Run on server: php public_html/admiralteyskaya/_maintenance/patch-kfunctions-qr-codes.php
 */
$path = dirname(__DIR__) . '/couch/addons/kfunctions.php';
if (!is_file($path)) {
    fwrite(STDERR, "kfunctions.php not found: {$path}\n");
    exit(1);
}
$content = file_get_contents($path);
if ($content === false) {
    fwrite(STDERR, "Cannot read kfunctions.php\n");
    exit(1);
}
$changed = false;

if (strpos($content, "'qr-codes.php'") === false) {
    $insertAfter = "'admin-instructions.php' => array('field'=>'', 'title'=>'Инструкции', 'weight'=>-10),";
    if (strpos($content, $insertAfter) === false) {
        $insertAfter = "return array(\n        ";
        $line = "'qr-codes.php' => array('field'=>'label_qr_codes', 'title'=>'QR коды', 'weight'=>1),\n        ";
        $content = preg_replace(
            '/return array\(\s*/',
            "return array(\n        " . $line,
            $content,
            1,
            $count
        );
        if (!$count) {
            fwrite(STDERR, "Could not insert qr-codes.php into garden_admin_label_defaults\n");
            exit(1);
        }
        $changed = true;
        echo "Added qr-codes.php to garden_admin_label_defaults() via array start\n";
    } else {
        $line = "'qr-codes.php' => array('field'=>'label_qr_codes', 'title'=>'QR коды', 'weight'=>1),\n        " . $insertAfter;
        $content = str_replace($insertAfter, $line, $content, $count);
        if (!$count) {
            fwrite(STDERR, "Could not insert qr-codes.php label\n");
            exit(1);
        }
        $changed = true;
        echo "Added qr-codes.php to garden_admin_label_defaults()\n";
    }
} else {
    echo "qr-codes.php already in label defaults\n";
}

if (strpos($content, "'_garden_qr_'") === false) {
    if (!preg_match("/\\\$items\\['_garden_udelnaya_'\\]\\s*=\\s*garden_admin_menu_header\\([^;]+;/", $content, $m)) {
        fwrite(STDERR, "Could not find _garden_udelnaya_ header to insert QR section\n");
        exit(1);
    }
    $insert = $m[0] . "\n    \$items['_garden_qr_'] = garden_admin_menu_header( '_garden_qr_', 'QR коды', 2 );";
    $content = str_replace($m[0], $insert, $content, $count);
    if (!$count) {
        fwrite(STDERR, "Failed to insert _garden_qr_ header\n");
        exit(1);
    }
    $changed = true;
    echo "Added _garden_qr_ menu header\n";
} else {
    echo "QR menu header already present\n";
}

if (strpos($content, "\$items['_templates_']['weight'] = 2;") !== false && strpos($content, "'_garden_qr_'") !== false) {
    $content = str_replace("\$items['_templates_']['weight'] = 2;", "\$items['_templates_']['weight'] = 3;", $content, $count);
    if ($count) {
        $changed = true;
        echo "Moved Общие after QR section\n";
    }
}

if (strpos($content, "\$name === 'qr-codes.php'") === false) {
    $old = "if ( \$name === 'admin-instructions.php' ){\n                \$items[\$name]['parent'] = '_garden_instructions_';\n            }";
    $new = $old . "\n            elseif ( \$name === 'qr-codes.php' ){\n                \$items[\$name]['parent'] = '_garden_qr_';\n            }";
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $new, $content, $count);
        if ($count) {
            $changed = true;
            echo "Set qr-codes.php parent to QR section\n";
        }
    } else {
        $fallback = "if ( strpos(\$name, 'udelnaya/') === 0 ){";
        $insert = "if ( \$name === 'qr-codes.php' ){\n                \$items[\$name]['parent'] = '_garden_qr_';\n            }\n            elseif ( strpos(\$name, 'udelnaya/') === 0 ){";
        if (strpos($content, $fallback) === false) {
            fwrite(STDERR, "Could not insert qr-codes.php parent assignment\n");
            exit(1);
        }
        $content = str_replace($fallback, $insert, $content, $count);
        if (!$count) {
            fwrite(STDERR, "Failed to insert qr-codes.php parent assignment\n");
            exit(1);
        }
        $changed = true;
        echo "Set qr-codes.php parent via udelnaya fallback\n";
    }
} else {
    echo "qr-codes.php parent already set\n";
}

if (strpos($content, 'function garden_qr_admin_assets_js') === false) {
    $assets = <<<'PHP'

function garden_qr_admin_assets_css(){
    global $FUNCS;
    $ver = @filemtime( K_SITE_DIR . '../qr/admin.css' );
    if ( !$ver ) $ver = time();
    $FUNCS->add_css( '@import url("/qr/admin.css?v=' . intval($ver) . '");' );
    $FUNCS->add_css( 'body.gl-qr-admin-page #scroll-content .ctrl-bot,body.gl-qr-admin-page .ctrl-bot,body.gl-qr-admin-page #advanced-settings{display:none!important}' );
}

function garden_qr_admin_assets_js(){
    global $FUNCS;
    $ver = @filemtime( K_SITE_DIR . '../qr/admin.js' );
    if ( !$ver ) $ver = time();
    $js = '(function($){$(function(){var params=new URLSearchParams(window.location.search||"");if(params.get("o")!=="qr-codes.php")return;document.body.classList.add("gl-qr-admin-page");if(!document.getElementById("gl-qr-root")){var host=document.getElementById("scroll-content")||document.body;var box=document.createElement("div");box.id="gl-qr-root";host.insertBefore(box,host.firstChild);}if(!document.querySelector("script[src^=\'/qr/admin.js\']")){var s=document.createElement("script");s.src="/qr/admin.js?v=' . intval($ver) . '";document.body.appendChild(s);}});})(jQuery);';
    $FUNCS->add_js( $js );
}

$FUNCS->add_event_listener( 'add_admin_css', 'garden_qr_admin_assets_css' );
$FUNCS->add_event_listener( 'add_admin_js', 'garden_qr_admin_assets_js' );
PHP;
    $content = rtrim($content) . "\n" . $assets . "\n";
    $changed = true;
    echo "Added QR admin assets loader\n";
} else {
    echo "QR admin assets already present\n";
}

if (!$changed) {
    echo "No kfunctions changes needed\n";
} else {
    if (file_put_contents($path, $content) === false) {
        fwrite(STDERR, "Failed to write kfunctions.php\n");
        exit(1);
    }
    echo "Patched kfunctions.php for QR codes\n";
}
