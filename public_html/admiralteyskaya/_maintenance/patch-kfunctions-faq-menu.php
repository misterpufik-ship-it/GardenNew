<?php
/**
 * Fix FAQ admin submenu: Адмирал + Уделка under "Вопросы и ответы".
 * Run: php _maintenance/patch-kfunctions-faq-menu.php
 * Web: /admiralteyskaya/_maintenance/patch-kfunctions-faq-menu-web.php?token=gl-cache-clear-20260623
 */
$path = dirname(__DIR__) . '/couch/addons/kfunctions.php';
if (!is_file($path)) {
    fwrite(STDERR, "kfunctions.php not found: {$path}\n");
    exit(1);
}

$content = file_get_contents($path);
$changed = false;

if (strpos($content, "'_garden_faq_'") === false) {
    if (preg_match('/(\$items\[\'\_garden_udelnaya\_\'\]\s*=\s*garden_admin_menu_header\([^;]+;)/', $content, $m)) {
        $content = str_replace($m[1], $m[1] . "\n    \$items['_garden_faq_'] = garden_admin_menu_header( '_garden_faq_', 'Вопросы и ответы', 25 );", $content, $count);
        if ($count) {
            $changed = true;
            echo "Added _garden_faq_ header\n";
        }
    }
}

if (!preg_match("/'faq\\.php'\\s*=>\\s*array\\('field'=>'label_faq',\\s*'title'=>'Адмирал'/", $content)) {
    if (preg_match("/'faq\\.php'\\s*=>\\s*array\\('field'=>'label_faq',\\s*'title'=>'[^']*',\\s*'weight'=>\\d+\\),/", $content)) {
        $content = preg_replace(
            "/'faq\\.php'\\s*=>\\s*array\\('field'=>'label_faq',\\s*'title'=>'[^']*',\\s*'weight'=>\\d+\\),/",
            "'faq.php' => array('field'=>'label_faq', 'title'=>'Адмирал', 'weight'=>26),",
            $content,
            1,
            $count
        );
        if ($count) {
            $changed = true;
            echo "Updated faq.php menu label\n";
        }
    } elseif (strpos($content, "'faq.php'") === false) {
        $anchor = "'filial.php' => array('field'=>'label_filial', 'title'=>'Филиал', 'weight'=>200),";
        if (strpos($content, $anchor) !== false) {
            $line = "'faq.php' => array('field'=>'label_faq', 'title'=>'Адмирал', 'weight'=>26),\n        " . $anchor;
            $content = str_replace($anchor, $line, $content, $count);
            if ($count) {
                $changed = true;
                echo "Inserted faq.php defaults\n";
            }
        }
    }
}

if (!preg_match("/'udelnaya\\/faq\\.php'\\s*=>\\s*array\\('field'=>'label_u_faq',\\s*'title'=>'Уделка'/", $content)) {
    if (preg_match("/'udelnaya\\/faq\\.php'\\s*=>\\s*array\\('field'=>'label_u_faq',\\s*'title'=>'[^']*',\\s*'weight'=>\\d+\\),/", $content)) {
        $content = preg_replace(
            "/'udelnaya\\/faq\\.php'\\s*=>\\s*array\\('field'=>'label_u_faq',\\s*'title'=>'[^']*',\\s*'weight'=>\\d+\\),/",
            "'udelnaya/faq.php' => array('field'=>'label_u_faq', 'title'=>'Уделка', 'weight'=>27),",
            $content,
            1,
            $count
        );
        if ($count) {
            $changed = true;
            echo "Updated udelnaya/faq.php menu label\n";
        }
    } elseif (strpos($content, "'udelnaya/faq.php'") === false) {
        $anchor = "'udelnaya/filial.php' => array('field'=>'label_u_filial', 'title'=>'Филиал', 'weight'=>200),";
        if (strpos($content, $anchor) !== false) {
            $line = "'udelnaya/faq.php' => array('field'=>'label_u_faq', 'title'=>'Уделка', 'weight'=>27),\n        " . $anchor;
            $content = str_replace($anchor, $line, $content, $count);
            if ($count) {
                $changed = true;
                echo "Inserted udelnaya/faq.php defaults\n";
            }
        }
    }
}

$faqOverride = <<<'PHP'

            if ( in_array( $name, array( 'faq.php', 'udelnaya/faq.php' ), true ) ) {
                $items[$name]['parent'] = '_garden_faq_';
            }
PHP;

$content = preg_replace(
    '/elseif\s*\(\s*in_array\(\s*\$name,\s*array\(\s*\'faq\.php\',\s*\'udelnaya\/faq\.php\'\s*\),\s*true\s*\)\s*\)\{\s*\n\s*\$items\[\$name\]\[\'parent\'\]\s*=\s*\'\_garden\_faq\_\';\s*\n\s*\}/',
    '',
    $content,
    -1,
    $removedEarly
);
if ($removedEarly) {
    $changed = true;
    echo "Removed broken early FAQ elseif ({$removedEarly})\n";
}

if (strpos($content, "in_array( \$name, array( 'faq.php', 'udelnaya/faq.php' ), true )") === false) {
    $patterns = array(
        "/(\s*else\{\s*\n\s*\$items\[\$name\]\['parent'\]\s*=\s*'_garden_admiral_';\s*\n\s*\})/",
        "/(\s*else\s*\{\s*\n\s*\$items\[\$name\]\['parent'\]\s*=\s*'_garden_admiral_';\s*\n\s*\})/",
    );
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, '$1' . $faqOverride, $content, 1, $count);
            if ($count) {
                $changed = true;
                echo "Added FAQ parent override after admiral else\n";
                break;
            }
        }
    }
}

if (!$changed) {
    echo "No kfunctions changes needed\n";
} else {
    file_put_contents($path, $content);
    echo "Patched kfunctions.php\n";
}
