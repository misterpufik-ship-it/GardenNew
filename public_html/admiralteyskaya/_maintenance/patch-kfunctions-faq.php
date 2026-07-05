<?php
/**
 * Add faq.php templates to Couch admin menu.
 * Run on server: php public_html/admiralteyskaya/_maintenance/patch-kfunctions-faq.php
 */
$path = dirname(__DIR__) . '/couch/addons/kfunctions.php';
if (!is_file($path)) {
    fwrite(STDERR, "kfunctions.php not found: {$path}\n");
    exit(1);
}
$content = file_get_contents($path);
$changed = false;

$entries = array(
    array(
        'name' => 'faq.php',
        'insertAfter' => "'filial.php' => array('field'=>'label_filial', 'title'=>'Филиал', 'weight'=>200),",
        'line' => "'faq.php' => array('field'=>'label_faq', 'title'=>'Вопросы и ответы', 'weight'=>205),",
    ),
    array(
        'name' => 'udelnaya/faq.php',
        'insertAfter' => "'udelnaya/filial.php' => array('field'=>'label_u_filial', 'title'=>'Филиал', 'weight'=>200),",
        'line' => "'udelnaya/faq.php' => array('field'=>'label_u_faq', 'title'=>'Вопросы и ответы', 'weight'=>205),",
    ),
);

foreach ($entries as $entry) {
    if (strpos($content, "'{$entry['name']}'") !== false) {
        echo "{$entry['name']} already present\n";
        continue;
    }
    if (strpos($content, $entry['insertAfter']) === false) {
        fwrite(STDERR, "Insert anchor not found for {$entry['name']}\n");
        exit(1);
    }
    $insertLine = $entry['line'] . "\n        " . $entry['insertAfter'];
    $content = str_replace($entry['insertAfter'], $insertLine, $content, $count);
    if ($count) {
        $changed = true;
        echo "Added {$entry['name']} to garden_admin_label_defaults()\n";
    }
}

if (!$changed) {
    echo "No changes needed\n";
    exit(0);
}

file_put_contents($path, $content);
echo "Patched kfunctions.php\n";
