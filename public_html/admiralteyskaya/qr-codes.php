<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='QR коды' name='qr_codes' executable='0' order='2' icon='image'>

    <cms:editable name='qr_manager' label='QR коды' type='message' order='1'>
        <div id="gl-qr-root">Загрузка раздела QR…</div>
        <link rel="stylesheet" href="/qr/admin.css">
        <script src="/qr/admin.js"></script>
    </cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
