<?php
if (!defined('K_COUCH_DIR')) {
    require_once dirname(__DIR__) . '/couch/cms.php';
}
$db = $DB->conn;
$tpl = mysqli_fetch_assoc(mysqli_query($db, "SELECT id FROM " . K_TBL_TEMPLATES . " WHERE name='home.php' LIMIT 1"));
if (!$tpl) {
    echo "home.php template missing\n";
    exit(1);
}
$page = mysqli_fetch_assoc(mysqli_query($db, "SELECT id, page_title FROM " . K_TBL_PAGES . " WHERE template_id=" . (int)$tpl['id'] . " LIMIT 1"));
echo "home page #" . $page['id'] . " " . $page['page_title'] . "\n";
$res = mysqli_query($db, "SELECT f.id, f.name, f.type, d.value FROM " . K_TBL_FIELDS . " f LEFT JOIN " . K_TBL_DATA_TEXT . " d ON d.field_id=f.id AND d.page_id=" . (int)$page['id'] . " WHERE f.template_id=" . (int)$tpl['id'] . " AND (f.name LIKE '%gallery%' OR f.name LIKE '%btn%') ORDER BY f.k_order");
while ($row = mysqli_fetch_assoc($res)) {
    $len = $row['value'] ? strlen($row['value']) : 0;
    echo $row['id'] . ' ' . $row['name'] . ' [' . $row['type'] . '] len=' . $len . "\n";
    if ($len) {
        echo '  preview: ' . substr($row['value'], 0, 180) . "\n";
    }
}
