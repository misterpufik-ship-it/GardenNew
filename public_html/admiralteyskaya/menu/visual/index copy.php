<?php require_once( '../../couch/cms.php' ); ?>
<cms:template title='Меню визуальное' name='menu_visual' executable='1' order='160'>
    <cms:editable name='visual_group_assets' label='Логотип и разделители' type='group' />
    <cms:editable name='visual_logo' label='Логотип меню' group='visual_group_assets' type='image'>https://garden-lounge.pro/img/logo3.png</cms:editable>
    <cms:editable name='visual_divider' label='Разделитель (линия)' group='visual_group_assets' type='image'>https://garden-lounge.pro/img/div.png</cms:editable>

    <cms:repeatable name='menu_items_list' label='Список блюд'>
        <cms:editable name='item_title' label='Название блюда' type='text' />
        <cms:editable name='item_price' label='Цена (цифры)' type='text' />
        <cms:editable name='item_weight' label='Вес/Объем' type='text' />
        <cms:editable name='item_img' label='Фото блюда' type='image' />
        <cms:editable name='item_desc' label='Описание' type='textarea' />
        <cms:editable name='item_cat' label='Категория' opt_values='Кальяны=shisha | Кухня=kitchen | Бар=bar | Десерты=desserts' type='dropdown' />
    </cms:repeatable>
