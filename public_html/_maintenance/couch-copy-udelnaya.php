<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

$config = __DIR__ . '/../admiralteyskaya/couch/config.php';
if (!is_file($config)) {
    fwrite(STDERR, "CouchCMS config not found: {$config}\n");
    exit(1);
}

define('K_COUCH_DIR', dirname($config) . '/');
require_once $config;

$host = K_DB_HOST;
$port = ini_get('mysqli.default_port') ?: 3306;
if (strpos($host, ':') !== false) {
    list($host, $port) = explode(':', $host, 2);
}

$db = new mysqli($host, K_DB_USER, K_DB_PASSWORD, K_DB_NAME, (int)$port);
if ($db->connect_errno) {
    fwrite(STDERR, "DB connection failed: {$db->connect_error}\n");
    exit(1);
}
$db->set_charset('utf8');

$tables = [
    'templates' => K_DB_TABLES_PREFIX . 'couch_templates',
    'fields' => K_DB_TABLES_PREFIX . 'couch_fields',
    'pages' => K_DB_TABLES_PREFIX . 'couch_pages',
    'text' => K_DB_TABLES_PREFIX . 'couch_data_text',
    'numeric' => K_DB_TABLES_PREFIX . 'couch_data_numeric',
];

function table_columns($db, $table) {
    $cols = [];
    $res = $db->query("SHOW COLUMNS FROM `{$table}`");
    while ($row = $res->fetch_assoc()) {
        $cols[] = $row['Field'];
    }
    return $cols;
}

function one($db, $sql) {
    $res = $db->query($sql);
    if (!$res) {
        throw new RuntimeException($db->error);
    }
    $row = $res->fetch_assoc();
    return $row ?: null;
}

function q($db, $value) {
    if ($value === null) return 'NULL';
    return "'" . $db->real_escape_string((string)$value) . "'";
}

function insert_row($db, $table, $row, $skip = array()) {
    foreach ($skip as $key) {
        unset($row[$key]);
    }
    $cols = array_keys($row);
    $values = array();
    foreach (array_values($row) as $value) {
        $values[] = q($db, $value);
    }
    $sql = "INSERT INTO `{$table}` (`" . implode('`,`', $cols) . "`) VALUES (" . implode(',', $values) . ")";
    if (!$db->query($sql)) {
        throw new RuntimeException($db->error . "\n" . $sql);
    }
    return (int)$db->insert_id;
}

function update_row($db, $table, $row, $where, $skip = array()) {
    foreach ($skip as $key) {
        unset($row[$key]);
    }
    $sets = [];
    foreach ($row as $key => $value) {
        $sets[] = "`{$key}`=" . q($db, $value);
    }
    $sql = "UPDATE `{$table}` SET " . implode(',', $sets) . " WHERE {$where}";
    if (!$db->query($sql)) {
        throw new RuntimeException($db->error . "\n" . $sql);
    }
}

function ensure_template($db, $tables, $sourceName, $targetName) {
    $source = one($db, "SELECT * FROM `{$tables['templates']}` WHERE name=" . q($db, $sourceName) . " AND deleted='0' LIMIT 1");
    if (!$source) {
        throw new RuntimeException("Source template not found: {$sourceName}");
    }

    $target = one($db, "SELECT * FROM `{$tables['templates']}` WHERE name=" . q($db, $targetName) . " LIMIT 1");
    if (!$target) {
        $new = $source;
        $new['name'] = $targetName;
        $new['title'] = trim(($new['title'] ?: $targetName) . ' — Удельная');
        $targetId = insert_row($db, $tables['templates'], $new, ['id']);
    } else {
        $targetId = (int)$target['id'];
        $new = $source;
        $new['id'] = $targetId;
        $new['name'] = $targetName;
        $new['title'] = trim(($new['title'] ?: $targetName) . ' — Удельная');
        update_row($db, $tables['templates'], $new, "id={$targetId}", ['id']);
    }

    $target = one($db, "SELECT * FROM `{$tables['templates']}` WHERE id={$targetId} LIMIT 1");
    return [$source, $target];
}

