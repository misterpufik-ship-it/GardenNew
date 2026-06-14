<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garden Lounge — кальянные и лаунж-бары в Санкт-Петербурге</title>
    <meta name="description" content="Garden Lounge в Санкт-Петербурге: выберите филиал на Адмиралтейской или Удельной. Кальяны, кухня, бар, VIP-комнаты, PS5, меню и бронирование столика.">
    <meta name="keywords" content="Garden Lounge, кальянная СПб, лаунж бар СПб, кальянная Адмиралтейская, кальянная Удельная, кальянная Санкт-Петербург, VIP-комнаты, PS5, кухня">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://garden-lounge.pro/">
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://garden-lounge.pro/">
    <meta property="og:title" content="Garden Lounge — кальянные и лаунж-бары в СПб">
    <meta property="og:description" content="Два филиала Garden Lounge в Санкт-Петербурге: Адмиралтейская и Удельная. Выберите локацию, посмотрите меню и забронируйте столик.">
    <meta property="og:image" content="https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/garden-main.jpg">
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="Garden Lounge — кальянные и лаунж-бары в СПб">
    <meta property="twitter:description" content="Выбор филиала Garden Lounge: Адмиралтейская или Удельная. Кальянная, лаунж-бар, кухня, VIP-комнаты и бронь.">
    <meta property="twitter:image" content="https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/garden-main.jpg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --gold: #C5A059; --gold-soft: #e7cf91; --bg: #030403; --text: #f7f3ea; --muted: rgba(247,243,234,.72); }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: var(--bg); color: var(--text); font-family: Montserrat, Arial, sans-serif; overflow-x: hidden; }
        body:before { content: ""; position: fixed; inset: 0; pointer-events: none; z-index: 1; background: radial-gradient(circle at 50% 0%, rgba(197,160,89,.16), transparent 34%), linear-gradient(180deg, rgba(0,0,0,.15), #030403 78%); }
        a { color: inherit; text-decoration: none; }
        .page { position: relative; z-index: 2; min-height: 100vh; padding: 28px clamp(18px, 4vw, 64px) 34px; display: flex; flex-direction: column; }
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 20px; min-height: 74px; }
        .logo { width: clamp(168px, 20vw, 260px); height: auto; display: block; }
        .top-actions { display: flex; gap: 14px; align-items: center; font-size: 11px; letter-spacing: .18em; text-transform: uppercase; color: var(--gold-soft); }
        .top-actions a { border: 1px solid rgba(197,160,89,.42); padding: 12px 16px; transition: background .25s, color .25s; }
        .top-actions a:hover { background: var(--gold); color: #050505; }
        .intro { max-width: 960px; margin: clamp(18px, 5vh, 54px) auto clamp(22px, 5vh, 44px); text-align: center; }
        h1 { font-family: "Cormorant Garamond", serif; font-size: clamp(46px, 8vw, 110px); line-height: .9; font-weight: 300; margin: 0 0 22px; letter-spacing: 0; }
        .lead { margin: 0 auto; max-width: 760px; color: var(--muted); font-size: clamp(15px, 1.6vw, 18px); line-height: 1.8; }
        .seo-answer { max-width: 860px; margin: 18px auto 0; color: rgba(247,243,234,.62); font-size: 13px; line-height: 1.7; }
        .branches { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: clamp(18px, 3vw, 34px); width: min(1220px, 100%); margin: 0 auto; }
        .branch { border: 1px solid rgba(197,160,89,.34); background: rgba(5,6,5,.82); min-height: 560px; display: grid; grid-template-rows: minmax(300px, 1fr) auto; overflow: hidden; }
        .slider { position: relative; min-height: 300px; background: #080808; }
        .slide { position: absolute; inset: 0; opacity: 0; transition: opacity .5s ease; }
        .slide.active { opacity: 1; }
        .slide img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .slider:after { content: ""; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(0,0,0,.12), rgba(0,0,0,.72)); pointer-events: none; }
        .slider-btn { position: absolute; top: 50%; z-index: 3; width: 42px; height: 42px; transform: translateY(-50%); border: 1px solid rgba(197,160,89,.55); background: rgba(0,0,0,.42); color: var(--gold-soft); cursor: pointer; font-size: 24px; line-height: 1; }
        .slider-btn:hover { background: var(--gold); color: #070707; }
        .prev { left: 16px; }
        .next { right: 16px; }
        .branch-body { padding: clamp(22px, 3vw, 34px); }
        .eyebrow { color: var(--gold); font-size: 11px; letter-spacing: .28em; text-transform: uppercase; margin: 0 0 12px; }
        h2 { font-family: "Cormorant Garamond", serif; font-size: clamp(34px, 4vw, 54px); font-weight: 300; margin: 0 0 12px; letter-spacing: 0; }
        .address { color: var(--muted); line-height: 1.7; margin: 0 0 18px; font-size: 14px; }
        .facts { display: flex; flex-wrap: wrap; gap: 8px; margin: 0 0 24px; padding: 0; list-style: none; }
        .facts li { border: 1px solid rgba(255,255,255,.12); color: rgba(247,243,234,.76); padding: 8px 10px; font-size: 11px; text-transform: uppercase; letter-spacing: .12em; }
        .buttons { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .button { min-height: 48px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid rgba(197,160,89,.54); color: var(--gold-soft); font-size: 11px; letter-spacing: .16em; text-transform: uppercase; transition: background .25s, color .25s; text-align: center; padding: 12px; }
        .button.primary { background: var(--gold); color: #050505; }
        .button:hover { background: var(--gold-soft); color: #050505; }
        .footer-note { width: min(900px, 100%); margin: 30px auto 0; text-align: center; color: rgba(247,243,234,.52); font-size: 12px; line-height: 1.7; }
        @media (max-width: 880px) {
            .topbar { flex-direction: column; justify-content: center; }
            .top-actions { width: 100%; justify-content: center; flex-wrap: wrap; }
            .branches { grid-template-columns: 1fr; }
            .branch { min-height: 0; }
        }
        @media (max-width: 520px) {
            .page { padding-inline: 14px; }
            .top-actions a { padding: 10px 12px; font-size: 10px; }
            .buttons { grid-template-columns: 1fr; }
            .branch-body { padding: 22px 16px; }
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
            <a href="/" aria-label="Garden Lounge">
                <img class="logo" src="/img/logo3.png" alt="Garden Lounge">
            </a>
            <div class="top-actions">
                <a href="/admiralteyskaya/menu/">Меню</a>
                <a href="tel:+79956246808">Позвонить</a>
            </div>
        </nav>

        <section class="intro">
            <h1>Garden Lounge</h1>
            <p class="lead">Кальянные и лаунж-бары Garden Lounge в Санкт-Петербурге. Выберите филиал: камерный сад в центре у Адмиралтейской или уютная локация у метро Удельная.</p>
            <p class="seo-answer">Garden Lounge работает в двух филиалах СПб: наб. реки Мойки 67-69 и ул. Аккуратова 13. В заведениях доступны кальяны, кухня, бар, VIP-комнаты, PS5, меню и бронирование столика.</p>
        </section>

        <section class="branches" aria-label="Выбор филиала Garden Lounge">
            <article class="branch" data-slider>
                <div class="slider">
                    <div class="slide active"><img src="/admiralteyskaya/couch/uploads/image/garden-main.webp" alt="Garden Lounge Адмиралтейская интерьер"></div>
                    <div class="slide"><img src="/admiralteyskaya/couch/uploads/image/garden-2.webp" alt="Garden Lounge Адмиралтейская зал"></div>
                    <div class="slide"><img src="/admiralteyskaya/couch/uploads/image/safonovleonid_green_55.webp" alt="Garden Lounge Адмиралтейская атмосфера"></div>
                    <button class="slider-btn prev" type="button" aria-label="Предыдущее фото">‹</button>
                    <button class="slider-btn next" type="button" aria-label="Следующее фото">›</button>
                </div>
                <div class="branch-body">
                    <p class="eyebrow">Филиал в центре СПб</p>
                    <h2>Адмиралтейская</h2>
                    <p class="address">Санкт-Петербург, наб. реки Мойки 67-69. Рядом с метро Адмиралтейская, Невским проспектом и историческим центром.</p>
                    <ul class="facts"><li>Кальяны</li><li>Кухня</li><li>VIP</li><li>PS5</li></ul>
                    <div class="buttons">
                        <a class="button primary" href="/admiralteyskaya/">Открыть филиал</a>
                        <a class="button" href="/admiralteyskaya/menu/">Меню</a>
                    </div>
                </div>
            </article>

            <article class="branch" data-slider>
                <div class="slider">
                    <div class="slide active"><img src="/udelnaya/couch/uploads/image/garden-main.webp" alt="Garden Lounge Удельная интерьер"></div>
                    <div class="slide"><img src="/udelnaya/couch/uploads/image/garden-2.webp" alt="Garden Lounge Удельная зал"></div>
                    <div class="slide"><img src="/udelnaya/couch/uploads/image/safonovleonid_green_55.webp" alt="Garden Lounge Удельная атмосфера"></div>
                    <button class="slider-btn prev" type="button" aria-label="Предыдущее фото">‹</button>
                    <button class="slider-btn next" type="button" aria-label="Следующее фото">›</button>
                </div>
                <div class="branch-body">
                    <p class="eyebrow">Филиал у метро Удельная</p>
                    <h2>Удельная</h2>
                    <p class="address">Санкт-Петербург, ул. Аккуратова 13. Второй филиал Garden Lounge с тем же меню, атмосферой и возможностью бронирования.</p>
                    <ul class="facts"><li>Кальяны</li><li>Кухня</li><li>VIP</li><li>PS5</li></ul>
                    <div class="buttons">
                        <a class="button primary" href="/udelnaya/">Открыть филиал</a>
                        <a class="button" href="/udelnaya/menu/">Меню</a>
                    </div>
                </div>
            </article>
        </section>

        <p class="footer-note">Для поисковых систем и AI-поиска главная страница закрепляет бренд Garden Lounge за двумя реальными филиалами в Санкт-Петербурге, а подробные локальные сигналы остаются на отдельных страницах филиалов.</p>
    </main>
    <script>
        document.querySelectorAll('[data-slider]').forEach((slider) => {
            const slides = Array.from(slider.querySelectorAll('.slide'));
            let index = 0;
            const show = (nextIndex) => {
                slides[index].classList.remove('active');
                index = (nextIndex + slides.length) % slides.length;
                slides[index].classList.add('active');
            };
            slider.querySelector('.prev').addEventListener('click', () => show(index - 1));
            slider.querySelector('.next').addEventListener('click', () => show(index + 1));
        });
    </script>
</body>
</html>
