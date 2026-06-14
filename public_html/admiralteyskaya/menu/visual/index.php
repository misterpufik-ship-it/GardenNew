<?php require_once( '../../couch/cms.php' ); ?>
<cms:template title='Меню: Визуальное' name='menu_visual' executable='1'>

    <cms:editable name='group_main_settings' label='Настройки логотипа' type='group' collapsed='1' order='1' />
    <cms:editable name='visual_logo' label='Логотип меню' group='group_main_settings' type='image'>https://garden-lounge.pro/img/logo3.png</cms:editable>
    <cms:editable name='visual_divider' label='Разделитель (линия)' group='group_main_settings' type='image'>https://garden-lounge.pro/img/div.png</cms:editable>

    <cms:set tag_options = 'Нет=- | New | Hit | Special | Chef’s Choice | 🌶️ | 🌶️🌶️ | 🌶️🌶️🌶️ | New + 🌶️ | Hit + 🌶️' />

    <cms:editable name='group_shisha' label='Кальяны' type='group' collapsed='1' order='2' />
    <cms:repeatable name='menu_shisha' label='Список блюд: Кальяны' group='group_shisha'>
        <cms:editable name='item_title' label='Название' type='text' />
        <cms:editable name='item_tag' label='Тег' type='dropdown' opt_values="<cms:show tag_options />" />
        <cms:editable name='item_price' label='Цена' type='text' />
        <cms:editable name='item_weight' label='Вес/Объем' type='text' />
        <cms:editable name='item_img' label='Фото' type='image' input_width='160' />
        <cms:editable name='item_desc' label='Описание' type='textarea' />
    </cms:repeatable>

    <cms:editable name='group_kitchen' label='Кухня' type='group' collapsed='1' order='3' />
    <cms:repeatable name='menu_kitchen' label='Список блюд: Кухня' group='group_kitchen'>
        <cms:editable name='item_title' label='Название' type='text' />
        <cms:editable name='item_tag' label='Тег' type='dropdown' opt_values="<cms:show tag_options />" />
        <cms:editable name='item_price' label='Цена' type='text' />
        <cms:editable name='item_weight' label='Вес/Объем' type='text' />
        <cms:editable name='item_img' label='Фото' type='image' input_width='160' />
        <cms:editable name='item_desc' label='Описание' type='textarea' />
    </cms:repeatable>

    <cms:editable name='group_bar' label='Бар' type='group' collapsed='1' order='4' />
    <cms:repeatable name='menu_bar' label='Список блюд: Бар' group='group_bar'>
        <cms:editable name='item_title' label='Название' type='text' />
        <cms:editable name='item_tag' label='Тег' type='dropdown' opt_values="<cms:show tag_options />" />
        <cms:editable name='item_price' label='Цена' type='text' />
        <cms:editable name='item_weight' label='Вес/Объем' type='text' />
        <cms:editable name='item_img' label='Фото' type='image' input_width='160' />
        <cms:editable name='item_desc' label='Описание' type='textarea' />
    </cms:repeatable>

    <cms:editable name='group_desserts' label='Десерты' type='group' collapsed='1' order='5' />
    <cms:repeatable name='menu_desserts' label='Список блюд: Десерты' group='group_desserts'>
        <cms:editable name='item_title' label='Название' type='text' />
        <cms:editable name='item_tag' label='Тег' type='dropdown' opt_values="<cms:show tag_options />" />
        <cms:editable name='item_price' label='Цена' type='text' />
        <cms:editable name='item_weight' label='Вес/Объем' type='text' />
        <cms:editable name='item_img' label='Фото' type='image' input_width='160' />
        <cms:editable name='item_desc' label='Описание' type='textarea' />
    </cms:repeatable>