function ensure_master_page($db, $tables, $sourceTpl, $targetTpl) {
    $source = one($db, "SELECT * FROM `{$tables['pages']}` WHERE template_id=" . (int)$sourceTpl['id'] . " AND is_master='1' LIMIT 1");
    if (!$source) {
        throw new RuntimeException("Source master page not found: {$sourceTpl['name']}");
    }
    $target = one($db, "SELECT * FROM `{$tables['pages']}` WHERE template_id=" . (int)$targetTpl['id'] . " AND is_master='1' LIMIT 1");
    if (!$target) {
        $new = $source;
        $new['template_id'] = (int)$targetTpl['id'];
        $new['page_title'] = 'Default page for ' . $targetTpl['name'] . ' * PLEASE CHANGE THIS TITLE *';
        $new['page_name'] = preg_replace('/[^a-z0-9-]+/', '-', strtolower(str_replace(['/', '.php'], ['-', ''], $targetTpl['name'])));
        $targetId = insert_row($db, $tables['pages'], $new, ['id']);
    } else {
        $targetId = (int)$target['id'];
    }
    return [$source, one($db, "SELECT * FROM `{$tables['pages']}` WHERE id={$targetId} LIMIT 1")];
}

function sync_fields_and_data($db, $tables, $sourceTpl, $targetTpl, $sourcePage, $targetPage) {
    $sourceFields = $db->query("SELECT * FROM `{$tables['fields']}` WHERE template_id=" . (int)$sourceTpl['id'] . " ORDER BY id");
    while ($sourceField = $sourceFields->fetch_assoc()) {
        $targetField = one($db, "SELECT * FROM `{$tables['fields']}` WHERE template_id=" . (int)$targetTpl['id'] . " AND name=" . q($db, $sourceField['name']) . " LIMIT 1");
        if (!$targetField) {
            $newField = $sourceField;
            $newField['template_id'] = (int)$targetTpl['id'];
            $targetFieldId = insert_row($db, $tables['fields'], $newField, ['id']);
        } else {
            $targetFieldId = (int)$targetField['id'];
            $newField = $sourceField;
            $newField['id'] = $targetFieldId;
            $newField['template_id'] = (int)$targetTpl['id'];
            update_row($db, $tables['fields'], $newField, "id={$targetFieldId}", ['id']);
        }

        foreach (['text', 'numeric'] as $kind) {
            $sourceData = one($db, "SELECT * FROM `{$tables[$kind]}` WHERE page_id=" . (int)$sourcePage['id'] . " AND field_id=" . (int)$sourceField['id'] . " LIMIT 1");
            if (!$sourceData) continue;
            $exists = one($db, "SELECT * FROM `{$tables[$kind]}` WHERE page_id=" . (int)$targetPage['id'] . " AND field_id={$targetFieldId} LIMIT 1");
            $newData = $sourceData;
            $newData['page_id'] = (int)$targetPage['id'];
            $newData['field_id'] = $targetFieldId;
            if ($exists) {
                update_row($db, $tables[$kind], $newData, "page_id=" . (int)$targetPage['id'] . " AND field_id={$targetFieldId}");
            } else {
                insert_row($db, $tables[$kind], $newData);
            }
        }
    }
}

function set_field_text($db, $tables, $templateName, $fieldName, $value, $search = '') {
    $tpl = one($db, "SELECT id FROM `{$tables['templates']}` WHERE name=" . q($db, $templateName) . " LIMIT 1");
    if (!$tpl) return;
    $page = one($db, "SELECT id FROM `{$tables['pages']}` WHERE template_id=" . (int)$tpl['id'] . " AND is_master='1' LIMIT 1");
    $field = one($db, "SELECT id FROM `{$tables['fields']}` WHERE template_id=" . (int)$tpl['id'] . " AND name=" . q($db, $fieldName) . " LIMIT 1");
    if (!$page || !$field) return;
    $exists = one($db, "SELECT page_id FROM `{$tables['text']}` WHERE page_id=" . (int)$page['id'] . " AND field_id=" . (int)$field['id'] . " LIMIT 1");
    $row = ['page_id' => (int)$page['id'], 'field_id' => (int)$field['id'], 'value' => $value, 'search_value' => $search ?: $value];
    if ($exists) {
        update_row($db, $tables['text'], $row, "page_id=" . (int)$page['id'] . " AND field_id=" . (int)$field['id']);
    } else {
        insert_row($db, $tables['text'], $row);
    }
}

