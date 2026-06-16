<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/');

function send_section($title, $subtitle, $lang = 'ru') {
    $is_en = ($lang === 'en');
    $note = $is_en
        ? 'Local clickable preview is enabled. The real CMS content will appear after the local database is connected.'
        : '&#1051;&#1086;&#1082;&#1072;&#1083;&#1100;&#1085;&#1099;&#1081; &#1082;&#1083;&#1080;&#1082;&#1072;&#1073;&#1077;&#1083;&#1100;&#1085;&#1099;&#1081; &#1087;&#1088;&#1086;&#1089;&#1084;&#1086;&#1090;&#1088; &#1074;&#1082;&#1083;&#1102;&#1095;&#1077;&#1085;. &#1053;&#1072;&#1089;&#1090;&#1086;&#1103;&#1097;&#1077;&#1077; &#1089;&#1086;&#1076;&#1077;&#1088;&#1078;&#1080;&#1084;&#1086;&#1077; CMS &#1087;&#1086;&#1103;&#1074;&#1080;&#1090;&#1089;&#1103; &#1087;&#1086;&#1089;&#1083;&#1077; &#1087;&#1086;&#1076;&#1082;&#1083;&#1102;&#1095;&#1077;&#1085;&#1080;&#1103; &#1083;&#1086;&#1082;&#1072;&#1083;&#1100;&#1085;&#1086;&#1081; &#1073;&#1072;&#1079;&#1099;.';

    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><html lang="' . ($is_en ? 'en' : 'ru') . '"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . $title . '</title>';
    echo '<script src="https://cdn.tailwindcss.com"></script><link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">';
    echo '<style>body{margin:0;background:#000;color:#eee;font-family:Montserrat,sans-serif}.serif{font-family:"Cormorant Garamond",serif}.gold{color:#C5A059}.wrap{min-height:100vh;display:flex;flex-direction:column;align-items:center;padding:34px 18px}.logo{height:105px;object-fit:contain}.panel{width:100%;max-width:620px;border-top:1px solid rgba(197,160,89,.45);border-bottom:1px solid rgba(197,160,89,.25);padding:34px 0;text-align:center}.nav{display:flex;gap:10px;flex-wrap:wrap;justify-content:center;margin:28px 0}.nav a{border:1px solid rgba(197,160,89,.35);color:#C5A059;text-decoration:none;text-transform:uppercase;letter-spacing:.14em;font-size:10px;padding:12px 14px}.nav a:hover{background:#C5A059;color:#000}.note{max-width:520px;color:#999;font-size:13px;line-height:1.7;margin:0 auto}.back{display:inline-block;margin-top:26px;color:#C5A059;text-decoration:none;font-size:11px;text-transform:uppercase;letter-spacing:.2em}</style></head>';
    echo '<body><main class="wrap"><img class="logo" src="/img/logo3.webp" alt="Lounge Garden"><section class="panel"><h1 class="serif" style="font-size:46px;font-style:italic;margin:0 0 10px">' . $title . '</h1><div class="gold" style="font-size:11px;text-transform:uppercase;letter-spacing:.35em;margin-bottom:24px">' . $subtitle . '</div><div class="nav">';
    echo '<a href="/admiralteyskaya/menu/visual">&#1042;&#1080;&#1079;&#1091;&#1072;&#1083;&#1100;&#1085;&#1086;&#1077; &#1084;&#1077;&#1085;&#1102;</a><a href="/admiralteyskaya/menu/text">&#1058;&#1077;&#1082;&#1089;&#1090;&#1086;&#1074;&#1086;&#1077; &#1084;&#1077;&#1085;&#1102;</a><a href="/admiralteyskaya/menu/english">English menu</a></div><p class="note">' . $note . '</p><a class="back" href="/admiralteyskaya/menu/">' . ($is_en ? 'Back to menu choice' : '&#1042;&#1077;&#1088;&#1085;&#1091;&#1090;&#1100;&#1089;&#1103; &#1082; &#1074;&#1099;&#1073;&#1086;&#1088;&#1091; &#1084;&#1077;&#1085;&#1102;') . '</a></section></main></body></html>';
}

if ($path === '/admiralteyskaya/menu/visual') {
    send_section('&#1042;&#1080;&#1079;&#1091;&#1072;&#1083;&#1100;&#1085;&#1086;&#1077; &#1084;&#1077;&#1085;&#1102;', '&#1043;&#1072;&#1083;&#1077;&#1088;&#1077;&#1103; &#1084;&#1077;&#1085;&#1102;');
    return true;
}

if ($path === '/admiralteyskaya/menu/text') {
    send_section('&#1058;&#1077;&#1082;&#1089;&#1090;&#1086;&#1074;&#1086;&#1077; &#1084;&#1077;&#1085;&#1102;', 'Classic list');
    return true;
}

if ($path === '/admiralteyskaya/menu/english') {
    send_section('English menu', 'Classic list', 'en');
    return true;
}

return false;