</cms:template>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Визуальное меню Garden Lounge — кальяны, кухня и бар в СПб</title>
    <meta name="description" content="Визуальное меню Garden Lounge на Адмиралтейской: кальяны, блюда кухни, бар, десерты и акции лаунж-бара в центре Санкт-Петербурга.">
    <link rel="canonical" href="https://garden-lounge.pro/admiralteyskaya/menu/visual/">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://garden-lounge.pro/admiralteyskaya/menu/visual/">
    <meta property="og:title" content="Визуальное меню Garden Lounge — кальяны, кухня и бар в СПб">
    <meta property="og:description" content="Визуальное меню Garden Lounge на Адмиралтейской: кальяны, кухня, бар, десерты и акции.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=Montserrat:wght@200;300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root { --bg-color: #000; --gold: #C5A059; --gold-light: #FFEebb; --gold-dark: #8e7037; }
        body { background-color: var(--bg-color); color: #EAEAEA; font-family: 'Montserrat', sans-serif; margin: 0; padding: 0; overflow-x: hidden; }
        .font-serif-lux { font-family: 'Cormorant Garamond', serif; }

        @keyframes shineGold { to { background-position: 200% center; } }
        .subtitle-gold {
            background: linear-gradient(to right, #8e7037 0%, var(--gold) 40%, var(--gold-light) 50%, var(--gold) 60%, #8e7037 100%);
            background-size: 200% auto; -webkit-background-clip: text; background-clip: text; color: transparent; animation: shineGold 5s linear infinite;
        }

        .logo-container { padding: 2rem 0; text-align: center; background: #000; }
        .main-logo { height: 7rem; width: auto; margin: 0 auto; }
        .nav-sticky { position: sticky; top:0; z-index:50; background: rgba(0,0,0,0.95); backdrop-filter: blur(10px); border-bottom: 1px solid #1a1a1a; margin-bottom: 30px; }
        .nav-container { display: flex; justify-content: center; gap: 18px; padding: 14px 10px 8px; flex-wrap: wrap; }
        .nav-item { text-transform: uppercase; letter-spacing: 0.1em; font-size: 12px; font-weight: 700; color: #888; cursor: pointer; transition: 0.3s; padding-bottom: 6px; position: relative; }
        .nav-item.active { color: var(--gold); }
        .nav-item.active::after { content: ''; position: absolute; bottom: -4px; left: 0; width: 100%; height: 2px; background: var(--gold); }
        .gold-divider-nav { width:100%; height:1px; background: linear-gradient(90deg, transparent 0%, var(--gold) 50%, transparent 100%); opacity:0.8; margin-top: 6px; }
        
        .main-wrapper { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 15px; }
        .menu-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 25px 15px; width: 100%; align-items: start; }
        @media (min-width: 768px) { .menu-grid { grid-template-columns: repeat(4, 1fr); gap: 40px 30px; } }
        
        .dish-card { display: flex; flex-direction: column; height: 100%; position: relative; }

        .image-frame { position: relative; width: 100%; aspect-ratio: 1/1; border-radius: 8px; overflow: hidden; border: 1px solid rgba(197, 160, 89, 0.2); margin-bottom: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.5); cursor: pointer; }
        .image-frame img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
        .image-frame:hover img { transform: scale(1.05); }

        .badge-container { 
            position: absolute; top: 10px; right: 10px; z-index: 10;
            display: flex; flex-direction: column; align-items: flex-end; gap: 4px; 
            pointer-events: none;
        }
        .badge-item {
            height: 18px; font-size: 10px; font-weight: 800; text-transform: uppercase;
            padding: 0 6px; border-radius: 2px; white-space: nowrap; line-height: 18px;
            display: inline-flex; align-items: center; justify-content: center;
            border: 1px solid rgba(197, 160, 89, 0.8); color: var(--gold); background: rgba(0, 0, 0, 0.7);
            box-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }
        .badge-chef { background: var(--gold); color: #000; border: 1px solid var(--gold); }
        .badge-spicy { color: #ff4d4d; font-size: 16px; text-shadow: 0 2px 4px rgba(0,0,0,0.8); line-height: 1; }

        .dish-title { font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; text-align: center; line-height: 1.3; min-height: 34px; display: flex; align-items: center; justify-content: center; }
        .dish-bottom { margin-top: auto; display: flex; flex-direction: column; align-items: center; }
        .dish-info { display: flex; align-items: baseline; gap: 8px; margin-bottom: 8px; justify-content: center; }
        .price { font-size: 1.1rem; font-weight: 600; color: #fff; }
        .weight { font-size: 11px; color: #777; font-weight: 400; font-style: italic; }
        .details-trigger { font-size: 10px; text-transform: uppercase; color: #8e7037; letter-spacing: 0.15em; cursor: pointer; opacity: 0.8; text-align: center; padding: 5px 0; }
        .details-content { font-size: 12px; color: #A0A0A0; line-height: 1.4; font-weight: 300; margin-top: 10px; display: none; text-align: center; padding: 5px; background: rgba(255,255,255,0.02); border-radius: 4px; }
        .details-content.is-open { display: block; animation: fadeIn 0.3s ease; }
        
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* СТИЛИ АКЦИЙ (ИЗ ТЕКСТОВОГО МЕНЮ) */
        .promo-card { border:1px solid rgba(197,160,89,0.2); background-color: rgba(20,20,20,0.4); padding:20px; text-align:center; margin-bottom: 15px; }
        .gold-line-fade { width:160px; height:1px; background: linear-gradient(90deg, transparent, var(--gold), transparent); margin: 16px auto; }
        .film-grain { position:absolute; top:0; left:0; width:100%; height:100%; background:url('https://grainy-gradients.vercel.app/noise.svg'); opacity:.04; pointer-events:none; z-index:1; }

        .action-area { display: flex; flex-direction: column; align-items: center; gap: 12px; margin-top: 50px; width: 100%; }
        @media (min-width: 768px) { .action-area { flex-direction: row; justify-content: center; gap: 15px; } }

        .btn-base {
            display: flex; align-items: center; justify-content: center;
            width: 100%; max-width: 280px; height: 52px;    
            border: 1px solid rgba(197, 160, 89, 0.3);
            text-transform: uppercase; font-size: 10px; letter-spacing: 0.15em;
            text-decoration: none; text-align: center; transition: 0.3s; cursor: pointer; color: #fff;
        }
        .btn-base:hover { border-color: var(--gold); background: rgba(197,160,89,0.05); }
        .btn-gold-fill { background: var(--gold); color: #000 !important; font-weight: 700; border: none; }

        #loyalty-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(10px); display: none; justify-content: center; align-items: center; z-index: 3000; padding: 20px; }
        .modal-content { background: #0a0a0a; border: 1px solid var(--gold); padding: 40px 25px; width: 100%; max-width: 400px; text-align: center; position: relative; }
        .modal-title { font-family: 'Cormorant Garamond', serif; font-style: italic; font-size: 24px; margin-bottom: 25px; color: #fff; }
        .modal-btn { display: flex; align-items: center; justify-content: center; gap: 12px; width: 100%; height: 54px; border: 1px solid rgba(197, 160, 89, 0.3); margin-bottom: 12px; color: #fff; text-decoration: none; text-transform: uppercase; font-size: 11px; letter-spacing: 0.1em; transition: 0.3s; }
        .close-modal { position: absolute; top: 10px; right: 15px; font-size: 28px; color: #555; cursor: pointer; }

        #lightbox { position: fixed; inset: 0; background: rgba(0,0,0,0.96); display: none; justify-content: center; align-items: center; z-index: 2000; }
        #lightbox img { max-width: 95%; max-height: 85vh; border: 1px solid var(--gold-dark); }
    </style>
</head>
<body class="antialiased">

    <div id="lightbox" onclick="closeLightbox()"><img id="lightbox-img" src=""></div>

    <div class="logo-container">
        <a href="https://garden-lounge.pro/admiralteyskaya">
            <img src="<cms:show visual_logo />" alt="Logo" class="main-logo">
        </a>
    </div>

    <div class="nav-sticky">
        <nav class="nav-container">
            <div class="nav-item active" onclick="filterMenu('shisha', this)">Кальяны</div>
            <div class="nav-item" onclick="filterMenu('kitchen', this)">Кухня</div>
            <div class="nav-item" onclick="filterMenu('bar', this)">Бар</div>
            <div class="nav-item" onclick="filterMenu('desserts', this)">Десерты</div>
            <div class="nav-item" onclick="showPromos(this)">Акции</div>
        </nav>
        <div class="gold-divider-nav"></div>
    </div>

    <main class="main-wrapper flex flex-col items-center pb-20">
        <div class="menu-grid" id="menu-items"></div>

        <cms:pages masterpage='menu/text/index.php' limit='1'> 
        <div id="promos-container" style="display: none; width: 100%; position: relative;">
            <div class="film-grain"></div>
            <div class="max-w-[600px] mx-auto px-5 relative z-10">
                <header class="text-center mb-10">
                    <h1 class="font-serif-lux text-3xl text-white font-light italic m-0"><cms:show promo_title /></h1>
                    <div class="gold-line-fade"></div>
                    <p class="text-[12px] uppercase tracking-[0.4em] subtitle-gold font-medium m-0"><cms:show promo_subtitle /></p>
                </header>

                <div class="space-y-4">
                    <cms:show_repeatable 'list_promos_v2'>
                        <div class="promo-card">
                            <h2 class="font-serif-lux text-2xl text-white italic mb-1"><cms:show p_title /></h2>
                            <p class="text-[12px] text-gray-400 font-light leading-relaxed mb-3"><cms:show p_desc /></p>
                            <p class="text-[9px] uppercase tracking-[0.2em] subtitle-gold font-medium"><cms:show p_tag /></p>
                        </div>
                    </cms:show_repeatable>
                </div>
            </div>
        </div>
        </cms:pages>

        <div class="flex justify-center mt-12 mb-6">
            <img src="<cms:show visual_divider />" class="max-w-[200px] opacity-50">
        </div>

        <div class="action-area">
            <a href="https://garden-lounge.pro/admiralteyskaya/menu" class="btn-base">
                <span class="subtitle-gold">Вернуться Назад</span>
            </a>
            <div onclick="openLoyaltyModal()" class="btn-base btn-gold-fill">
                Программа лояльности
            </div>
            <a href="https://garden-lounge.pro/admiralteyskaya/menu/text/" class="btn-base">
                <span class="subtitle-gold">Текстовое меню</span>
            </a>
        </div>
        
        <div class="text-center mt-10 text-[9px] uppercase tracking-[0.4em] text-[#8e7037] opacity-40">Garden Lounge Luxury Experience</div>
    </main>

    <div id="loyalty-modal" onclick="closeLoyaltyModal()">
        <div class="modal-content" onclick="event.stopPropagation()">
            <span class="close-modal" onclick="closeLoyaltyModal()">&times;</span>
            <div class="modal-title subtitle-gold">Способ регистрации</div>
            <a href="https://access.clientomer.ru/feedback/676900-1/" target="_blank" class="modal-btn">
                <i class="fa-solid fa-wallet"></i> Wallet
            </a>
            <a href="https://t.me/GardenLounge_Loyalty_Bot" target="_blank" class="modal-btn">
                <i class="fa-brands fa-telegram"></i> Telegram
            </a>
        </div>
    </div>

    <script>
        const menuData = [
            <cms:show_repeatable 'menu_shisha'>
            { cat: 'shisha', title: '<cms:show item_title />', price: '<cms:show item_price />', weight: '<cms:show item_weight />', img: '<cms:show item_img />', desc: '<cms:show item_desc />', tag: '<cms:show item_tag />' },
            </cms:show_repeatable>
            <cms:show_repeatable 'menu_kitchen'>
            { cat: 'kitchen', title: '<cms:show item_title />', price: '<cms:show item_price />', weight: '<cms:show item_weight />', img: '<cms:show item_img />', desc: '<cms:show item_desc />', tag: '<cms:show item_tag />' },
            </cms:show_repeatable>
            <cms:show_repeatable 'menu_bar'>
            { cat: 'bar', title: '<cms:show item_title />', price: '<cms:show item_price />', weight: '<cms:show item_weight />', img: '<cms:show item_img />', desc: '<cms:show item_desc />', tag: '<cms:show item_tag />' },
            </cms:show_repeatable>
            <cms:show_repeatable 'menu_desserts'>
            { cat: 'desserts', title: '<cms:show item_title />', price: '<cms:show item_price />', weight: '<cms:show item_weight />', img: '<cms:show item_img />', desc: '<cms:show item_desc />', tag: '<cms:show item_tag />' },
            </cms:show_repeatable>
        ];

        function getTagHtml(tag) {
            if (!tag || tag === '-') return '';
            let html = '<div class="badge-container">';
            if (tag.includes('New')) html += '<span class="badge-item">New</span>';
            if (tag.includes('Hit')) html += '<span class="badge-item">Hit</span>';
            if (tag.includes('Special')) html += '<span class="badge-item">Special</span>';
            if (tag.includes('Chef’s Choice')) html += '<span class="badge-item badge-chef">Chef</span>';
            if (tag.includes('🌶️🌶️🌶️')) html += '<span class="badge-spicy">🌶️🌶️🌶️</span>';
            else if (tag.includes('🌶️🌶️')) html += '<span class="badge-spicy">🌶️🌶️</span>';
            else if (tag.includes('🌶️')) html += '<span class="badge-spicy">🌶️</span>';
            html += '</div>';
            return html;
        }

        function renderMenu(filter = 'shisha') {
            const container = document.getElementById('menu-items');
            container.innerHTML = '';
            const data = menuData.filter(i => i.cat === filter);
            data.forEach(item => {
                const card = document.createElement('div');
                card.className = 'dish-card';
                card.innerHTML = `
                    <div class="image-frame" onclick="openLightbox('${item.img}')">
                        ${getTagHtml(item.tag)}
                        <img src="${item.img}" alt="${item.title}" loading="lazy">
                    </div>
                    <h3 class="dish-title subtitle-gold">${item.title}</h3>
                    <div class="dish-bottom">
                        <div class="dish-info">
                            <span class="price">${item.price} ₽</span>
                            <span class="weight">${item.weight}</span>
                        </div>
                        <div class="details-trigger" onclick="toggleDetails(this)">Подробнее</div>
                        <div class="details-content">${item.desc}</div>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        function filterMenu(cat, el) {
            document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
            el.classList.add('active');
            document.getElementById('menu-items').style.display = 'grid';
            document.getElementById('promos-container').style.display = 'none';
            renderMenu(cat);
        }

        function showPromos(el) {
            document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
            el.classList.add('active');
            document.getElementById('menu-items').style.display = 'none';
            document.getElementById('promos-container').style.display = 'block';
        }

        function openLightbox(src) {
            if(!src) return;
            document.getElementById('lightbox-img').src = src;
            document.getElementById('lightbox').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() { document.getElementById('lightbox').style.display = 'none'; document.body.style.overflow = 'auto'; }
        
        function toggleDetails(btn) {
            const content = btn.nextElementSibling;
            content.classList.toggle('is-open');
            btn.innerText = content.classList.contains('is-open') ? 'Свернуть' : 'Подробнее';
        }

        function openLoyaltyModal() { document.getElementById('loyalty-modal').style.display = 'flex'; document.body.style.overflow = 'hidden'; }
        function closeLoyaltyModal() { document.getElementById('loyalty-modal').style.display = 'none'; document.body.style.overflow = 'auto'; }

        window.onload = () => renderMenu('shisha');
    </script>
</body>
</html>
<?php COUCH::invoke(); ?>