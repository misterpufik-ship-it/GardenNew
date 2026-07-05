<?php
/**
 * Move FAQ items into branch menus (Admiral + Udelnaya), remove shared FAQ group.
 * Run: php _maintenance/patch-kfunctions-faq-branches.php
 * Web: /admiralteyskaya/_maintenance/patch-kfunctions-faq-branches-web.php?token=gl-cache-clear-20260623
 */
$path = dirname(__DIR__) . '/couch/addons/kfunctions.php';
if (!is_file($path)) {
    fwrite(STDERR, "kfunctions.php not found: {$path}\n");
    exit(1);
}

$content = file_get_contents($path);
$changed = false;

$content = preg_replace(
    "/\n\\s*\\\$items\\['_garden_faq_'\\]\\s*=\\s*garden_admin_menu_header\\([^;]+;\\s*/",
    "\n",
    $content,
    -1,
    $count
);
if ($count) {
    $changed = true;
    echo "Removed _garden_faq_ header ({$count})\n";
}

$content = preg_replace(
    '/function garden_faq_admin_menu_fix\( &\$items \)\{.*?\n\}\s*\n\s*\$FUNCS->add_event_listener\(\s*\'alter_admin_menuitems\',\s*\'garden_faq_admin_menu_fix\'\s*\);\s*/s',
    '',
    $content,
    -1,
    $count
);
if ($count) {
    $changed = true;
    echo "Removed garden_faq_admin_menu_fix listener ({$count})\n";
}

$content = preg_replace(
    '/\s*if\s*\(\s*in_array\(\s*\$name,\s*array\(\s*\'faq\.php\',\s*\'udelnaya\/faq\.php\'\s*\),\s*true\s*\)\s*\)\{\s*\n\s*\$items\[\$name\]\[\'parent\'\]\s*=\s*\'_garden_faq_\';\s*\n\s*\}/',
    '',
    $content,
    -1,
    $count
);
if ($count) {
    $changed = true;
    echo "Removed FAQ parent override to _garden_faq_ ({$count})\n";
}

$content = preg_replace(
    '/elseif\s*\(\s*in_array\(\s*\$name,\s*array\(\s*\'faq\.php\',\s*\'udelnaya\/faq\.php\'\s*\),\s*true\s*\)\s*\)\{\s*\n\s*\$items\[\$name\]\[\'parent\'\]\s*=\s*\'_garden_faq_\';\s*\n\s*\}/',
    '',
    $content,
    -1,
    $count
);
if ($count) {
    $changed = true;
    echo "Removed early FAQ elseif ({$count})\n";
}

$faqDefaults = array(
    "'faq.php' => array('field'=>'label_faq', 'title'=>'Вопросы и ответы', 'weight'=>175)," =>
        "/'faq\\.php'\\s*=>\\s*array\\('field'=>'label_faq',\\s*'title'=>'[^']*',\\s*'weight'=>\\d+\\),/",
    "'udelnaya/faq.php' => array('field'=>'label_u_faq', 'title'=>'Вопросы и ответы', 'weight'=>175)," =>
        "/'udelnaya\\/faq\\.php'\\s*=>\\s*array\\('field'=>'label_u_faq',\\s*'title'=>'[^']*',\\s*'weight'=>\\d+\\),/",
);

foreach ($faqDefaults as $replacement => $pattern) {
    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, $replacement, $content, 1, $count);
        if ($count) {
            $changed = true;
            echo "Updated FAQ default: {$replacement}\n";
        }
    }
}

if (strpos($content, "'faq.php'") === false) {
    $anchor = "'gallery.php' => array('field'=>'label_gallery', 'title'=>'Галерея', 'weight'=>170),";
    if (strpos($content, $anchor) !== false) {
        $line = "'faq.php' => array('field'=>'label_faq', 'title'=>'Вопросы и ответы', 'weight'=>175),\n        " . $anchor;
        $content = str_replace($anchor, $line, $content, $count);
        if ($count) {
            $changed = true;
            echo "Inserted faq.php default\n";
        }
    }
}

if (strpos($content, "'udelnaya/faq.php'") === false) {
    $anchor = "'udelnaya/gallery.php' => array('field'=>'label_u_gallery', 'title'=>'Галерея', 'weight'=>170),";
    if (strpos($content, $anchor) !== false) {
        $line = "'udelnaya/faq.php' => array('field'=>'label_u_faq', 'title'=>'Вопросы и ответы', 'weight'=>175),\n        " . $anchor;
        $content = str_replace($anchor, $line, $content, $count);
        if ($count) {
            $changed = true;
            echo "Inserted udelnaya/faq.php default\n";
        }
    }
}

if (!$changed) {
    echo "No kfunctions changes needed\n";
} else {
    file_put_contents($path, $content);
    echo "Patched kfunctions.php\n";
}
