<?php
/**
 * Переименовывает ключи в данных repeatable-галерей home.php
 * home_gallery_img -> home_adm_gallery_img / home_udel_gallery_img
 */
$root = realpath(__DIR__ . '/..');
if (!$root) {
    echo "Cannot resolve template root\n";
    exit(1);
}
chdir($root);
require_once $root . '/couch/cms.php';

global $DB;

$templates = K_DB_TABLES_PREFIX . 'couch_templates';
$fields = K_DB_TABLES_PREFIX . 'couch_fields';
$pages = K_DB_TABLES_PREFIX . 'couch_pages';
$dataText = K_DB_TABLES_PREFIX . 'couch_data_text';

$tplRows = $DB->select($templates, array('id'), "name='home.php' LIMIT 1");
if (!count($tplRows)) {
    echo "home.php template missing\n";
    exit(1);
}
$pageRows = $DB->select($pages, array('id'), "template_id='" . $DB->sanitize($tplRows[0]['id']) . "' LIMIT 1");
if (!count($pageRows)) {
    echo "home.php page missing\n";
    exit(1);
}

function migrate_repeatable_keys($DB, $fields, $dataText, $pageId, $repeatableName, $imgKey, $altKey)
{
    $fieldRows = $DB->select($fields, array('id'), "name='" . $DB->sanitize($repeatableName) . "' LIMIT 1");
    if (!count($fieldRows)) {
        echo "{$repeatableName}: field missing\n";
        return;
    }
    $fieldId = (int) $fieldRows[0]['id'];
    $dataRows = $DB->select($dataText, array('id', 'value'), "page_id='" . $DB->sanitize($pageId) . "' AND field_id='" . $DB->sanitize($fieldId) . "' LIMIT 1");
    if (!count($dataRows) || !$dataRows[0]['value']) {
        echo "{$repeatableName}: no data\n";
        return;
    }
    $rows = @unserialize($dataRows[0]['value']);
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
    $DB->update($dataText, array('value' => serialize($rows)), "id='" . $DB->sanitize($dataRows[0]['id']) . "'");
    echo "{$repeatableName}: migrated " . count($rows) . " rows\n";
}

$pageId = (int) $pageRows[0]['id'];
migrate_repeatable_keys($DB, $fields, $dataText, $pageId, 'home_adm_gallery', 'home_adm_gallery_img', 'home_adm_gallery_alt');
migrate_repeatable_keys($DB, $fields, $dataText, $pageId, 'home_udel_gallery', 'home_udel_gallery_img', 'home_udel_gallery_alt');
echo "Done.\n";