</cms:template>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garden Lounge | Luxury Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Montserrat:wght@200;300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-color: #000000;
            --gold: #C5A059;
            --gold-light: #FFEebb;
            --gold-dark: #8e7037;
        }

        body {
            background-color: var(--bg-color);
            color: #EAEAEA;
            font-family: 'Montserrat', sans-serif;
            margin: 0; padding: 0; overflow-x: hidden;
        }

        @keyframes shineGold {
            to { background-position: 200% center; }
        }

        .subtitle-gold {
            background: linear-gradient(to right, #8e7037 0%, var(--gold) 40%, var(--gold-light) 50%, var(--gold) 60%, #8e7037 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: shineGold 5s linear infinite;
        }

        .logo-container { padding-top: 50px; margin-bottom: 30px; text-align: center; }
        .main-logo { height: 160px; width: auto; transition: transform 0.5s; }
        @media (max-width: 768px) { .main-logo { height: 110px; } }

        .nav-container { display: flex; justify-content: center; gap: 15px; margin-bottom: 35px; flex-wrap: wrap; }
        @media (min-width: 768px) { .nav-container { gap: 35px; margin-bottom: 45px; } }

        .nav-item {
            text-transform: uppercase; letter-spacing: 0.15em; font-size: 11px; color: #fff;
            opacity: 0.5; cursor: pointer; transition: 0.3s; padding-bottom: 6px; position: relative;
        }
        .nav-item.active { opacity: 1; color: var(--gold); }
        .nav-item.active::after {
            content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 1px;
            background: var(--gold);
        }

        .main-wrapper { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 15px; }

        .menu-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; width: 100%; }
        @media (min-width: 768px) { .menu-grid { grid-template-columns: repeat(4, 1fr); gap: 25px; } }

        .image-frame {
            width: 100%; aspect-ratio: 1/1; border-radius: 12px; overflow: hidden;
            border: 1px solid var(--gold-dark); margin-bottom: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5); cursor: pointer;
        }
        .image-frame img { width: 100%; height: 100%; object-fit: cover; }

        .dish-title { 
            font-size: 12px; font-weight: 500; text-transform: uppercase; 
            letter-spacing: 0.1em; margin-bottom: 4px; text-align: center;
        }
        .dish-info { display: flex; align-items: baseline; gap: 8px; margin-bottom: 8px; justify-content: center; }
        .price { font-size: 15px; font-weight: 600; color: #fff; }
        .weight { font-family: 'Montserrat', sans-serif; font-size: 11px; color: #999; font-weight: 300; font-style: italic; }

        .details-trigger { 
            font-size: 9px; text-transform: uppercase; color: #8e7037; 
            letter-spacing: 0.2em; cursor: pointer; opacity: 0.9; text-align: center; 
        }

        .details-content { 
            font-family: 'Montserrat', sans-serif; font-size: 12px; color: #D1D1D1; 
            line-height: 1.5; font-weight: 300; font-style: italic; margin-top: 10px; 
            display: none; text-align: center; padding: 0 10px;
        }
        .details-content.is-open { display: block; }

        .action-area { display: flex; flex-direction: column; align-items: center; gap: 10px; margin-top: 40px; width: 100%; }
        @media (min-width: 768px) { .action-area { flex-direction: row; justify-content: center; gap: 15px; } }

        .btn-base {
            display: flex; align-items: center; justify-content: center;
            width: 100%; max-width: 280px; height: 52px;    
            border: 1px solid rgba(197, 160, 89, 0.3);
            text-transform: uppercase; font-size: 10px; letter-spacing: 0.15em;
            text-decoration: none; text-align: center; box-sizing: border-box; transition: 0.3s; cursor: pointer;
        }
        .btn-base:hover { border-color: var(--gold); }
        .btn-gold-fill { 
            background: linear-gradient(to right, #8e7037, #C5A059, #FFEebb, #C5A059, #8e7037); 
            background-size: 200% auto; animation: shineGold 5s linear infinite; 
            color: #000 !important; font-weight: 700; border: none; 
        }

        #loyalty-modal {
            position: fixed; inset: 0; background: rgba(0,0,0,0.85); 
            backdrop-filter: blur(10px); display: none; justify-content: center; 
            align-items: center; z-index: 3000; padding: 20px;
        }
        .modal-content {
            background: #0a0a0a; border: 1px solid var(--gold);
            padding: 35px 25px; width: 100%; max-width: 400px;
            text-align: center; position: relative;
            box-shadow: 0 0 40px rgba(197, 160, 89, 0.2);
        }
        .modal-title { font-family: 'Cormorant Garamond', serif; font-style: italic; font-size: 26px; margin-bottom: 30px; color: #fff; }
        .modal-btn {
            display: flex; align-items: center; justify-content: center; gap: 15px;
            width: 100%; height: 58px; border: 1px solid rgba(197,160,89,0.3);
            margin-bottom: 15px; color: #fff; text-decoration: none;
            text-transform: uppercase; font-size: 11px; letter-spacing: 0.1em; transition: 0.3s;
        }
        .modal-btn:hover { background: rgba(197,160,89,0.1); border-color: var(--gold); }
        .modal-btn i { font-size: 20px; color: var(--gold); width: 25px; }
        .close-modal { position: absolute; top: 12px; right: 18px; font-size: 24px; color: #555; cursor: pointer; transition: 0.3s; }
        .close-modal:hover { color: #fff; }

        #lightbox { position: fixed; inset: 0; background: rgba(0,0,0,0.96); display: none; justify-content: center; align-items: center; z-index: 2000; }
        #lightbox img { max-width: 95%; max-height: 85vh; border: 1px solid var(--gold-dark); }
        .film-grain { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: url('https://grainy-gradients.vercel.app/noise.svg'); opacity: 0.04; pointer-events: none; z-index: 1; }

        /* СТИЛИ ДЛЯ БЛОКА АКЦИЙ (ИЗ ТЕКСТОВОГО МЕНЮ) */
        .font-serif-lux { font-family: 'Cormorant Garamond', serif; }
        .taplink-block-wrapper { width:100vw; position:relative; left:50%; margin-left:-50vw; background-color: #000; padding: 40px 0; overflow: hidden; margin-top: 20px;}
        .content-limiter { max-width:600px; margin:0 auto; padding: 0 20px; position: relative; z-index: 10; }
        .promo-card { border:1px solid rgba(197,160,89,0.2); background-color: rgba(20,20,20,0.4); padding:20px; text-align:center; margin-bottom: 15px; }
        .gold-line-fade { width:160px; height:1px; background: linear-gradient(90deg, transparent, var(--gold), transparent); margin: 16px auto; }
        .shimmer-gold { background: linear-gradient(to right, #8e7037 0%, #C5A059 40%, #FFEebb 50%, #C5A059 60%, #8e7037 100%); background-size:200% auto; color:transparent; -webkit-background-clip:text; background-clip:text; animation: shineGold 5s linear infinite; display:inline-block; }
    </style>
</head>
<body class="antialiased">
    <div class="film-grain"></div>

    <div id="lightbox" onclick="closeLightbox()"><img id="lightbox-img" src=""></div>

    <div class="min-h-screen flex flex-col items-center pb-12">
        <div class="logo-container">
            <a href="https://garden-lounge.pro/admiralteyskaya" target="_self">
                <img src="<cms:show visual_logo />" alt="Logo" class="main-logo">
            </a>
        </div>

        <nav class="nav-container">
            <div class="nav-item active" onclick="filterMenu('shisha', this)">Кальяны</div>
            <div class="nav-item" onclick="filterMenu('kitchen', this)">Кухня</div>
            <div class="nav-item" onclick="filterMenu('bar', this)">Бар</div>
            <div class="nav-item" onclick="filterMenu('desserts', this)">Десерты</div>
            <div class="nav-item" onclick="showPromos(this)">Акции</div>
        </nav>

        <main class="main-wrapper flex flex-col items-center">
            
            <div class="menu-grid" id="menu-items"></div>

            <cms:pages masterpage='menu/text/index.php' limit='1'> 
            <div id="promos-container" style="display: none; width: 100%;">
                <div class="taplink-block-wrapper">
                    <div class="content-limiter">
                        <header class="text-center mb-12">
                            <h1 class="font-serif-lux text-3xl text-white font-light italic m-0">
                                <cms:show promo_title />
                            </h1>
                            <div class="gold-line-fade"></div>
                            <p class="text-[12px] uppercase tracking-[0.4em] shimmer-gold font-medium m-0">
                                <cms:show promo_subtitle />
                            </p>
                        </header>

                        <div class="space-y-3">
                            <cms:show_repeatable 'list_promos_v2'>
                                <div class="promo-card">
                                    <h2 class="font-serif-lux text-2xl text-white italic mb-1">
                                        <cms:show p_title />
                                    </h2>
                                    <p class="text-[12px] text-gray-400 font-light leading-relaxed mb-3 tracking-wide">
                                        <cms:show p_desc />
                                    </p>
                                    <div class="w-6 h-px bg-[#C5A059]/30 mx-auto mb-3"></div>
                                    <p class="text-[9px] uppercase tracking-[0.2em] shimmer-gold font-medium">
                                        <cms:show p_tag />
                                    </p>
                                </div>
                            </cms:show_repeatable>
                        </div>

                        <footer class="mt-8 text-center">
                            <p class="text-[10px] uppercase tracking-[0.3em] font-medium m-0 italic shimmer-gold">
                                <cms:show promo_footer />
                            </p>
                        </footer>
                    </div>
                </div>
            </div>
            </cms:pages>

            <div class="flex justify-center mt-12 mb-6">
                <img src="<cms:show visual_divider />" class="max-w-[220px] opacity-70">
            </div>

            <div class="action-area">
                <a href="https://garden-lounge.pro/admiralteyskaya/menu" class="btn-base">
                    <span class="subtitle-gold">Вернуться Назад</span>
                </a>
                <div onclick="openLoyaltyModal()" class="btn-base btn-gold-fill">
                    Программа лояльности
                </div>
                <a href="https://garden-lounge.pro/admiralteyskaya/menu/text/" class="btn-base">
                    <span class="subtitle-gold">Полное меню</span>
                </a>
            </div>
            
            <div class="text-center mt-10 text-[9px] uppercase tracking-[0.4em] text-[#8e7037] opacity-50">испытай гастрономическое наслаждение</div>
        </main>
    </div>

    <div id="loyalty-modal" onclick="closeLoyaltyModal(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <span class="close-modal" onclick="closeLoyaltyModal()">&times;</span>
            <div class="modal-title subtitle-gold">Способ регистрации</div>
            
            <a href="https://access.clientomer.ru/feedback/676900-1/" target="_blank" class="modal-btn">
                <i class="fa-solid fa-wallet"></i> Регистрация через Wallet
            </a>
            
            <a href="https://t.me/GardenLounge_Loyalty_Bot" target="_blank" class="modal-btn">
                <i class="fa-brands fa-telegram"></i> Регистрация через Telegram
            </a>
        </div>
    </div>

    <script>
        const menuData = [
            <cms:show_repeatable 'menu_items_list'>
            { 
                cat: '<cms:show item_cat />', 
                title: '<cms:show item_title />', 
                price: '<cms:show item_price />', 
                weight: '<cms:show item_weight />', 
                img: '<cms:show item_img />', 
                desc: '<cms:show item_desc />' 
            }<cms:if k_count != k_total_records>,</cms:if>
            </cms:show_repeatable>
        ];

        function renderMenu(filter = 'shisha') {
            const container = document.getElementById('menu-items');
            container.innerHTML = '';
            const data = filter === 'all' ? menuData : menuData.filter(i => i.cat === filter);

            data.forEach(item => {
                const card = document.createElement('div');
                card.className = 'dish-card';
                card.innerHTML = `
                    <div class="image-frame" onclick="openLightbox('${item.img}')">
                        <img src="${item.img}" alt="${item.title}">
                    </div>
                    <h3 class="dish-title subtitle-gold">${item.title}</h3>
                    <div class="dish-info">
                        <span class="price">${item.price}₽</span>
                        <span class="weight">${item.weight}</span>
                    </div>
                    <div class="details-trigger" onclick="toggleDetails(this)">Подробнее</div>
                    <div class="details-content">${item.desc}</div>
                `;
                container.appendChild(card);
            });
        }

        function filterMenu(cat, el) {
            document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
            el.classList.add('active');
            
            // Показываем сетку меню, скрываем акции
            document.getElementById('menu-items').style.display = 'grid';
            document.getElementById('promos-container').style.display = 'none';
            
            renderMenu(cat);
        }

        function showPromos(el) {
            document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
            el.classList.add('active');
            
            // Скрываем сетку меню, показываем акции
            document.getElementById('menu-items').style.display = 'none';
            document.getElementById('promos-container').style.display = 'block';
        }

        function openLightbox(src) {
            document.getElementById('lightbox-img').src = src;
            document.getElementById('lightbox').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() { 
            document.getElementById('lightbox').style.display = 'none'; 
            document.body.style.overflow = 'auto';
        }

        function toggleDetails(btn) {
            const content = btn.nextElementSibling;
            const isOpen = content.classList.contains('is-open');
            content.classList.toggle('is-open');
            btn.innerText = isOpen ? 'Подробнее' : 'Свернуть';
            btn.style.color = isOpen ? '#8e7037' : '#FFEebb';
        }

        function openLoyaltyModal() { 
            document.getElementById('loyalty-modal').style.display = 'flex'; 
            document.body.style.overflow = 'hidden'; 
        }
        function closeLoyaltyModal() { 
            document.getElementById('loyalty-modal').style.display = 'none'; 
            document.body.style.overflow = 'auto'; 
        }

        // Запускаем Кальяны по умолчанию при загрузке
        window.onload = () => renderMenu('shisha');
    </script>
</body>
</html>
<?php COUCH::invoke(); ?>
