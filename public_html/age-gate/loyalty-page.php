<?php
/**
 * Standalone loyalty registration pages for each branch.
 * Thin landing for QR: logo + Wallet / Telegram, noindex.
 */
function gl_loyalty_page_render($branch)
{
    $isUdelnaya = ($branch === 'udelnaya');
    $homePath = $isUdelnaya ? '/udelnaya' : '/admiralteyskaya';
    $branchLabel = $isUdelnaya ? 'Удельная' : 'Адмиралтейская';
    $pageUrl = 'https://garden-lounge.pro' . $homePath . '/loyalty';
    $walletUrl = 'https://access.clientomer.ru/feedback/676900-1/';
    $telegramUrl = 'https://t.me/GardenLounge_Loyalty_Bot';
    $logoSrc = '/img/logo3.webp';
    $title = 'Программа лояльности Garden Lounge — ' . $branchLabel;

    header('X-Robots-Tag: noindex, nofollow');

    $esc = static function ($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    };

    require_once __DIR__ . '/assets.php';
    ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $esc($title); ?></title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Регистрация в программе лояльности Garden Lounge: Wallet или Telegram.">
    <link rel="canonical" href="<?php echo $esc($pageUrl); ?>">
    <?php gl_favicon_render_tags('/favicon.png'); gl_render_font_assets(); ?>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --gold: #C5A059;
            --gold-dark: #8e7037;
            --gold-light: #FFEebb;
            --bg: #000000;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            min-height: 100%;
            background: var(--bg);
            color: #fff;
            font-family: Montserrat, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        body {
            min-height: 100svh;
        }

        a { color: inherit; text-decoration: none; }

        .page {
            min-height: 100svh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: clamp(22px, 4vw, 40px) clamp(16px, 4vw, 54px) 36px;
        }

        .header {
            display: flex;
            justify-content: center;
            margin-bottom: clamp(18px, 3vw, 32px);
        }

        .logo {
            width: clamp(220px, 28vw, 360px);
            height: auto;
            display: block;
        }

        .lead {
            margin: 0 0 clamp(22px, 3.2vw, 32px);
            color: var(--gold);
            font-size: clamp(10px, 1vw, 13px);
            font-weight: 500;
            letter-spacing: .34em;
            text-transform: uppercase;
            text-align: center;
        }

        .card {
            width: 100%;
            max-width: 400px;
            background: #0a0a0a;
            border: 1px solid var(--gold);
            padding: 40px 25px;
            text-align: center;
            box-shadow: 0 0 30px rgba(197, 160, 89, 0.2);
        }

        .card-title {
            margin: 0 0 25px;
            font-family: "Cormorant Garamond", Georgia, serif;
            font-style: italic;
            font-size: 24px;
            font-weight: 400;
            line-height: 1.2;
            color: var(--gold);
        }

        .gold-shimmer {
            background-image: linear-gradient(90deg,
                #8e7037 0%, #C5A059 20%, #FFEebb 25%, #C5A059 30%, #8e7037 50%,
                #8e7037 50%, #C5A059 70%, #FFEebb 75%, #C5A059 80%, #8e7037 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
            animation: shineGold 5s linear infinite;
        }

        @keyframes shineGold {
            0% { background-position: 0% center; }
            100% { background-position: 100% center; }
        }

        .modal-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            height: 54px;
            margin-bottom: 12px;
            border: 1px solid rgba(197, 160, 89, 0.3);
            background: transparent;
            color: #fff;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.1em;
            transition: 0.3s;
        }

        .modal-btn:last-child { margin-bottom: 0; }

        .modal-btn:hover,
        .modal-btn:focus-visible {
            background: rgba(197, 160, 89, 0.1);
            border-color: var(--gold);
        }

        .modal-btn i {
            font-size: 18px;
            color: var(--gold);
        }
    </style>
</head>
<body>
    <div class="page">
        <header class="header">
            <a href="<?php echo $esc($homePath); ?>" aria-label="Garden Lounge <?php echo $esc($branchLabel); ?>">
                <img class="logo" src="<?php echo $esc($logoSrc); ?>" alt="Garden Lounge" width="360" height="152" decoding="async">
            </a>
        </header>

        <p class="lead"><?php echo $esc($branchLabel); ?></p>

        <main class="card">
            <h1 class="card-title gold-shimmer">Выберите способ регистрации</h1>

            <a class="modal-btn" href="<?php echo $esc($walletUrl); ?>" target="_blank" rel="noopener noreferrer">
                <i class="fa-solid fa-wallet" aria-hidden="true"></i>
                Регистрация через Wallet
            </a>

            <a class="modal-btn" href="<?php echo $esc($telegramUrl); ?>" target="_blank" rel="noopener noreferrer">
                <i class="fa-brands fa-telegram" aria-hidden="true"></i>
                Регистрация через Telegram
            </a>
        </main>
    </div>
</body>
</html>
    <?php
}
