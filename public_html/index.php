<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garden Lounge - кальянные и лаунж-бары в Санкт-Петербурге</title>
    <meta name="description" content="Garden Lounge в Санкт-Петербурге: выберите филиал на Адмиралтейской или Удельной. Кальяны, кухня, бар, VIP-комнаты, PS5, меню и бронирование столика.">
    <meta name="keywords" content="Garden Lounge, кальянная СПб, лаунж бар СПб, кальянная Адмиралтейская, кальянная Удельная, кальянная Санкт-Петербург, VIP-комнаты, PS5, кухня">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://garden-lounge.pro/">
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://garden-lounge.pro/">
    <meta property="og:title" content="Garden Lounge - кальянные и лаунж-бары в СПб">
    <meta property="og:description" content="Два филиала Garden Lounge в Санкт-Петербурге: Адмиралтейская и Удельная. Выберите локацию, посмотрите меню и забронируйте столик.">
    <meta property="og:image" content="https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/garden-main.jpg">
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="Garden Lounge - кальянные и лаунж-бары в СПб">
    <meta property="twitter:description" content="Выбор филиала Garden Lounge: Адмиралтейская или Удельная. Кальянная, лаунж-бар, кухня, VIP-комнаты и бронь.">
    <meta property="twitter:image" content="https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/garden-main.jpg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #030604;
            --panel: rgba(4, 6, 5, .76);
            --gold: #c5a059;
            --gold-2: #edd192;
            --line: rgba(197, 160, 89, .45);
            --text: #f5efe1;
            --muted: rgba(245, 239, 225, .74);
            --shadow: 0 22px 70px rgba(0, 0, 0, .62);
        }

        * { box-sizing: border-box; }

        html { min-height: 100%; background: var(--bg); }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--text);
            font-family: Montserrat, Arial, sans-serif;
            background:
                radial-gradient(circle at 50% 8%, rgba(197, 160, 89, .14), transparent 34%),
                linear-gradient(180deg, rgba(0, 0, 0, .26), rgba(0, 0, 0, .82)),
                url("/admiralteyskaya/couch/uploads/image/garden-main.webp") center / cover fixed;
            overflow-x: hidden;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
        }

        body::before {
            z-index: 0;
            background:
                linear-gradient(90deg, rgba(0, 0, 0, .75), transparent 22%, transparent 78%, rgba(0, 0, 0, .74)),
                linear-gradient(180deg, rgba(0, 0, 0, .44), rgba(0, 0, 0, .2) 34%, #020403 100%);
        }

        body::after {
            z-index: 1;
            background:
                radial-gradient(circle at 12% 18%, rgba(32, 83, 48, .42), transparent 21%),
                radial-gradient(circle at 88% 10%, rgba(63, 72, 31, .36), transparent 20%);
            mix-blend-mode: screen;
            opacity: .5;
        }

        a { color: inherit; text-decoration: none; }
        button { font: inherit; }

        .page {
            position: relative;
            z-index: 2;
            min-height: 100svh;
            padding: 22px clamp(16px, 4vw, 54px) 0;
        }

        .topbar {
            min-height: 54px;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: start;
            gap: 18px;
        }

        .brand {
            grid-column: 2;
            display: flex;
            justify-content: center;
            padding-top: 2px;
        }

        .logo {
            width: clamp(246px, 26vw, 358px);
            height: auto;
            display: block;
            filter: drop-shadow(0 0 12px rgba(197, 160, 89, .28));
        }

        .top-actions {
            grid-column: 3;
            justify-self: end;
            display: flex;
            align-items: center;
            gap: 22px;
        }

        .reserve-top {
            min-height: 42px;
            min-width: 210px;
            padding: 0 26px;
            border: 1px solid var(--line);
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--gold-2);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .02em;
            background: rgba(5, 7, 5, .5);
            box-shadow: inset 0 0 0 1px rgba(197, 160, 89, .1);
            transition: background .2s ease, color .2s ease, border-color .2s ease;
        }

        .reserve-top:hover,
        .reserve-top:focus-visible {
            color: #0b0905;
            background: linear-gradient(180deg, var(--gold-2), var(--gold));
            border-color: rgba(237, 209, 146, .9);
        }

        .burger {
            width: 42px;
            height: 42px;
            border: 0;
            background: transparent;
            color: var(--gold);
            display: inline-grid;
            place-items: center;
            cursor: pointer;
            padding: 0;
        }

        .burger span,
        .burger::before,
        .burger::after {
            content: "";
            width: 31px;
            height: 2px;
            background: currentColor;
            border-radius: 2px;
            box-shadow: 0 0 8px rgba(197, 160, 89, .4);
        }

        .burger { gap: 7px; }

        .hero-copy {
            text-align: center;
            margin: clamp(18px, 3.2vh, 42px) auto 17px;
        }

        h1 {
            margin: 0;
            color: var(--gold-2);
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: clamp(34px, 4.4vw, 58px);
            font-weight: 700;
            line-height: .95;
            letter-spacing: .055em;
            text-transform: uppercase;
            text-shadow: 0 0 26px rgba(197, 160, 89, .22), 0 3px 12px rgba(0, 0, 0, .88);
        }

        .lead {
            margin: 8px auto 0;
            color: rgba(255, 255, 255, .78);
            font-size: clamp(15px, 1.6vw, 22px);
            line-height: 1.35;
            font-weight: 600;
            text-shadow: 0 2px 12px rgba(0, 0, 0, .9);
        }

        .branches {
            width: min(1060px, 100%);
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .branch {
            position: relative;
            min-height: 338px;
            border: 1px solid var(--line);
            border-radius: 15px;
            overflow: hidden;
            background: #020302;
            box-shadow: var(--shadow), inset 0 0 0 1px rgba(237, 209, 146, .07);
            isolation: isolate;
        }

        .slider,
        .slide {
            position: absolute;
            inset: 0;
        }

        .slide {
            opacity: 0;
            transition: opacity .55s ease;
        }

        .slide.active { opacity: 1; }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            filter: brightness(.75) saturate(.95);
        }

        .branch::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 2;
            background:
                linear-gradient(180deg, rgba(0, 0, 0, .46) 0%, rgba(0, 0, 0, .13) 35%, rgba(0, 0, 0, .55) 68%, rgba(0, 0, 0, .86) 100%),
                radial-gradient(circle at 50% 8%, rgba(197, 160, 89, .18), transparent 34%);
            pointer-events: none;
        }

        .branch-content {
            position: relative;
            z-index: 3;
            min-height: 338px;
            padding: 24px clamp(16px, 2.5vw, 30px) 26px;
            display: grid;
            grid-template-rows: auto 1fr auto auto;
            align-items: center;
            text-align: center;
        }

        .branch h2 {
            margin: 0;
            color: #f8f4ed;
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: clamp(26px, 3vw, 39px);
            font-weight: 700;
            line-height: 1;
            letter-spacing: .06em;
            text-transform: uppercase;
            text-shadow: 0 3px 14px rgba(0, 0, 0, .95);
        }

        .branch-subtitle {
            width: min(340px, 88%);
            margin: 10px auto 0;
            color: rgba(255, 255, 255, .82);
            font-size: 14px;
            line-height: 1.45;
            font-weight: 600;
            text-shadow: 0 2px 11px #000;
        }

        .address {
            align-self: end;
            margin: 0 auto 20px;
            color: rgba(245, 239, 225, .86);
            font-size: 14px;
            line-height: 1.3;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            text-shadow: 0 2px 10px #000;
        }

        .pin {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            color: var(--gold-2);
        }

        .branch-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
        }

        .button {
            min-height: 45px;
            border: 1px solid var(--line);
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 18px;
            color: var(--gold-2);
            background: rgba(0, 0, 0, .45);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.1;
            transition: background .2s ease, color .2s ease, transform .2s ease;
        }

        .button.primary {
            color: #181006;
            background: linear-gradient(180deg, #f0d99f, var(--gold));
            border-color: rgba(237, 209, 146, .92);
            box-shadow: 0 10px 22px rgba(0, 0, 0, .24);
        }

        .button:hover,
        .button:focus-visible {
            transform: translateY(-1px);
            color: #171006;
            background: linear-gradient(180deg, #f0d99f, var(--gold));
        }

        .slider-btn {
            position: absolute;
            top: 49%;
            z-index: 5;
            width: 48px;
            height: 48px;
            border: 1px solid rgba(197, 160, 89, .5);
            border-radius: 50%;
            background: rgba(0, 0, 0, .54);
            color: var(--gold-2);
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: background .2s ease, color .2s ease, transform .2s ease;
        }

        .slider-btn:hover,
        .slider-btn:focus-visible {
            color: #171006;
            background: linear-gradient(180deg, #f0d99f, var(--gold));
            transform: translateY(-1px);
        }

        .slider-btn svg {
            width: 22px;
            height: 22px;
            stroke-width: 2.4;
        }

        .prev { left: 16px; }
        .next { right: 16px; }

        .dots {
            position: absolute;
            z-index: 5;
            left: 50%;
            bottom: 78px;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
        }

        .dot {
            width: 8px;
            height: 8px;
            border: 1px solid rgba(237, 209, 146, .75);
            border-radius: 50%;
            background: transparent;
            padding: 0;
            cursor: pointer;
        }

        .dot.active { background: var(--gold-2); }

        .features {
            width: min(1060px, 100%);
            margin: 15px auto 0;
            border-top: 1px solid rgba(197, 160, 89, .28);
            border-bottom: 1px solid rgba(197, 160, 89, .25);
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            background: rgba(3, 6, 3, .42);
            backdrop-filter: blur(8px);
        }

        .feature {
            min-height: 78px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 13px;
            color: var(--gold-2);
            border-left: 1px solid rgba(197, 160, 89, .16);
            text-transform: uppercase;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.15;
            text-align: left;
        }

        .feature:first-child { border-left: 0; }

        .feature svg {
            width: 31px;
            height: 31px;
            flex: 0 0 31px;
            stroke-width: 1.7;
        }

        .quick-booking {
            display: none;
            width: min(1060px, 100%);
            margin: 18px auto 0;
            border: 1px solid rgba(197, 160, 89, .42);
            border-radius: 8px;
            overflow: hidden;
            background: rgba(3, 5, 3, .75);
            box-shadow: 0 14px 40px rgba(0, 0, 0, .42);
        }

        .quick-booking-title {
            min-height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #171006;
            background: linear-gradient(180deg, #f0d99f, var(--gold));
            font-size: 15px;
            font-weight: 800;
        }

        .quick-booking-links {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .quick-booking a {
            min-height: 64px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            color: var(--gold-2);
            border-left: 1px solid rgba(197, 160, 89, .25);
            font-weight: 800;
            font-size: 16px;
        }

        .quick-booking a:first-child { border-left: 0; }

        .quick-booking span {
            color: rgba(245, 239, 225, .7);
            font-size: 11px;
            font-weight: 700;
        }

        .menu-panel {
            position: fixed;
            inset: 0;
            z-index: 20;
            display: none;
            place-items: center;
            padding: 28px;
            background:
                linear-gradient(180deg, rgba(0, 0, 0, .82), rgba(0, 0, 0, .96)),
                url("/admiralteyskaya/couch/uploads/image/garden-main.webp") center / cover;
        }

        .menu-panel.active { display: grid; }

        .menu-close {
            position: absolute;
            top: 24px;
            right: clamp(18px, 4vw, 54px);
            width: 44px;
            height: 44px;
            border: 1px solid rgba(197, 160, 89, .42);
            border-radius: 50%;
            background: rgba(0, 0, 0, .36);
            color: var(--gold-2);
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
        }

        .menu-links {
            width: min(420px, 100%);
            display: grid;
            gap: 12px;
            text-align: center;
        }

        .menu-links a {
            min-height: 58px;
            border: 1px solid rgba(197, 160, 89, .34);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold-2);
            background: rgba(0, 0, 0, .36);
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: 28px;
            font-weight: 700;
        }

        .sr-text {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
        }

        @media (max-width: 880px) {
            body {
                background:
                    radial-gradient(circle at 50% 8%, rgba(197, 160, 89, .16), transparent 34%),
                    linear-gradient(180deg, rgba(0, 0, 0, .25), rgba(0, 0, 0, .78)),
                    url("/admiralteyskaya/couch/uploads/image/garden-main-mobile.webp") center top / cover fixed;
            }

            .page {
                padding: 20px 17px 24px;
            }

            .topbar {
                min-height: 92px;
                grid-template-columns: 1fr auto 1fr;
                align-items: start;
            }

            .brand { padding-top: 3px; }

            .logo {
                width: min(58vw, 255px);
                min-width: 214px;
            }

            .reserve-top { display: none; }

            .top-actions {
                align-self: start;
                padding-top: 13px;
                gap: 0;
            }

            .burger {
                width: 36px;
                height: 36px;
                gap: 6px;
            }

            .burger span,
            .burger::before,
            .burger::after {
                width: 28px;
            }

            .hero-copy {
                margin: 6px auto 16px;
            }

            h1 {
                width: min(360px, 100%);
                margin-inline: auto;
                font-size: clamp(28px, 7.8vw, 37px);
                line-height: 1.04;
            }

            .lead {
                font-size: clamp(15px, 4.2vw, 18px);
                max-width: 330px;
            }

            .branches {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .branch {
                min-height: 282px;
                border-radius: 10px;
            }

            .branch-content {
                min-height: 282px;
                padding: 18px 22px 13px;
            }

            .branch h2 {
                font-size: clamp(24px, 7.3vw, 31px);
            }

            .branch-subtitle {
                margin-top: 7px;
                font-size: 13px;
                max-width: 246px;
            }

            .address {
                margin-bottom: 14px;
                font-size: 13px;
            }

            .branch-actions {
                gap: 17px;
            }

            .button {
                min-height: 39px;
                padding: 8px 10px;
                font-size: 12px;
            }

            .slider-btn {
                width: 42px;
                height: 42px;
                top: 51%;
            }

            .prev { left: 10px; }
            .next { right: 10px; }

            .dots { bottom: 61px; }

            .features {
                width: calc(100% + 34px);
                margin-left: -17px;
                margin-right: -17px;
                margin-top: 14px;
            }

            .feature {
                min-height: 111px;
                padding: 10px 5px;
                flex-direction: column;
                gap: 8px;
                font-size: 10px;
                text-align: center;
            }

            .feature svg {
                width: 28px;
                height: 28px;
            }

            .quick-booking { display: block; }
        }

        @media (max-width: 430px) {
            .branch { min-height: 292px; }
            .branch-content { min-height: 292px; }
            .branch-actions { gap: 12px; }
            .button { font-size: 11px; }
        }
    </style>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "@id": "https://garden-lounge.pro/#organization",
        "name": "Garden Lounge",
        "url": "https://garden-lounge.pro/",
        "logo": "https://garden-lounge.pro/img/logo3.png",
        "sameAs": ["https://vk.com/loungegarden", "https://instagram.com/garden_lounge_spb/", "https://youtube.com/@garden.lounge"]
    }
    </script>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ItemList",
        "@id": "https://garden-lounge.pro/#branches",
        "name": "Филиалы Garden Lounge в Санкт-Петербурге",
        "itemListElement": [
            {"@type": "ListItem", "position": 1, "url": "https://garden-lounge.pro/admiralteyskaya/", "name": "Garden Lounge Адмиралтейская"},
            {"@type": "ListItem", "position": 2, "url": "https://garden-lounge.pro/udelnaya/", "name": "Garden Lounge Удельная"}
        ]
    }
    </script>
</head>
<body>
    <main class="page">
        <nav class="topbar" aria-label="Главная навигация">
            <a class="brand" href="/" aria-label="Garden Lounge">
                <img class="logo" src="/img/logo3.png" alt="Garden Lounge" width="358" height="151">
            </a>
            <div class="top-actions">
                <a class="reserve-top" href="/admiralteyskaya/reservation/">Забронировать столик</a>
                <button class="burger" type="button" aria-label="Открыть меню"><span></span></button>
            </div>
        </nav>

        <section class="hero-copy" aria-labelledby="main-title">
            <h1 id="main-title">Два тайных сада Петербурга</h1>
            <p class="lead">Выберите свой Garden Lounge</p>
        </section>

        <section class="branches" aria-label="Выбор филиала Garden Lounge">
            <article class="branch" data-slider>
                <div class="slider" aria-hidden="true">
                    <div class="slide active"><img src="/admiralteyskaya/couch/uploads/image/garden-main.webp" alt=""></div>
                    <div class="slide"><img src="/admiralteyskaya/couch/uploads/image/garden-2.webp" alt=""></div>
                    <div class="slide"><img src="/admiralteyskaya/couch/uploads/image/safonovleonid_green_55.webp" alt=""></div>
                </div>
                <button class="slider-btn prev" type="button" aria-label="Предыдущее фото">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <button class="slider-btn next" type="button" aria-label="Следующее фото">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                </button>
                <div class="dots" aria-hidden="true"></div>
                <div class="branch-content">
                    <div>
                        <h2>Адмиралтейская</h2>
                        <p class="branch-subtitle">Магический вечнозеленый сад в сердце Петербурга</p>
                    </div>
                    <span></span>
                    <p class="address">
                        <svg class="pin" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.5A7.5 7.5 0 0 0 4.5 10c0 5.3 6.45 11.05 6.72 11.29a1.18 1.18 0 0 0 1.56 0C13.05 21.05 19.5 15.3 19.5 10A7.5 7.5 0 0 0 12 2.5Zm0 10.25A2.75 2.75 0 1 1 12 7.25a2.75 2.75 0 0 1 0 5.5Z"/></svg>
                        наб. реки Мойки, 67-69
                    </p>
                    <div class="branch-actions">
                        <a class="button primary" href="/admiralteyskaya/">Открыть филиал</a>
                        <a class="button" href="/admiralteyskaya/reservation/">Забронировать</a>
                    </div>
                </div>
            </article>

            <article class="branch" data-slider>
                <div class="slider" aria-hidden="true">
                    <div class="slide active"><img src="/admiralteyskaya/couch/uploads/image/kalyannaya-garden-lounge-udelnaya-interer-spb.webp" alt=""></div>
                    <div class="slide"><img src="/admiralteyskaya/couch/uploads/image/garden.webp" alt=""></div>
                    <div class="slide"><img src="/admiralteyskaya/couch/uploads/image/safonovleonid_green_65.webp" alt=""></div>
                </div>
                <button class="slider-btn prev" type="button" aria-label="Предыдущее фото">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <button class="slider-btn next" type="button" aria-label="Следующее фото">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                </button>
                <div class="dots" aria-hidden="true"></div>
                <div class="branch-content">
                    <div>
                        <h2>Удельная</h2>
                        <p class="branch-subtitle">Тропический оазис на севере Петербурга</p>
                    </div>
                    <span></span>
                    <p class="address">
                        <svg class="pin" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.5A7.5 7.5 0 0 0 4.5 10c0 5.3 6.45 11.05 6.72 11.29a1.18 1.18 0 0 0 1.56 0C13.05 21.05 19.5 15.3 19.5 10A7.5 7.5 0 0 0 12 2.5Zm0 10.25A2.75 2.75 0 1 1 12 7.25a2.75 2.75 0 0 1 0 5.5Z"/></svg>
                        ул. Аккуратова, 13
                    </p>
                    <div class="branch-actions">
                        <a class="button primary" href="/udelnaya/">Открыть филиал</a>
                        <a class="button" href="/udelnaya/reservation/">Забронировать</a>
                    </div>
                </div>
            </article>
        </section>

        <section class="features" aria-label="Возможности Garden Lounge">
            <div class="feature">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M9 3v3m6-3v3M7 21h10M8 6h8l1 5a5 5 0 0 1-10 0l1-5Z"/><path d="M12 15v6M4 11h16"/></svg>
                Кальяны
            </div>
            <div class="feature">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M7 2v20M4 2v7a3 3 0 0 0 6 0V2M17 2v20M14 2h3a3 3 0 0 1 3 3v7h-6V2Z"/></svg>
                Кухня и бар
            </div>
            <div class="feature">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M7 8h10a5 5 0 0 1 4.8 3.6l.8 2.8a3 3 0 0 1-5.3 2.6L15 14H9l-2.3 3a3 3 0 0 1-5.3-2.6l.8-2.8A5 5 0 0 1 7 8Z"/><path d="M8 12h.01M6.5 13.5h.01M16 12h.01M18 13.5h.01"/></svg>
                VIP и PS5
            </div>
            <div class="feature">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M12 21s7-4.5 7-11a7 7 0 1 0-14 0c0 6.5 7 11 7 11Z"/><path d="M12 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/></svg>
                Два филиала
            </div>
        </section>

        <section class="quick-booking" aria-label="Быстрое бронирование">
            <div class="quick-booking-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M8 2v4M16 2v4M3 10h18"/><path d="M5 4h14a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/></svg>
                Быстрое бронирование
            </div>
            <div class="quick-booking-links">
                <a href="tel:+79956246808"><span>Адмиралтейская</span>+7 995 624-68-08</a>
                <a href="tel:+79500473365"><span>Удельная</span>+7 950 047-33-65</a>
            </div>
        </section>

        <p class="sr-text">Garden Lounge работает в двух филиалах Санкт-Петербурга: наб. реки Мойки 67-69 и ул. Аккуратова 13. В заведениях доступны кальяны, кухня, бар, VIP-комнаты, PS5, меню и бронирование столика.</p>
    </main>

    <div class="menu-panel" id="menuPanel" aria-hidden="true">
        <button class="menu-close" type="button" aria-label="Закрыть меню">×</button>
        <div class="menu-links">
            <a href="/admiralteyskaya/">Адмиралтейская</a>
            <a href="/udelnaya/">Удельная</a>
            <a href="/admiralteyskaya/menu/">Меню</a>
            <a href="/admiralteyskaya/reservation/">Бронь</a>
            <a href="tel:+79956246808">Позвонить</a>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-slider]').forEach((branch) => {
            const slides = Array.from(branch.querySelectorAll('.slide'));
            const dotsWrap = branch.querySelector('.dots');
            let index = 0;

            slides.forEach((_, dotIndex) => {
                const dot = document.createElement('button');
                dot.className = `dot${dotIndex === 0 ? ' active' : ''}`;
                dot.type = 'button';
                dot.setAttribute('aria-label', `Показать фото ${dotIndex + 1}`);
                dot.addEventListener('click', () => show(dotIndex));
                dotsWrap.appendChild(dot);
            });

            const dots = Array.from(dotsWrap.querySelectorAll('.dot'));

            function show(nextIndex) {
                slides[index].classList.remove('active');
                dots[index].classList.remove('active');
                index = (nextIndex + slides.length) % slides.length;
                slides[index].classList.add('active');
                dots[index].classList.add('active');
            }

            branch.querySelector('.prev').addEventListener('click', () => show(index - 1));
            branch.querySelector('.next').addEventListener('click', () => show(index + 1));
        });

        const burger = document.querySelector('.burger');
        const menuPanel = document.getElementById('menuPanel');
        const menuClose = document.querySelector('.menu-close');

        function setMenu(open) {
            menuPanel.classList.toggle('active', open);
            menuPanel.setAttribute('aria-hidden', String(!open));
            document.body.style.overflow = open ? 'hidden' : '';
        }

        burger.addEventListener('click', () => setMenu(true));
        menuClose.addEventListener('click', () => setMenu(false));
        menuPanel.addEventListener('click', (event) => {
            if (event.target === menuPanel) setMenu(false);
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') setMenu(false);
        });
    </script>
</body>
</html>
