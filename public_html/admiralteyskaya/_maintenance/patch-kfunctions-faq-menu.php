<?php
/**
 * Group FAQ admin: parent "Вопросы и ответы" with children Адмирал + Уделка.
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

$replacements = array(
    "'faq.php' => array('field'=>'label_faq', 'title'=>'Вопросы и ответы', 'weight'=>205)," =>
        "'faq.php' => array('field'=>'label_faq', 'title'=>'Адмирал', 'weight'=>26),",
    "'udelnaya/faq.php' => array('field'=>'label_u_faq', 'title'=>'Вопросы и ответы', 'weight'=>205)," =>
        "'udelnaya/faq.php' => array('field'=>'label_u_faq', 'title'=>'Уделка', 'weight'=>27),",
);

foreach ($replacements as $old => $new) {
    if (strpos($content, $new) !== false) {
        continue;
    }
    if (strpos($content, $old) !== false) {
        $content = str_replace($old, $new, $content, $count);
        if ($count) {
            $changed = true;
            echo "Updated defaults entry\n";
        }
    }
}

if (strpos($content, "'title'=>'Адмирал', 'weight'=>26") === false &&
    preg_match("/'faq\\.php'\\s*=>\\s*array\\('field'=>'label_faq',\\s*'title'=>'[^']*',\\s*'weight'=>\\d+\\),/", $content)) {
    $content = preg_replace(
        "/'faq\\.php'\\s*=>\\s*array\\('field'=>'label_faq',\\s*'title'=>'[^']*',\\s*'weight'=>\\d+\\),/",
        "'faq.php' => array('field'=>'label_faq', 'title'=>'Адмирал', 'weight'=>26),",
        $content,
        1,
        $count
    );
    if ($count) {
        $changed = true;
        echo "Regex-updated faq.php label\n";
    }
}

if (strpos($content, "'title'=>'Уделка', 'weight'=>27") === false &&
    preg_match("/'udelnaya\\/faq\\.php'\\s*=>\\s*array\\('field'=>'label_u_faq',\\s*'title'=>'[^']*',\\s*'weight'=>\\d+\\),/", $content)) {
    $content = preg_replace(
        "/'udelnaya\\/faq\\.php'\\s*=>\\s*array\\('field'=>'label_u_faq',\\s*'title'=>'[^']*',\\s*'weight'=>\\d+\\),/",
        "'udelnaya/faq.php' => array('field'=>'label_u_faq', 'title'=>'Уделка', 'weight'=>27),",
        $content,
        1,
        $count
    );
    if ($count) {
        $changed = true;
        echo "Regex-updated udelnaya/faq.php label\n";
    }
}

if (strpos($content, "'_garden_faq_'") === false) {
    $needle = "\$items['_garden_udelnaya_'] = garden_admin_menu_header( '_garden_udelnaya_', 'Удельная', 1 );";
    if (strpos($content, $needle) === false) {
        $needle = "\$items['_garden_udelnaya_'] = garden_admin_menu_header( '_garden_udelnaya_',";
        if (preg_match('/\$items\[\'\_garden_udelnaya\_\'\] = garden_admin_menu_header\( \'_garden_udelnaya_\', \'[^\']*\', 1 \);/', $content, $m)) {
            $needle = $m[0];
        }
    }
    if ($needle && strpos($content, $needle) !== false) {
        $insert = $needle . "\n    \$items['_garden_faq_'] = garden_admin_menu_header( '_garden_faq_', 'Вопросы и ответы', 25 );";
        $content = str_replace($needle, $insert, $content, $count);
        if ($count) {
            $changed = true;
            echo "Added _garden_faq_ menu header\n";
        }
    } else {
        fwrite(STDERR, "Could not find udelnaya header anchor\n");
        exit(1);
    }
}

$faqParentRule = "elseif ( in_array( \$name, array( 'faq.php', 'udelnaya/faq.php' ), true ) ){\n                \$items[\$name]['parent'] = '_garden_faq_';\n            }";
if (strpos($content, "'faq.php', 'udelnaya/faq.php'") === false) {
    $anchors = array(
        "elseif ( strpos(\$name, 'udelnaya/') === 0 ){\n                \$items[\$name]['parent'] = '_garden_udelnaya_';",
        "if ( strpos(\$name, 'udelnaya/') === 0 ){\n                \$items[\$name]['parent'] = '_garden_udelnaya_';",
    );
    foreach ($anchors as $anchor) {
        if (strpos($content, $anchor) !== false) {
            $content = str_replace($anchor, $faqParentRule . "\n            " . $anchor, $content, $count);
            if ($count) {
                $changed = true;
                echo "Added FAQ parent routing rule\n";
                break;
            }
        }
    }
    if (!isset($count) || !$count) {
        fwrite(STDERR, "Could not insert FAQ parent rule\n");
        exit(1);
    }
}

if (!$changed) {
    echo "No changes needed\n";
    exit(0);
}

file_put_contents($path, $content);
echo "Patched kfunctions.php FAQ menu grouping\n";
