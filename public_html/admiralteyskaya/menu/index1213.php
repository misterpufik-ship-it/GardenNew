<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lounge Garden — Меню</title>
    <!-- Добавление фавикона -->
    <link rel="icon" type="image/png" href="/favicon.png">
    
   
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500&family=Montserrat:wght@200;300;400;500;600&display=swap" rel="stylesheet">
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
            background: url('https://grainy-gradients.vercel.app/noise.svg');
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
            background: linear-gradient(to right, #8e7037 0%, var(--gold) 40%, var(--gold-light) 50%, var(--gold) 60%, #8e7037 100%);
            background-size: 200% auto;
            color: transparent;
            -webkit-background-clip: text;
            background-clip: text;
            animation: menuShine 5s linear infinite;
        }

        @keyframes menuShine {
            to { background-position: 200% center; }
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
    </style>
</head>
<body class="antialiased">
    <div class="film-grain"></div>

    <div class="min-h-screen flex flex-col items-center pt-4 pb-10 md:py-10 px-6">
        
        <!-- ЛОГОТИП -->
        <header class="mb-8 fade-up" style="animation-delay: 0.1s;">
            <a href="https://garden-lounge.pro/admiralteyskaya/" class="block">
                <img src="/img/logo3.webp" alt="Lounge Garden Logo" class="h-24 md:h-32 w-auto object-contain transition-transform hover:scale-105 duration-500">
            </a>
        </header>

        <!-- ОСНОВНОЙ БЛОК МЕНЮ -->
        <main class="w-full max-w-[600px] z-10">
            
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
                    <img src="/admiralteyskaya/couch/uploads/image/gf11.webp" alt="Визуальное меню" onerror="this.src='https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?q=80&w=800'">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>
                    
                    <div class="absolute inset-0 flex flex-col items-center justify-center p-4">
                        <h3 class="font-serif-lux text-2xl md:text-2xl italic text-white text-center m-0 leading-tight">
                            Визуальное<br>меню
                        </h3>
                        <div class="mt-6 flex items-center gap-2 opacity-60">
                            <i class="fas fa-camera text-[10px] text-[#C5A059]"></i>
                            <span class="text-[9px] uppercase tracking-[0.2em] text-[#C5A059]">Открыть галерею</span>
                        </div>
                    </div>
                </a>
                
                <!-- Ссылки (Правая часть) -->
                <div class="card-right">
                    <a href="/admiralteyskaya/menu/text" class="menu-btn group">
                        <h3 class="font-serif-lux text-2xl italic text-[#C5A059] m-0 mb-1 group-hover:scale-105 transition-transform">Текстовое</h3>
                        <span class="text-[8px] uppercase tracking-[0.2em] text-white/40">Classic List</span>
                    </a>
                    
                    <div class="card-divider"></div>
                    
                    <a href="/admiralteyskaya/menu/english" class="menu-btn group">
                        <h3 class="font-serif-lux text-2xl italic text-[#C5A059] m-0 mb-1 group-hover:scale-105 transition-transform">English</h3>
                        <span class="text-[8px] uppercase tracking-[0.2em] text-white/40">classic list</span>
                    </a>
                </div>
            </div>

            <!-- ПОДПИСЬ -->
            <div class="mt-8 text-center fade-up" style="animation-delay: 0.6s;">
                <p class="text-[10px] uppercase tracking-[0.3em] font-medium shimmer-gold m-0 mb-4">
                    Гастрономическая поэзия
                </p>

                <!-- Кнопка возврата -->
                <a href="https://garden-lounge.pro/admiralteyskaya/" class="back-button">
                    <i class="fas fa-chevron-left text-[8px]"></i>
                    Вернуться на главную
                </a>
            </div>
        </main>
    </div>
</body>
</html>