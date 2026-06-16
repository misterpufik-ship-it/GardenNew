<?php
/**
 * Replace misterpufik.ru image URLs in CouchCMS text fields.
 * Run on server: php public_html/_maintenance/migrate-misterpufik-db.php
 */
$config = __DIR__ . '/../admiralteyskaya/couch/config.php';
if ( !file_exists($config) ) {
    fwrite(STDERR, "config.php not found\n");
    exit(1);
}

require_once $config;

$host = K_DB_HOST;
$port = 3306;
if ( strpos($host, ':') !== false ) {
    list($host, $port) = explode(':', $host, 2);
}

$db = new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
if ( $db->connect_errno ) {
    fwrite(STDERR, 'DB connect failed: ' . $db->connect_error . "\n");
    exit(1);
}
$db->set_charset('utf8mb4');

$replacements = array(
    '/img/logo3.webp' => 'https://garden-lounge.pro/img/logo3.webp',
    'http://misterpufik.ru/logo3.png' => 'https://garden-lounge.pro/img/logo3.webp',
    '/admiralteyskaya/couch/uploads/image/gf11.webp' => 'https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/gf11.webp',
    '/admiralteyskaya/couch/uploads/image/log4.webp' => 'https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/log4.webp',
    '/admiralteyskaya/couch/uploads/image/garden.webp' => 'https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/garden.webp',
);

for ( $i = 1; $i <= 6; $i++ ) {
    $replacements['https://misterpufik.ru/ga' . $i . '.jpg'] = 'https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/ga' . $i . '.webp';
}
for ( $i = 1; $i <= 12; $i++ ) {
    $replacements['https://misterpufik.ru/gf' . $i . '.jpg'] = 'https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/gf' . $i . '.webp';
}

$table = K_DB_TABLES_PREFIX . 'data_text';
$total = 0;

foreach ( $replacements as $from => $to ) {
    $fromEsc = $db->real_escape_string($from);
    $toEsc = $db->real_escape_string($to);
    $sql = "UPDATE {$table} SET value = REPLACE(value, '{$fromEsc}', '{$toEsc}') WHERE value LIKE '%{$fromEsc}%'";
    $db->query($sql);
    if ( $db->errno ) {
        fwrite(STDERR, "Error: {$db->error}\n");
        exit(1);
    }
    $count = $db->affected_rows;
    if ( $count > 0 ) {
        echo "{$from} -> {$count} row(s)\n";
        $total += $count;
    }
}

echo "Done. Updated {$total} row(s).\n";
