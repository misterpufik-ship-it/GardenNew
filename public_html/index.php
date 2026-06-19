<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garden Lounge - кальянные и лаунж-бары в Санкт-Петербурге</title>
    <meta name="description" content="Garden Lounge в Санкт-Петербурге: выберите филиал на Адмиралтейской или Удельной. Кальянная, кухня, бар, VIP-комнаты, PS5 и звонок в выбранный филиал.">
    <meta name="keywords" content="Garden Lounge, кальянная СПб, лаунж бар СПб, кальянная Адмиралтейская, кальянная Удельная">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://garden-lounge.pro/">
    <link rel="icon" type="image/png" href="/udelnaya/favicon.png">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://garden-lounge.pro/">
    <meta property="og:title" content="Garden Lounge - филиалы в Санкт-Петербурге">
    <meta property="og:description" content="Два филиала Garden Lounge: Адмиралтейская и Удельная. Выберите локацию и позвоните в нужный филиал.">
    <meta property="og:image" content="https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/garden-main.jpg">
    <meta property="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,500;0,600;1,300&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold-main: #C5A059;
            --gold-dark: #8e7037;
            --gold-light: #FFEebb;
            --bg-black: #000000;
            --text-soft: rgba(255, 255, 255, .74);
            --surface: #050505;
            --surface-soft: #090909;
        }

        * { box-sizing: border-box; }

        html {
            min-height: 100%;
            background: var(--bg-black);
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #000;
            color: #fff;
            font-family: Montserrat, Arial, sans-serif;
            line-height: 1.5;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background: none;
        }

        a {
            color: inherit;
            text-decoration: none;
            transition: color .25s ease, border-color .25s ease, background .25s ease, transform .25s ease;
        }

        button { font: inherit; }

        .page {
            position: relative;
            min-height: 100svh;
            padding: clamp(22px, 4vw, 40px) clamp(16px, 4vw, 54px) 30px;
        }

        .header {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: clamp(18px, 3vw, 32px);
        }

        .logo {
            width: clamp(268px, 28vw, 386px);
            height: auto;
            display: block;
            filter: none;
        }

        .intro {
            width: min(760px, 100%);
            margin: 0 auto clamp(18px, 2.6vw, 26px);
            text-align: center;
        }

        h1 {
            margin: 0;
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: clamp(33px, 3.6vw, 48px);
            font-weight: 600;
            font-style: italic;
            line-height: 1;
            letter-spacing: 0;
            color: #fff;
            text-shadow: 0 8px 28px rgba(0, 0, 0, .55);
        }

        .lead {
            margin: 6px 0 0;
            color: var(--gold-main);
            font-size: clamp(10px, 1vw, 13px);
            font-weight: 500;
            letter-spacing: .34em;
            text-transform: uppercase;
        }

        @keyframes shineGold {
            to { background-position: 200% center; }
        }

        @keyframes ringPhone {
            0%, 100% { transform: rotate(0deg); }
            10% { transform: rotate(-15deg); }
            20% { transform: rotate(15deg); }
            30% { transform: rotate(-15deg); }
            40% { transform: rotate(15deg); }
            50% { transform: rotate(0deg); }
        }

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(197, 160, 89, 0.7); }
            50% { box-shadow: 0 0 0 8px rgba(197, 160, 89, 0); }
        }

        .branches {
            width: min(1100px, 100%);
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: clamp(18px, 3vw, 30px);
        }

        .branch {
            background: linear-gradient(180deg, var(--surface), #000);
            box-shadow: 0 22px 70px rgba(0, 0, 0, .58);
        }

        .branch-title {
            min-height: 88px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 18px 16px 17px;
            background: #000;
        }

        .branch-title h2 {
            margin: 0;
            font-family: "Cormorant Garamond", Georgia, serif;
            font-size: clamp(25px, 3.15vw, 38px);
            font-weight: 300;
            font-style: italic;
            line-height: 1.04;
            letter-spacing: 0;
            text-transform: none;
            color: #fff;
            text-shadow: 0 10px 28px rgba(0, 0, 0, .64);
        }

        .branch-address {
            color: var(--gold-main);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            font-size: 12px;
            font-weight: 500;
            line-height: 1.25;
            text-align: center;
        }

        .branch-address:hover,
        .branch-address:focus-visible {
            color: var(--gold-light);
        }

        .branch-address .pin {
            width: 14px;
            height: 14px;
            flex-basis: 14px;
        }

        .slider {
            position: relative;
            aspect-ratio: 16 / 10;
            background: #050505;
            overflow: hidden;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .035);
            cursor: pointer;
        }

        .slider:hover {
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .035), 0 0 20px rgba(197, 160, 89, 0.2);
        }

        .slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity .55s ease;
        }

        .slide.active { opacity: 1; }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .slider-btn {
            position: absolute;
            top: 50%;
            z-index: 3;
            width: 46px;
            height: 46px;
            border: 1px solid rgba(197, 160, 89, .42);
            border-radius: 50%;
            background: rgba(0, 0, 0, .64);
            color: var(--gold-main);
            display: grid;
            place-items: center;
            cursor: pointer;
            transform: translateY(-50%);
        }

        .slider-btn:hover,
        .slider-btn:focus-visible {
            color: #000;
            background: var(--gold-main);
        }

        .slider-btn svg {
            width: 22px;
            height: 22px;
            stroke-width: 2.2;
        }

        .prev { left: 14px; }
        .next { right: 14px; }

        .dots {
            position: absolute;
            z-index: 4;
            left: 50%;
            bottom: 14px;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
        }

        .dot {
            width: 7px;
            height: 7px;
            border: 1px solid rgba(255, 238, 187, .76);
            border-radius: 50%;
            background: transparent;
            padding: 0;
            cursor: pointer;
        }

        .dot.active {
            background: var(--gold-main);
            border-color: var(--gold-main);
        }

        .branch-info {
            padding: 18px clamp(16px, 2.2vw, 26px) 20px;
            background: linear-gradient(180deg, var(--surface-soft), #000);
        }

        .contact-btn {
            width: 54px;
            height: 54px;
            border: 2px solid rgba(197, 160, 89, 0.7);
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.6);
            color: var(--gold-main);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .contact-btn:hover,
        .contact-btn:focus-visible {
            background: var(--gold-main);
            color: #000;
            border-color: var(--gold-main);
            transform: translateY(-3px);
        }

        .contact-btn svg {
            width: 28px;
            height: 28px;
            stroke-width: 1.5;
        }

        .pin {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            color: var(--gold-main);
        }

        .branch-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            align-items: stretch;
        }

        @media (min-width: 881px) {
            .branch-actions {
                display: flex;
                align-items: center;
                gap: 16px;
                grid-template-columns: unset;
            }

            .branch-actions .button {
                flex: 1;
                min-height: 50px;
                padding: 14px 24px;
                font-size: 12px;
            }

            .branch-info-contact {
                display: flex;
                align-items: center;
                gap: 8px;
                flex: 1;
            }

            .phone-number {
                color: var(--gold-main);
                font-size: 13px;
                font-weight: 600;
                letter-spacing: 0.05em;
            }

            .phone-number a {
                color: inherit;
            }

            .phone-number a:hover {
                color: var(--gold-light);
            }

            .branch-info-contact .contact-btn {
                width: 50px;
                height: 50px;
                flex-shrink: 0;
                border: 2px solid rgba(197, 160, 89, 0.7);
            }

            .branch-info-contact .contact-btn svg {
                width: 26px;
                height: 26px;
            }
        }

        .button {
            min-height: 54px;
            border: 1px solid rgba(197, 160, 89, 0.85);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 16px 28px;
            color: #080806;
            background: linear-gradient(
                135deg,
                #c5a059 0%,
                #f3d78b 45%,
                #9f7a36 100%
            );
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            line-height: 1;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            text-align: center;
            border-radius: 6px;
            cursor: pointer;
            box-shadow:
                0 0 18px rgba(197, 160, 89, 0.28),
                0 12px 34px rgba(0, 0, 0, 0.38),
                inset 0 1px 0 rgba(255, 255, 255, 0.45);
            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease,
                filter 0.25s ease;
        }

        .button:hover,
        .button:focus-visible {
            transform: translateY(-2px);
            filter: brightness(1.08);
            box-shadow:
                0 0 26px rgba(197, 160, 89, 0.45),
                0 16px 42px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.55);
        }

        .button:active {
            transform: translateY(0) scale(0.98);
            box-shadow:
                0 0 14px rgba(197, 160, 89, 0.25),
                0 8px 22px rgba(0, 0, 0, 0.42);
        }

        .button:focus-visible {
            outline: none;
            box-shadow:
                0 0 0 3px rgba(197, 160, 89, 0.28),
                0 0 26px rgba(197, 160, 89, 0.45),
                0 16px 42px rgba(0, 0, 0, 0.5);
        }

        .socials {
            width: min(1100px, 100%);
            margin: clamp(22px, 3.4vw, 38px) auto 0;
            padding-top: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
        }

        .social-link {
            width: 42px;
            height: 42px;
            border: 2px solid rgba(197, 160, 89, 0.6);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: rgba(0, 0, 0, 0.5);
            cursor: pointer;
        }

        .social-link:hover,
        .social-link:focus-visible {
            background: var(--gold-main);
            color: #000;
            border-color: var(--gold-main);
            transform: translateY(-2px);
        }

        .social-link svg {
            width: 22px;
            height: 22px;
            fill: currentColor;
        }

        .social-link .stroke-icon {
            fill: none;
            stroke: currentColor;
            stroke-width: 1.9;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .sr-text {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
        }

        @media (max-width: 880px) {
            .page {
                padding: 20px 17px 24px;
            }

            .header {
                margin-bottom: 16px;
            }

            .logo {
                width: min(68vw, 286px);
                min-width: 236px;
            }

            .intro {
                margin-bottom: 18px;
            }

            h1 {
                font-size: clamp(31px, 8.4vw, 39px);
                line-height: 1;
            }

            .lead {
                font-size: 11px;
                letter-spacing: .14em;
            }

            .branches {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .branch-title {
                min-height: 82px;
                padding: 16px 12px 14px;
            }

            .branch-title h2 {
                font-size: clamp(25px, 6.9vw, 32px);
            }

            .branch-address {
                font-size: 11px;
            }

            .slider {
                aspect-ratio: 4 / 3;
            }

            .slider-btn {
                width: 42px;
                height: 42px;
            }

            .prev { left: 10px; }
            .next { right: 10px; }

            .branch-info {
                padding: 16px 14px 17px;
            }

            .branch-actions {
                gap: 10px;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .button {
                min-height: 48px;
                padding: 14px 24px;
                font-size: 12px;
            }

            .socials {
                margin-top: 22px;
                padding-top: 19px;
                gap: 12px;
            }

            .social-link {
                width: 40px;
                height: 40px;
            }

            .social-link svg {
                width: 20px;
                height: 20px;
            }
        }
    </style>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "@id": "https://garden-lounge.pro/#organization",
        "name": "Garden Lounge",
        "url": "https://garden-lounge.pro/",
        "logo": "https://garden-lounge.pro/img/logo3.webp",
        "sameAs": [
            "https://instagram.com/garden_lounge_spb/",
            "https://vk.com/loungegarden",
            "https://youtube.com/@garden.lounge",
            "https://t.me/gardenlounge_admiral"
        ]
    }
    </script>
</head>
<body>
    <main class="page">
        <header class="header">
            <a href="/" aria-label="Garden Lounge">
                <img class="logo" src="/img/logo3.webp" alt="Garden Lounge" width="360" height="152" decoding="async">
            </a>
        </header>

        <section class="branches" aria-label="Выбор филиала Garden Lounge">
            <article class="branch" data-slider>
                <div class="branch-title">
                    <h2>м. Адмиралтейская</h2>
                    <a class="branch-address" href="https://yandex.ru/maps/-/CPxBuF4-" target="_blank" rel="noopener">
                        <svg class="pin" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.5A7.5 7.5 0 0 0 4.5 10c0 5.3 6.45 11.05 6.72 11.29a1.18 1.18 0 0 0 1.56 0C13.05 21.05 19.5 15.3 19.5 10A7.5 7.5 0 0 0 12 2.5Zm0 10.25A2.75 2.75 0 1 1 12 7.25a2.75 2.75 0 0 1 0 5.5Z"/></svg>
                        наб. реки Мойки, 67-69
                    </a>
                </div>
                <div class="slider" aria-label="Слайдер фото филиала Адмиралтейская" data-branch="admiralteyskaya">
                    <div class="slide active"><img src="/admiralteyskaya/couch/uploads/image/garden-main.webp" alt="Garden Lounge Адмиралтейская"></div>
                    <div class="slide"><img src="/admiralteyskaya/couch/uploads/image/garden-2.webp" alt="Garden Lounge Адмиралтейская интерьер"></div>
                    <div class="slide"><img src="/admiralteyskaya/couch/uploads/image/safonovleonid_green_55.webp" alt="Garden Lounge Адмиралтейская зал"></div>
                    <button class="slider-btn prev" type="button" aria-label="Предыдущее фото">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <button class="slider-btn next" type="button" aria-label="Следующее фото">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                    <div class="dots" aria-hidden="true"></div>
                </div>
                <div class="branch-info">
                    <div class="branch-actions">
                        <a class="button" href="/admiralteyskaya/">Войти в оазис</a>
                        <div class="branch-info-contact">
                            <div class="phone-number">
                                <a href="tel:+79956246808">+7 995 624 68 08</a>
                            </div>
                            <a href="https://t.me/gardenlounge_admiral" target="_blank" rel="noopener" class="contact-btn" aria-label="Написать в Telegram на Адмиралтейскую" title="Telegram">
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.6 4.6 18.4 19c-.2 1-1 1.2-1.8.7l-4.8-3.5-2.3 2.2c-.3.3-.5.5-1 .5l.4-4.9 8.9-8c.4-.4-.1-.6-.6-.3L6.1 12.6 1.4 11.1c-1-.3-1-1 .2-1.5L20 2.5c.8-.3 1.6.2 1.6 2.1Z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </article>

            <article class="branch" data-slider>
                <div class="branch-title">
                    <h2>м. Удельная</h2>
                    <a class="branch-address" href="https://yandex.ru/maps/-/CPxBuAyI" target="_blank" rel="noopener">
                        <svg class="pin" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.5A7.5 7.5 0 0 0 4.5 10c0 5.3 6.45 11.05 6.72 11.29a1.18 1.18 0 0 0 1.56 0C13.05 21.05 19.5 15.3 19.5 10A7.5 7.5 0 0 0 12 2.5Zm0 10.25A2.75 2.75 0 1 1 12 7.25a2.75 2.75 0 0 1 0 5.5Z"/></svg>
                        ул. Аккуратова, 13
                    </a>
                </div>
                <div class="slider" aria-label="Слайдер фото филиала Удельная" data-branch="udelnaya">
                    <div class="slide active"><img src="/admiralteyskaya/couch/uploads/image/kalyannaya-garden-lounge-udelnaya-interer-spb.webp" alt="Garden Lounge Удельная"></div>
                    <div class="slide"><img src="/admiralteyskaya/couch/uploads/image/garden.webp" alt="Garden Lounge Удельная интерьер"></div>
                    <div class="slide"><img src="/admiralteyskaya/couch/uploads/image/safonovleonid_green_65.webp" alt="Garden Lounge Удельная зал"></div>
                    <button class="slider-btn prev" type="button" aria-label="Предыдущее фото">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <button class="slider-btn next" type="button" aria-label="Следующее фото">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                    <div class="dots" aria-hidden="true"></div>
                </div>
                <div class="branch-info">
                    <div class="branch-actions">
                        <a class="button" href="/udelnaya/">Выбрать сад</a>
                        <div class="branch-info-contact">
                            <div class="phone-number">
                                <a href="tel:+79500473365">+7 950 047 33 65</a>
                            </div>
                            <a href="https://t.me/gardenlounge_udelnaya" target="_blank" rel="noopener" class="contact-btn" aria-label="Написать в Telegram на Удельную" title="Telegram">
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.6 4.6 18.4 19c-.2 1-1 1.2-1.8.7l-4.8-3.5-2.3 2.2c-.3.3-.5.5-1 .5l.4-4.9 8.9-8c.4-.4-.1-.6-.6-.3L6.1 12.6 1.4 11.1c-1-.3-1-1 .2-1.5L20 2.5c.8-.3 1.6.2 1.6 2.1Z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </article>
        </section>

        <nav class="socials" aria-label="Социальные сети Garden Lounge">
            <a class="social-link" href="https://instagram.com/garden_lounge_spb/" target="_blank" rel="noopener" aria-label="Instagram Garden Lounge">
                <svg class="stroke-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="5"/><circle cx="12" cy="12" r="3.5"/><path d="M17.5 6.8h.01"/></svg>
            </a>
            <a class="social-link" href="https://vk.com/loungegarden" target="_blank" rel="noopener" aria-label="VK Garden Lounge">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.2 7.2h3.1c.1 3.8 1.7 5.4 2.9 5.7V7.2h2.9v3.3c1.1-.1 2.2-1.7 2.6-3.3h2.9c-.4 2-1.9 3.6-2.9 4.3 1.1.6 2.8 2 3.5 5.3h-3.2c-.4-1.5-1.5-2.8-2.9-3v3h-.4c-6 0-8.2-4.1-8.5-9.6Z"/></svg>
            </a>
            <a class="social-link" href="https://youtube.com/@garden.lounge" target="_blank" rel="noopener" aria-label="YouTube Garden Lounge">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21.2 8.2a3 3 0 0 0-2.1-2.1C17.2 5.6 12 5.6 12 5.6s-5.2 0-7.1.5a3 3 0 0 0-2.1 2.1 31 31 0 0 0 0 7.6 3 3 0 0 0 2.1 2.1c1.9.5 7.1.5 7.1.5s5.2 0 7.1-.5a3 3 0 0 0 2.1-2.1 31 31 0 0 0 0-7.6ZM10.2 15.4V8.6l5.8 3.4-5.8 3.4Z"/></svg>
            </a>
            <a class="social-link" href="https://t.me/gardenlounge_admiral" target="_blank" rel="noopener" aria-label="Telegram Garden Lounge">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21.6 4.6 18.4 19c-.2 1-1 1.2-1.8.7l-4.8-3.5-2.3 2.2c-.3.3-.5.5-1 .5l.4-4.9 8.9-8c.4-.4-.1-.6-.6-.3L6.1 12.6 1.4 11.1c-1-.3-1-1 .2-1.5L20 2.5c.8-.3 1.6.2 1.6 2.1Z"/></svg>
            </a>
        </nav>

        <p class="sr-text">Garden Lounge работает в двух филиалах Санкт-Петербурга: наб. реки Мойки 67-69 и ул. Аккуратова 13.</p>
    </main>

    <script>
        document.querySelectorAll('[data-slider]').forEach((branch) => {
            const slides = Array.from(branch.querySelectorAll('.slide'));
            const dotsWrap = branch.querySelector('.dots');
            const slider = branch.querySelector('.slider');
            const branchName = slider?.getAttribute('data-branch');
            let index = 0;

            slides.forEach((_, dotIndex) => {
                const dot = document.createElement('button');
                dot.className = `dot${dotIndex === 0 ? ' active' : ''}`;
                dot.type = 'button';
                dot.setAttribute('aria-label', `Показать фото ${dotIndex + 1}`);
                dot.addEventListener('click', (e) => {
                    e.stopPropagation();
                    show(dotIndex);
                });
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

            const prevBtn = branch.querySelector('.prev');
            const nextBtn = branch.querySelector('.next');

            prevBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                show(index - 1);
            });

            nextBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                show(index + 1);
            });

            // Click on slider to open branch
            slider.addEventListener('click', (e) => {
                if (!e.target.closest('button')) {
                    if (branchName === 'admiralteyskaya') {
                        window.location.href = '/admiralteyskaya/';
                    } else if (branchName === 'udelnaya') {
                        window.location.href = '/udelnaya/';
                    }
                }
            });
        });
    </script>
</body>
</html>