$map = [
    'index.php' => 'udelnaya/index.php',
    'header.php' => 'udelnaya/header.php',
    'about.php' => 'udelnaya/about.php',
    'gallery.php' => 'udelnaya/gallery.php',
    'menu.php' => 'udelnaya/menu.php',
    'akzii.php' => 'udelnaya/akzii.php',
    'reservation.php' => 'udelnaya/reservation.php',
    'contacts.php' => 'udelnaya/contacts.php',
    'filial.php' => 'udelnaya/filial.php',
    'globals.php' => 'udelnaya/globals.php',
    'menu/text/index.php' => 'udelnaya/menu/text/index.php',
    'menu/visual/index.php' => 'udelnaya/menu/visual/index.php',
    'menu/english/index.php' => 'udelnaya/menu/english/index.php',
];

$db->begin_transaction();
try {
    foreach ($map as $sourceName => $targetName) {
        list($sourceTpl, $targetTpl) = ensure_template($db, $tables, $sourceName, $targetName);
        list($sourcePage, $targetPage) = ensure_master_page($db, $tables, $sourceTpl, $targetTpl);
        sync_fields_and_data($db, $tables, $sourceTpl, $targetTpl, $sourcePage, $targetPage);
        echo "Synced {$sourceName} -> {$targetName}\n";
    }

    set_field_text($db, $tables, 'udelnaya/globals.php', 'seo_title_default', 'Garden Lounge на Удельной — кальянная и лаунж-бар у метро Удельная');
    set_field_text($db, $tables, 'udelnaya/globals.php', 'seo_desc_default', 'Garden Lounge на ул. Аккуратова 13: кальяны, кухня, бар, VIP-комнаты, PS5 и бронирование столика у метро Удельная. Тел. +7 995 624-68-08.');
    set_field_text($db, $tables, 'udelnaya/globals.php', 'seo_keywords_default', 'Garden Lounge Удельная, кальянная Удельная, кальянная у метро Удельная, лаунж бар Удельная, кальянная СПб, ул. Аккуратова 13, VIP-комнаты, PS5, кухня');
    set_field_text($db, $tables, 'udelnaya/globals.php', 'seo_image_default', 'https://garden-lounge.pro/udelnaya/couch/uploads/image/garden-main.jpg');

    set_field_text($db, $tables, 'udelnaya/contacts.php', 'cont_address', 'СПб., ул. Аккуратова, д. 13');
    set_field_text($db, $tables, 'udelnaya/contacts.php', 'cont_map_link', 'https://yandex.ru/maps/-/CPE-mNm0');
    set_field_text($db, $tables, 'udelnaya/contacts.php', 'cont_telegram', 'https://t.me/Garden_lounge_spb');
    set_field_text($db, $tables, 'udelnaya/menu.php', 'menu_visual_link', 'https://garden-lounge.pro/udelnaya/menu/visual/');
    set_field_text($db, $tables, 'udelnaya/menu.php', 'menu_text_link', 'https://garden-lounge.pro/udelnaya/menu/text/');
    set_field_text($db, $tables, 'udelnaya/menu.php', 'menu_eng_link', 'https://garden-lounge.pro/udelnaya/menu/english/');
    set_field_text($db, $tables, 'udelnaya/filial.php', 'final_btn_link', 'https://garden-lounge.pro/admiralteyskaya/');

    $db->commit();
    echo "Done. Udelnaya CouchCMS templates and menu data are ready.\n";
} catch (Exception $e) {
    $db->rollback();
    fwrite(STDERR, "Migration failed: {$e->getMessage()}\n");
    exit(1);
}
