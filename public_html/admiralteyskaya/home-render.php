<?php require_once( 'couch/cms.php' ); ?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <cms:pages masterpage='home.php' limit='1'>
    <title><cms:show home_seo_title /></title>
    <meta name="description" content="<cms:show home_seo_desc />">
    <meta name="keywords" content="<cms:show home_seo_keywords />">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://garden-lounge.pro/">
    <link rel="icon" type="image/png" href="<cms:if home_favicon><cms:show home_favicon /><cms:else />/udelnaya/favicon.png</cms:if>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://garden-lounge.pro/">
    <meta property="og:title" content="<cms:show home_seo_title />">
    <meta property="og:description" content="<cms:show home_seo_desc />">
    <meta property="og:image" content="<cms:if home_seo_og_image><cms:show home_seo_og_image /><cms:else />https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/garden-main.jpg</cms:if>">
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
            --home-icon-color: <cms:show home_icon_color />;
            --home-icon-border-color: <cms:show home_icon_border_color />;
            --home-icon-border-width: <cms:show home_icon_border_width />px;
            --home-icon-size: <cms:show home_icon_size />px;
            --home-icon-bg: <cms:show home_icon_bg />;
            --home-social-size: <cms:show home_social_size />px;
            --home-social-border-width: <cms:show home_social_border_width />px;
        }

        body.home-icon-hover-gold .contact-btn:hover,
        body.home-icon-hover-gold .contact-btn:focus-visible,
        body.home-icon-hover-gold .social-link:hover,
        body.home-icon-hover-gold .social-link:focus-visible {
            background: var(--home-icon-color);
            color: #000;
            border-color: var(--home-icon-color);
        }

        body.home-icon-hover-light .contact-btn:hover,
        body.home-icon-hover-light .contact-btn:focus-visible,
        body.home-icon-hover-light .social-link:hover,
        body.home-icon-hover-light .social-link:focus-visible {
            color: var(--gold-light);
            border-color: var(--gold-light);
        }

        body.home-icon-hover-none .contact-btn:hover,
        body.home-icon-hover-none .contact-btn:focus-visible,
        body.home-icon-hover-none .social-link:hover,
        body.home-icon-hover-none .social-link:focus-visible {
            transform: translateY(-2px);
        }

        body.home-phone-animation-off .contact-btn.phone {
            animation: none;
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
            overflow: visible;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .035);
            cursor: pointer;
        }

        .slider:hover {
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .035), 0 0 20px rgba(197, 160, 89, 0.2);
        }

        .slider-overlay {
            position: absolute;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            text-align: center;
            z-index: 5;
            pointer-events: none;
        }

        .slider-overlay > * {
            pointer-events: auto;
        }

        @keyframes breatheBtn {
            0%, 100% { opacity: 0.42; }
            50% { opacity: 0.88; }
        }

        .slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            overflow: hidden;
            transition: opacity .6s ease;
            will-change: opacity;
        }

        .slider-cta,
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
            padding: 16px 32px;
            min-width: 220px;
            min-height: 52px;
            border-radius: 0;
            border: 1px solid rgba(166, 138, 92, 0.75);
            background: rgba(18, 16, 14, 0.55);
            color: #a68a5c;
            text-decoration: none;
            font-family: Montserrat, Arial, sans-serif;
            font-size: 11px;
            font-weight: 600;
            font-style: normal;
            letter-spacing: 0.18em;
            line-height: 1.2;
            text-align: center;
            text-transform: uppercase;
            white-space: nowrap;
            cursor: pointer;
            box-shadow: none;
            text-shadow: none;
            transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease;
            animation: breatheBtn 4.5s ease-in-out infinite;
        }

        .slider-cta:hover,
        .slider-cta:focus-visible,
        .button:hover,
        .button:focus-visible {
            transform: translateY(-1px);
            opacity: 1;
            animation-play-state: paused;
            border-color: rgba(197, 160, 89, 0.95);
            background: rgba(18, 16, 14, 0.78);
            color: #c5a059;
        }

        .slider-overlay .contact-btn {
            position: absolute;
            bottom: 18px;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            border: 1px solid rgba(197, 160, 89, 0.65);
            background: rgba(0, 0, 0, 0.45);
            display: grid;
            place-items: center;
            opacity: 0.95;
            box-shadow: 0 0 18px rgba(0, 0, 0, 0.35);
        }

        .slider-overlay .contact-btn.phone {
            left: 18px;
        }

        .slider-overlay .contact-btn:last-of-type {
            right: 18px;
        }

        .slider-overlay .contact-btn:hover,
        .slider-overlay .contact-btn:focus-visible {
            opacity: 1;
        }

        .slider-btn {
            position: absolute;
            top: 50%;
            z-index: 3;
            width: 34px;
            height: 34px;
            border: 1px solid rgba(197, 160, 89, .42);
            border-radius: 50%;
            background: rgba(0, 0, 0, .54);
            color: var(--gold-main);
            display: grid;
            place-items: center;
            cursor: pointer;
            transform: translateY(-50%);
        }

        .slide.active { opacity: 1; }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            display: block;
        }

        .slider-btn {
            position: absolute;
            top: 50%;
            z-index: 3;
            padding: 8px;
            background: transparent;
            border: none;
            color: var(--gold-main);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transform: translateY(-50%);
        }

        .slider-btn:hover,
        .slider-btn:focus-visible {
            color: var(--gold-light);
            background: transparent;
        }

        .slider-btn svg {
            width: 20px;
            height: 20px;
            stroke-width: 2.2;
        }

        .prev { left: -10px; }
        .next { right: -10px; }

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

        @keyframes ringPhone {
            0%, 100% {
                transform: rotate(0deg) scale(1);
                box-shadow: 0 0 0 0 rgba(197, 160, 89, 0.18);
            }
            10% { transform: rotate(-18deg) scale(1.03); }
            20% { transform: rotate(14deg) scale(1.03); }
            30% { transform: rotate(-12deg) scale(1.02); }
            40% { transform: rotate(10deg) scale(1.02); }
            50% {
                transform: rotate(0deg) scale(1.05);
                box-shadow: 0 0 0 8px rgba(197, 160, 89, 0);
            }
        }

        .branch-info {
            padding: 18px clamp(16px, 2.2vw, 26px) 20px;
            background: linear-gradient(180deg, var(--surface-soft), #000);
        }

        .contact-btn {
            width: var(--home-icon-size);
            height: var(--home-icon-size);
            border: var(--home-icon-border-width) solid var(--home-icon-border-color);
            border-radius: 50%;
            background: var(--home-icon-bg);
            color: var(--home-icon-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .contact-btn:hover,
        .contact-btn:focus-visible {
            transform: translateY(-2px);
        }

        .contact-btn svg {
            width: 24px;
            height: 24px;
            stroke-width: 1.5;
        }

        .contact-btn.phone {
            animation: ringPhone 2.8s ease-in-out infinite;
        }

        .branch-action-icons {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: flex-end;
        }

        .branch-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
        }

        .branch-actions .button {
            flex: 1 1 220px;
        }

        .pin {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            color: var(--gold-main);
        }

        @media (min-width: 881px) {
            .branch-info {
                display: none;
            }

            .slider-overlay {
                display: flex;
            }
        }

        @media (max-width: 880px) {
            .branch-info {
                display: none;
            }

            .branch-action-icons {
                justify-content: flex-start;
            }

            .slider-overlay {
                display: flex;
            }
        }

        .button:active {
            transform: translateY(0) scale(0.98);
        }

        .button:focus-visible {
            outline: none;
            box-shadow: 0 0 0 2px rgba(197, 160, 89, 0.35);
        }

        .socials {
            width: min(1100px, 100%);
            margin: clamp(22px, 3.4vw, 38px) auto 0;
            padding-top: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .social-link {
            width: var(--home-social-size);
            height: var(--home-social-size);
            border: var(--home-social-border-width) solid var(--home-icon-border-color);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: var(--home-icon-bg);
            cursor: pointer;
        }

        .social-link:hover,
        .social-link:focus-visible {
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
            stroke-width: 2.6;
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
                aspect-ratio: 16 / 10;
            }

            .slider-btn {
                width: 42px;
                height: 42px;
            }

            .prev { left: -6px; }
            .next { right: -6px; }

            .branch-info {
                padding: 16px 14px 17px;
            }

            .branch-actions {
                gap: 10px;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .button,
            .slider-cta {
                min-height: 48px;
                padding: 14px 24px;
                font-size: 10px;
                letter-spacing: 0.16em;
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
        "logo": "<cms:if home_logo><cms:show home_logo /><cms:else />https://garden-lounge.pro/img/logo3.webp</cms:if>",
        "sameAs": [
            "<cms:show home_instagram />",
            "<cms:show home_vk />",
            "<cms:show home_youtube />",
            "<cms:show home_telegram />"
        ]
    }
    </script>
    <cms:set home_body_class='home-icon-hover-gold' scope='global' />
    <cms:if home_icon_hover_style='light'><cms:set home_body_class='home-icon-hover-light' scope='global' /></cms:if>
    <cms:if home_icon_hover_style='none'><cms:set home_body_class='home-icon-hover-none' scope='global' /></cms:if>
    <cms:if home_phone_animation='0'><cms:set home_body_class="<cms:show home_body_class /> home-phone-animation-off" scope='global' /></cms:if>
    </cms:pages>
</head>
<body class="<cms:show home_body_class />">
    <main class="page">
        <cms:pages masterpage='home.php' limit='1'>
            <cms:embed 'home-page.html' />
        </cms:pages>
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
                dot.setAttribute('aria-label', `╨Я╨╛╨║╨░╨╖╨░╤В╤М ╤Д╨╛╤В╨╛ ${dotIndex + 1}`);
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
                if (!e.target.closest('button') && !e.target.closest('a')) {
                    const branchLink = slider.getAttribute('data-branch-link');
                    if (branchLink) {
                        window.location.href = branchLink;
                    }
                }
            });
        });
    </script>
</body>
</html>
<?php COUCH::invoke(); ?>
