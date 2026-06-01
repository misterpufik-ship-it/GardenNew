<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garden Lounge | Luxury Experience</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/favicon.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500&family=Montserrat:wght@200;300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #000000;
            --gold: #C5A059;
            --gold-light: #FFEebb;
            --gold-dark: #8e7037;
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

        /* Эффект зернистости пленки на фоне */
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

        .fade-up {
            animation: menuFadeIn 1s ease-out forwards;
            opacity: 0;
        }

        @keyframes menuFadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Навигация */
        .nav-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
            margin-bottom: 12px;
        }

        @media (min-width: 768px) {
            .nav-container {
                gap: 40px;
            }
        }

        .nav-item {
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-size: 14px;
            color: #ffffff;
            text-decoration: none;
            padding-bottom: 8px;
            transition: all 0.3s ease;
            position: relative;
            font-weight: 300;
            cursor: pointer;
        }

        .nav-item::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 1px;
            background-color: var(--gold);
            transition: width 0.3s ease;
        }

        .nav-item.active {
            color: var(--gold);
        }

        .nav-item.active::after {
            width: 100%;
        }

        .hero-image-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
            border-radius: 2px;
        }
        
        #hero-image {
            width: 100%;
            height: auto;
            display: block;
            opacity: 0.9;
            transition: opacity 0.5s ease;
        }

        /* Кнопки */
        .action-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 28px;
            border: 1px solid rgba(197, 160, 89, 0.4);
            color: var(--gold);
            text-transform: uppercase;
            letter-spacing: 0.2em;
            font-size: 11px;
            font-weight: 500;
            transition: all 0.4s ease;
            text-decoration: none;
            width: 100%;
            max-width: 320px;
            text-align: center;
            box-sizing: border-box;
        }

        /* Золотая переливающаяся кнопка лояльности */
        .button-gold-shimmer {
            background: linear-gradient(to right, var(--gold-dark) 0%, var(--gold) 40%, var(--gold-light) 50%, var(--gold) 60%, var(--gold-dark) 100%);
            background-size: 200% auto;
            border: 1px solid transparent;
            animation: menuShine 4s linear infinite;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }

        .button-gold-shimmer .loyalty-text {
            color: white;
            transition: all 0.4s ease;
        }

        /* Состояние при наведении для кнопки лояльности */
        .button-gold-shimmer:hover {
            background: transparent !important;
            border: 1px solid rgba(197, 160, 89, 0.4);
            box-shadow: none;
        }

        .button-gold-shimmer:hover .loyalty-text {
            background: linear-gradient(to right, #8e7037 0%, var(--gold) 40%, var(--gold-light) 50%, var(--gold) 60%, #8e7037 100%);
            background-size: 200% auto;
            color: transparent;
            -webkit-background-clip: text;
            background-clip: text;
            animation: menuShine 5s linear infinite;
        }

        .action-button:not(.button-gold-shimmer):hover {
            background: rgba(197, 160, 89, 0.1);
        }

        /* Эффект мерцающего золотого текста */
        .shimmer-gold-text {
            background: linear-gradient(to right, #8e7037 0%, var(--gold) 40%, var(--gold-light) 50%, var(--gold) 60%, #8e7037 100%);
            background-size: 200% auto;
            color: transparent;
            -webkit-background-clip: text;
            background-clip: text;
            animation: menuShine 5s linear infinite;
            display: inline-block;
        }

        @keyframes menuShine {
            to { background-position: 200% center; }
        }
    </style>
</head>
<body class="antialiased">
    <div class="film-grain"></div>

    <div class="min-h-screen flex flex-col items-center pt-10 pb-16 px-3 md:px-6">
        
        <!-- ШАПКА / ЛОГОТИП -->
        <header class="w-full max-w-[600px] text-center mb-8 fade-up" style="animation-delay: 0.1s;">
            <a href="https://garden-lounge.pro/admiralteyskaya" class="inline-block mb-6">
                <img src="https://garden-lounge.pro/img/logo3.png" alt="Garden Lounge" class="h-32 md:h-40 w-auto mx-auto transition-transform hover:scale-105 duration-500">
            </a>
            
            <!-- НАВИГАЦИЯ -->
            <nav class="nav-container" id="main-nav">
                <div class="nav-item active" data-image="https://garden-lounge.pro/img/312.png">HOOKAH</div>
                <div class="nav-item" data-image="https://garden-lounge.pro/img/1.webp">BAR</div>
                <div class="nav-item" data-image="https://garden-lounge.pro/img/312.png">KITCHEN</div>
                <div class="nav-item" data-image="https://garden-lounge.pro/img/1.webp">OFFERS</div>
            </nav>
        </header>

        <!-- ОСНОВНОЙ КОНТЕНТ -->
        <main class="w-full max-w-[600px] z-10 flex flex-col items-center">
            
            <!-- ГЛАВНОЕ ИЗОБРАЖЕНИЕ -->
            <div class="hero-image-container fade-up" style="animation-delay: 0.2s;">
                <img id="hero-image" src="https://garden-lounge.pro/img/312.png" alt="Lounge Interior">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none"></div>
            </div>

            <!-- РАЗДЕЛИТЕЛЬ -->
            <div class="fade-up w-full flex justify-center" style="animation-delay: 0.25s;">
                <img src="https://garden-lounge.pro/img/div.png" alt="Section Divider" class="w-full max-w-[280px] h-auto opacity-40 object-contain my-10">
            </div>

            <!-- КНОПКИ ДЕЙСТВИЯ -->
            <div class="flex flex-col md:flex-row items-center justify-center gap-4 w-full fade-up px-3 md:px-0" style="animation-delay: 0.3s;">
                
                <!-- Кнопка программы лояльности -->
                <a href="https://t.me/GardenLounge_Loyalty_Bot" target="_blank" class="action-button button-gold-shimmer">
                    <span class="loyalty-text">Loyalty Program</span>
                </a>

                <!-- Кнопка назад -->
                <a href="https://garden-lounge.pro/admiralteyskaya/menu" class="action-button">
                    <span class="shimmer-gold-text">Go Back</span>
                </a>
            </div>

            <!-- ТЕГЛАЙН -->
            <div class="mt-12 text-center fade-up" style="animation-delay: 0.4s;">
                <p class="text-[10px] uppercase tracking-[0.4em] font-medium shimmer-gold-text opacity-80">
                    The Aesthetics of Your Leisure
                </p>
            </div>

        </main>

        <footer class="mt-auto pt-16 opacity-20 text-[9px] tracking-widest uppercase text-center">
            <!-- Футер пуст по запросу -->
        </footer>
    </div>

    <script>
        // Логика переключения вкладок и обновления изображений
        const navItems = document.querySelectorAll('.nav-item');
        const heroImage = document.getElementById('hero-image');

        navItems.forEach(item => {
            item.addEventListener('click', () => {
                // Обновление активного состояния
                navItems.forEach(nav => nav.classList.remove('active'));
                item.classList.add('active');
                
                // Исчезновение текущего изображения
                heroImage.style.opacity = '0';
                
                // Смена источника и появление
                setTimeout(() => {
                    heroImage.src = item.getAttribute('data-image');
                    heroImage.style.opacity = '0.9';
                }, 300);
            });
        });
    </script>
</body>
</html>