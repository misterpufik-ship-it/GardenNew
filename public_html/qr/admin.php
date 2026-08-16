<?php
require_once __DIR__ . '/auth.php';
gl_qr_require_admin(false);
$embed = isset($_GET['embed']) && (string)$_GET['embed'] === '1';
$v = @filemtime(__DIR__ . '/admin.js') ?: time();
?><!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>QR коды — Garden Lounge</title>
    <link rel="stylesheet" href="/qr/admin.css?v=<?php echo (int)$v; ?>">
</head>
<body class="gl-qr-standalone<?php echo $embed ? ' is-embed' : ''; ?>">
    <?php if (!$embed) { ?>
        <header class="gl-qr-top">
            <a href="/admiralteyskaya/couch/?o=qr-codes.php">← В админку</a>
            <strong>QR коды</strong>
        </header>
    <?php } ?>
    <div id="gl-qr-root">Загрузка…</div>
    <script src="/qr/admin.js?v=<?php echo (int)$v; ?>"></script>
</body>
</html>
