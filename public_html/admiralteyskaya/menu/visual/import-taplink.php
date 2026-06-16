<?php require_once( '../../couch/cms.php' ); ?>
<cms:template title='Import Taplink Visual Menu' hidden='1' order='999' />

<?php
// One-time import: open with ?run=1&token=YOUR_TOKEN then delete this file.
$expected_token = 'garden-visual-' . substr(md5('loungegarden-menu-visual-2026'), 0, 12);
if (($_GET['run'] ?? '') !== '1' || ($_GET['token'] ?? '') !== $expected_token) {
    COUCH::invoke();
    return;
}

header('Content-Type: text/html; charset=utf-8');
$data_path = __DIR__ . '/taplink-import-data.json';
if (!is_file($data_path)) {
    echo '<h1 style="color:red">Missing taplink-import-data.json</h1>';
    COUCH::invoke();
    return;
}

$payload = json_decode(file_get_contents($data_path), true);
if (!is_array($payload)) {
    echo '<h1 style="color:red">Invalid JSON payload</h1>';
    COUCH::invoke();
    return;
}

global $CTX;
$CTX->set('bar_payload', json_encode($payload['bar'] ?? [], JSON_UNESCAPED_UNICODE), 'global');
$CTX->set('kitchen_payload', json_encode($payload['kitchen'] ?? [], JSON_UNESCAPED_UNICODE), 'global');
$CTX->set('desserts_payload', json_encode($payload['desserts'] ?? [], JSON_UNESCAPED_UNICODE), 'global');
$CTX->set('execute_visual_import', '1', 'global');
?>

<div style="padding:30px;background:#111;color:#fff;font-family:sans-serif">
    <h1 style="color:#C5A059">Import Taplink Visual Menu</h1>

    <cms:if execute_visual_import>
        <cms:pages masterpage='menu/visual/index.php' limit='1'>
            <cms:db_persist
                _masterpage='menu/visual/index.php'
                _mode='edit'
                _page_id=k_page_id
                menu_bar="<cms:show bar_payload />"
                menu_kitchen="<cms:show kitchen_payload />"
                menu_desserts="<cms:show desserts_payload />"
                _invalidate_cache='1'
            >
                <cms:if k_error>
                    <div style="color:#f66;border:1px solid #f66;padding:16px">
                        Ошибка: <cms:show k_error />
                    </div>
                <cms:else />
                    <div style="color:#C5A059;border:1px solid #C5A059;padding:16px">
                        ✅ Импорт выполнен.<br>
                        Бар: <?php echo count($payload['bar'] ?? []); ?>,
                        Кухня: <?php echo count($payload['kitchen'] ?? []); ?>,
                        Десерты: <?php echo count($payload['desserts'] ?? []); ?><br>
                        Удалите import-taplink.php после проверки.
                    </div>
                </cms:if>
            </cms:db_persist>
        </cms:pages>
    </cms:if>
</div>

<?php COUCH::invoke(); ?>
