<?php
/**
 * Переименовывает ключи в данных repeatable-галерей home.php
 * home_gallery_img -> home_adm_gallery_img / home_udel_gallery_img
 */
if (!defined('K_COUCH_DIR')) {
    require_once dirname(__DIR__) . '/couch/cms.php';
}

$db = $DB->conn;
$tpl = mysqli_fetch_assoc(mysqli_query($db, "SELECT id FROM " . K_TBL_TEMPLATES . " WHERE name='home.php' LIMIT 1"));
if (!$tpl) {
    echo "home.php template missing\n";
    exit(1);
}
$page = mysqli_fetch_assoc(mysqli_query($db, "SELECT id FROM " . K_TBL_PAGES . " WHERE template_id=" . (int) $tpl['id'] . " LIMIT 1"));
if (!$page) {
    echo "home.php page missing\n";
    exit(1);
}

function migrate_repeatable_keys($db, $pageId, $repeatableName, $imgKey, $altKey)
{
    $field = mysqli_fetch_assoc(mysqli_query(
        $db,
        "SELECT f.id, d.id AS data_id, d.value FROM " . K_TBL_FIELDS . " f " .
        "LEFT JOIN " . K_TBL_DATA_TEXT . " d ON d.field_id=f.id AND d.page_id=" . (int) $pageId . " " .
        "WHERE f.name='" . mysqli_real_escape_string($db, $repeatableName) . "' LIMIT 1"
    ));
    if (!$field || !$field['value']) {
        echo "{$repeatableName}: no data\n";
        return;
    }
    $rows = @unserialize($field['value']);
    if (!is_array($rows)) {
        echo "{$repeatableName}: invalid serialized data\n";
        return;
    }
    $changed = false;
    foreach ($rows as $i => $row) {
        if (!is_array($row)) {
            continue;
        }
        if (isset($row['home_gallery_img']) && !isset($row[$imgKey])) {
            $rows[$i][$imgKey] = $row['home_gallery_img'];
            unset($rows[$i]['home_gallery_img']);
            $changed = true;
        }
        if (isset($row['home_gallery_alt']) && !isset($row[$altKey])) {
            $rows[$i][$altKey] = $row['home_gallery_alt'];
            unset($rows[$i]['home_gallery_alt']);
            $changed = true;
        }
    }
    if (!$changed) {
        echo "{$repeatableName}: nothing to migrate\n";
        return;
    }
    $value = mysqli_real_escape_string($db, serialize($rows));
    if ($field['data_id']) {
        mysqli_query($db, "UPDATE " . K_TBL_DATA_TEXT . " SET value='{$value}' WHERE id=" . (int) $field['data_id'] . " LIMIT 1");
    } else {
        mysqli_query($db, "INSERT INTO " . K_TBL_DATA_TEXT . " (page_id, field_id, value) VALUES (" . (int) $pageId . ", " . (int) $field['id'] . ", '{$value}')");
    }
    echo "{$repeatableName}: migrated " . count($rows) . " rows\n";
}

$pageId = (int) $page['id'];
migrate_repeatable_keys($db, $pageId, 'home_adm_gallery', 'home_adm_gallery_img', 'home_adm_gallery_alt');
migrate_repeatable_keys($db, $pageId, 'home_udel_gallery', 'home_udel_gallery_img', 'home_udel_gallery_alt');
echo "Done.\n";
