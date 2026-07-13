<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Меню Garden Lounge — Адмиралтейская</title>
    <meta name="description" content="Меню Garden Lounge на Адмиралтейской: текстовое, визуальное и английское меню — кальяны, кухня, бар в центре Санкт-Петербурга.">
    <link rel="canonical" href="https://garden-lounge.pro/admiralteyskaya/menu">
    <?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/age-gate/menu-schema.php';
    gl_menu_og_render(array(
        'branch' => 'admiralteyskaya',
        'url' => 'https://garden-lounge.pro/admiralteyskaya/menu',
        'title' => 'Меню Garden Lounge — Адмиралтейская',
        'description' => 'Меню Garden Lounge на Адмиралтейской: текстовое, визуальное и английское меню — кальяны, кухня, бар в центре Санкт-Петербурга.',
    ));
    ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "@id": "https://garden-lounge.pro/admiralteyskaya/menu#webpage",
        "url": "https://garden-lounge.pro/admiralteyskaya/menu",
        "name": "Меню Garden Lounge — Адмиралтейская",
        "description": "Меню Garden Lounge на Адмиралтейской: текстовое, визуальное и английское меню — кальяны, кухня, бар в центре Санкт-Петербурга.",
        "isPartOf": { "@id": "https://garden-lounge.pro/admiralteyskaya#localbusiness" },
        "hasPart": [
            { "@type": "WebPage", "url": "https://garden-lounge.pro/admiralteyskaya/menu/text", "name": "Текстовое меню" },
            { "@type": "WebPage", "url": "https://garden-lounge.pro/admiralteyskaya/menu/visual", "name": "Визуальное меню" },
            { "@type": "WebPage", "url": "https://garden-lounge.pro/admiralteyskaya/menu/english", "name": "English menu" }
        ]
    }
    </script>
    <?php
    gl_menu_seo_schema_render(array(
        'branch' => 'admiralteyskaya',
        'page' => 'hub',
        'url' => 'https://garden-lounge.pro/admiralteyskaya/menu',
        'name' => 'Меню Garden Lounge — Адмиралтейская',
    ));
    ?>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/age-gate/assets.php'; gl_render_head_assets(); gl_menu_page_head_assets(); ?>
    <style>
        :root {
            --bg-color: #000000;
            --gold: #C5A059;
            --gold-light: #FFEebb;
            --text-gray: #EAEAEA;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-gray);
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Эффект кинопленки */
        .film-grain {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('/img/noise.svg');
            opacity: 0.04;
            pointer-events: none;
            z-index: 50;
        }

        .font-serif-lux {
            font-family: 'Cormorant Garamond', serif;
        }

        /* Анимация появления */
        .fade-up {
            animation: menuFadeIn 1s ease-out forwards;
            opacity: 0;
        }

        @keyframes menuFadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Золотое свечение текста */
        .shimmer-gold {
            background-color: transparent;
            background-image: linear-gradient(90deg,
                #8e7037 0%, var(--gold) 20%, var(--gold-light) 25%, var(--gold) 30%, #8e7037 50%,
                #8e7037 50%, var(--gold) 70%, var(--gold-light) 75%, var(--gold) 80%, #8e7037 100%);
            background-repeat: no-repeat;
            background-size: 200% auto;
            background-position: 0% center;
            color: transparent;
            -webkit-text-fill-color: transparent;
            -webkit-background-clip: text;
            background-clip: text;
            animation: menuShine 5s linear infinite;
            -webkit-animation: menuShine 5s linear infinite;
        }

        .text-center > .shimmer-gold {
            display: block;
            width: 100%;
        }

        @keyframes menuShine {
            0% { background-position: 0% center; }
            100% { background-position: 100% center; }
        }
        @-webkit-keyframes menuShine {
            0% { background-position: 0% center; }
            100% { background-position: 100% center; }
        }

        /* Стили карточки */
        .main-card {
            background-color: #0a0a0a;
            height: 280px;
            display: flex;
            border: 1px solid rgba(197, 160, 89, 0.2);
            box-shadow: 0 15px 35px rgba(0,0,0,0.8);
            position: relative;
            overflow: hidden;
            transition: border-color 0.4s ease;
        }

        .main-card:hover {
            border-color: rgba(197, 160, 89, 0.5);
        }

        @media (min-width: 768px) {
            .main-card { height: 320px; }
        }

        .card-left {
            width: 55%;
            height: 100%;
            position: relative;
            border-right: 1px solid rgba(197, 160, 89, 0.2);
            overflow: hidden;
        }

        .card-left img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.5;
            transition: transform 1.5s ease, opacity 0.5s ease;
        }

        .card-left:hover img {
            transform: scale(1.1);
            opacity: 0.7;
        }

        .card-right {
            width: 45%;
            display: flex;
            flex-direction: column;
        }

        .menu-btn {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none !important;
            color: inherit !important;
            transition: all 0.3s ease;
        }

        .menu-btn:hover {
            background: rgba(197, 160, 89, 0.08);
        }

        .card-divider {
            height: 1px;
            width: 100%;
            background: rgba(197, 160, 89, 0.15);
        }

        .golden-line {
            width: 140px;
            height: 1px;
            background: linear-gradient(to right, transparent, var(--gold), transparent);
            margin: 16px auto;
        }

        /* Кнопка возврата */
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            border: 1px solid rgba(197, 160, 89, 0.3);
            border-radius: 0px;
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 0.2em;
            font-size: 10px;
            transition: all 0.4s ease;
            text-decoration: none;
            margin-top: 10px;
        }

        .back-button:hover {
            background: var(--gold);
            color: black;
            border-color: var(--gold);
        }

        #loyalty-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(10px); display: none; justify-content: center; align-items: center; z-index: 3000; padding: 20px; }
        .modal-content { background: #0a0a0a; border: 1px solid var(--gold); padding: 40px 25px; width: 100%; max-width: 400px; text-align: center; position: relative; box-shadow: 0 0 30px rgba(197, 160, 89, 0.2); }
        .modal-title { font-family: 'Cormorant Garamond', serif; font-style: italic; font-size: 24px; margin-bottom: 25px; color: #fff; line-height: 1.2; }
        .modal-btn { display: flex; align-items: center; justify-content: center; gap: 12px; width: 100%; height: 54px; border: 1px solid rgba(197, 160, 89, 0.3); margin-bottom: 12px; color: #fff !important; text-decoration: none; text-transform: uppercase; font-size: 11px; letter-spacing: 0.1em; transition: 0.3s; }
        .modal-btn:hover { background: rgba(197, 160, 89, 0.1); border-color: var(--gold); }
        .modal-btn i { font-size: 18px; color: var(--gold); }
        .close-modal { position: absolute; top: 10px; right: 15px; font-size: 28px; color: rgba(255,255,255,0.3); cursor: pointer; line-height: 1; }
        .close-modal:hover { color: #fff; }

        .action-area { display: flex; flex-direction: column; align-items: center; gap: 10px; margin-top: 16px; }
        @media (min-width: 768px) { .action-area { flex-direction: row; justify-content: center; gap: 15px; } }
        .btn-base { display: flex; align-items: center; justify-content: center; width: 100%; max-width: 280px; height: 52px; border: 1px solid rgba(197,160,89,0.3); text-transform: uppercase; font-size: 10px; letter-spacing: 0.15em; text-decoration: none; transition: 0.3s; cursor: pointer; color: #fff; }
        .btn-base:hover:not(.btn-gold-fill) { border-color: var(--gold); background: rgba(197,160,89,0.05); }
        .btn-gold-fill { background: var(--gold); color: #000 !important; font-weight: 700; border: none; }
        .btn-gold-fill:hover { background: var(--gold); color: #000 !important; border: none; }
    </style>
</head>
<body class="antialiased">
    <div class="film-grain"></div>

    <div class="min-h-screen flex flex-col items-center pt-4 pb-10 md:py-10 px-6">
        
        <!-- ЛОГОТИП -->
        <header class="mb-8 fade-up" style="animation-delay: 0.1s;">
            <a href="https://garden-lounge.pro/admiralteyskaya" class="block">
                <img src="/admiralteyskaya/couch/uploads/image/logo3.webp" alt="Lounge Garden Logo" class="h-24 md:h-32 w-auto object-contain transition-transform hover:scale-105 duration-500" width="384" height="162" decoding="async">
            </a>
        </header>

        <!-- ОСНОВНОЙ БЛОК МЕНЮ -->
        <main class="w-full max-w-[600px] mx-auto z-10">
            
            <!-- Заголовок секции -->
            <div class="text-center mb-6 fade-up" style="animation-delay: 0.2s;">
                <h2 class="font-serif-lux text-4xl text-white font-light italic m-0">Меню</h2>
                <div class="golden-line"></div>
                <p class="text-[12px] uppercase tracking-[0.4em] font-medium shimmer-gold m-0">Эстетика вкуса</p>
            </div>

            <!-- ГЛАВНАЯ КАРТОЧКА -->
            <div class="main-card fade-up" style="animation-delay: 0.4s;">
                
                <!-- Визуальное меню (Левая часть) -->
                <a href="/admiralteyskaya/menu/visual" class="card-left">
                    <img src="/admiralteyskaya/couch/uploads/image/gf11.webp" alt="Визуальное меню" loading="lazy" decoding="async">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
                    
                    <div class="absolute inset-0 flex flex-col items-center justify-center p-4">
                        <h3 class="font-serif-lux text-2xl italic text-white text-center m-0 mb-1 leading-tight">
                            Визуальное меню
                        </h3>
                        <div class="mt-6 flex items-center gap-2 opacity-60">
                            <i class="fas fa-camera text-[10px] shimmer-gold"></i>
                            <span class="text-[9px] uppercase tracking-[0.2em] shimmer-gold">Открыть галерею</span>
                        </div>
                    </div>
                </a>
                
                <!-- Ссылки (Правая часть) -->
                <div class="card-right">
                    <a href="/admiralteyskaya/menu/text" class="menu-btn group">
                        <h3 class="font-serif-lux text-2xl italic text-white m-0 mb-1 group-hover:scale-105 transition-transform">Текстовое</h3>
                        <span class="text-[8px] uppercase tracking-[0.2em] shimmer-gold">Classic List</span>
                    </a>
                    
                    <div class="card-divider"></div>
                    
                    <a href="/admiralteyskaya/menu/english" class="menu-btn group">
                        <h3 class="font-serif-lux text-2xl italic text-white m-0 mb-1 group-hover:scale-105 transition-transform">English</h3>
                        <span class="text-[8px] uppercase tracking-[0.2em] shimmer-gold">classic list</span>
                    </a>
                </div>
            </div>

            <!-- ПОДПИСЬ -->
            <div class="mt-8 text-center fade-up" style="animation-delay: 0.6s;">
                <p class="text-[10px] uppercase tracking-[0.3em] font-medium shimmer-gold m-0 mb-4">
                    Гастрономическая поэзия
                </p>

                <div class="action-area">
                    <a href="https://garden-lounge.pro/admiralteyskaya" class="btn-base">
                        <span class="subtitle-gold">Вернуться на главную</span>
                    </a>
                    <div onclick="openLoyaltyModal()" class="btn-base btn-gold-fill">Программа лояльности</div>
                </div>
            </div>
        </main>
    </div>

    <div id="loyalty-modal" onclick="closeLoyaltyModal()">
        <div class="modal-content" onclick="event.stopPropagation()">
            <span class="close-modal" onclick="closeLoyaltyModal()">&times;</span>
            <div class="modal-title gold-shimmer">Выберите способ регистрации</div>

            <a href="https://access.clientomer.ru/feedback/676900-1/" target="_blank" rel="noopener" class="modal-btn">
                <i class="fa-solid fa-wallet"></i> Регистрация через Wallet
            </a>

            <a href="https://t.me/GardenLounge_Loyalty_Bot" target="_blank" rel="noopener" class="modal-btn">
                <i class="fa-brands fa-telegram"></i> Регистрация через Telegram
            </a>
        </div>
    </div>

    <script>
    function openLoyaltyModal() {
        document.getElementById('loyalty-modal').style.display = 'flex';
    }
    function closeLoyaltyModal() {
        document.getElementById('loyalty-modal').style.display = 'none';
    }
    </script>
</body>
</html>
