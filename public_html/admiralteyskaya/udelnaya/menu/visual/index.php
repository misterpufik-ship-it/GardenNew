<?php
if (!defined('K_TEMPLATE_NAME')) {
    define('K_TEMPLATE_NAME', 'udelnaya/menu/visual/index.php');
}
require_once dirname(__DIR__, 3) . '/couch/cms.php';
?>
<cms:template title='Уделка Меню Визуальное' name='menu_visual_udel' executable='1' order='160'>

    <cms:editable name='visual_logo' label='Логотип меню' type='image' hidden='1' order='1'>couch/uploads/image/logo3.webp</cms:editable>
    <cms:editable name='visual_divider' label='Разделитель (линия)' type='image' hidden='1' order='2'>couch/uploads/image/div.webp</cms:editable>

    <cms:set tag_options = 'Нет=- | New | Hit | Special | Chef’s Choice | 🌶️ | 🌶️🌶️ | 🌶️🌶️🌶️ | New + 🌶️ | Hit + 🌶️' />

    <cms:editable name='group_shisha' label='Кальяны' type='group' collapsed='1' order='10' />
    <cms:repeatable name='menu_shisha' label='Блюда' group='group_shisha'>
        <cms:editable name='item_title' label='Название' type='text' />
        <cms:editable name='item_tag' label='Тег' type='dropdown' opt_values="<cms:show tag_options />" />
        <cms:editable name='item_price' label='Цена' type='text' />
        <cms:editable name='item_weight' label='Вес/Объем' type='text' />
        <cms:editable name='item_img' label='Фото' type='image' input_width='160' />
        <cms:editable name='item_desc' label='Описание' type='textarea' />
    </cms:repeatable>

    <cms:editable name='group_kitchen' label='Кухня' type='group' collapsed='1' order='20' />
    <cms:repeatable name='menu_kitchen' label='Блюда' group='group_kitchen'>
        <cms:editable name='item_title' label='Название' type='text' />
        <cms:editable name='item_tag' label='Тег' type='dropdown' opt_values="<cms:show tag_options />" />
        <cms:editable name='item_price' label='Цена' type='text' />
        <cms:editable name='item_weight' label='Вес/Объем' type='text' />
        <cms:editable name='item_img' label='Фото' type='image' input_width='160' />
        <cms:editable name='item_desc' label='Описание' type='textarea' />
    </cms:repeatable>

    <cms:editable name='group_bar' label='Бар' type='group' collapsed='1' order='30' />
    <cms:repeatable name='menu_bar' label='Блюда' group='group_bar'>
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
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/age-gate/assets.php'; gl_render_head_assets(); ?>
    <title>Визуальное меню Garden Lounge на Удельной — кальяны, кухня и бар</title>
    <meta name="description" content="Визуальное меню Garden Lounge на Удельной: кальяны, блюда кухни, бар, десерты и акции лаунж-бара у метро Удельная, ул. Аккуратова 13.">
    <link rel="canonical" href="https://garden-lounge.pro/udelnaya/menu/visual">
    <?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/age-gate/menu-schema.php';
    gl_menu_og_render(array(
        'branch' => 'udelnaya',
        'url' => 'https://garden-lounge.pro/udelnaya/menu/visual',
        'title' => 'Визуальное меню Garden Lounge на Удельной — кальяны, кухня и бар',
        'description' => 'Визуальное меню Garden Lounge на Удельной: кальяны, кухня, бар, десерты и акции.',
    ));
    gl_menu_seo_schema_render(array(
        'branch' => 'udelnaya',
        'page' => 'visual',
        'url' => 'https://garden-lounge.pro/udelnaya/menu/visual',
        'name' => 'Визуальное меню Garden Lounge на Удельной — кальяны, кухня и бар',
        'description' => 'Визуальное меню Garden Lounge на Удельной: кальяны, кухня, бар, десерты и акции.',
    ));
    ?>
    <?php gl_menu_page_head_assets(); ?>
    <style>
        :root { --bg-color: #000; --gold: #C5A059; --gold-light: #FFEebb; --gold-dark: #8e7037; }
        body { background-color: var(--bg-color); color: #EAEAEA; font-family: 'Montserrat', sans-serif; margin: 0; padding: 0; overflow-x: hidden; }
        .font-serif-lux { font-family: 'Cormorant Garamond', serif; }

        .nav-sticky { position: sticky; top:0; z-index:50; background-color: rgba(0,0,0,0.95); backdrop-filter: blur(10px); border-bottom: 1px solid #1a1a1a; margin-bottom: 30px; }
        .tabs-wrap { display:flex; flex-wrap:wrap; justify-content:center; gap:14px 18px; padding: 14px 10px 8px; }
        .tab-btn { position: relative; transition: all .3s ease; color:#888; background:none; border:none; cursor:pointer; padding: 0 0 6px; }
        .tab-btn.active { color: var(--gold); }
        .tab-btn.active::after { content:''; position:absolute; bottom:-4px; left:0; width:100%; height:2px; background: var(--gold); }
        @media (min-width: 768px) {
            .tabs-wrap .tab-btn { font-size: 11px; letter-spacing: 0.16em; padding-bottom: 7px; }
        }
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
        .details-trigger:hover { opacity: 1; color: var(--gold); }
        
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* СТИЛИ АКЦИЙ (ИЗ ТЕКСТОВОГО МЕНЮ) */
        .promo-card { border:1px solid rgba(197,160,89,0.2); background-color: rgba(20,20,20,0.4); padding:20px; text-align:center; margin-bottom: 15px; }
        .promo-card .promo-offer { font-size: 10px; letter-spacing: 0.4em; text-transform: uppercase; font-weight: 500; margin: 0; }
        .gold-line-fade { width:160px; height:1px; background: linear-gradient(90deg, transparent, var(--gold), transparent); margin: 16px auto; }
        .film-grain { position:absolute; top:0; left:0; width:100%; height:100%; background:url('/img/noise.svg'); opacity:.04; pointer-events:none; z-index:1; }

        .action-area { display: flex; flex-direction: column; align-items: center; gap: 12px; margin-top: 50px; width: 100%; }
        @media (min-width: 768px) { .action-area { flex-direction: row; justify-content: center; gap: 15px; } }

        .btn-base {
            display: flex; align-items: center; justify-content: center;
            width: 100%; max-width: 280px; height: 52px;    
            border: 1px solid rgba(197, 160, 89, 0.3);
            text-transform: uppercase; font-size: 10px; letter-spacing: 0.15em;
            text-decoration: none; text-align: center; transition: 0.3s; cursor: pointer; color: #fff;
        }
        .btn-base:hover:not(.btn-gold-fill) { border-color: var(--gold); background: rgba(197,160,89,0.05); }
        .btn-gold-fill { background: var(--gold); color: #000 !important; font-weight: 700; border: none; }
        .btn-gold-fill:hover { background: var(--gold); color: #000 !important; border: none; }

        #loyalty-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(10px); display: none; justify-content: center; align-items: center; z-index: 3000; padding: 20px; }
        .modal-content { background: #0a0a0a; border: 1px solid var(--gold); padding: 40px 25px; width: 100%; max-width: 400px; text-align: center; position: relative; }
        .modal-title { font-family: 'Cormorant Garamond', serif; font-style: italic; font-size: 24px; margin-bottom: 25px; color: #fff; }
        .modal-btn { display: flex; align-items: center; justify-content: center; gap: 12px; width: 100%; height: 54px; border: 1px solid rgba(197, 160, 89, 0.3); margin-bottom: 12px; color: #fff; text-decoration: none; text-transform: uppercase; font-size: 11px; letter-spacing: 0.1em; transition: 0.3s; }
        .close-modal { position: absolute; top: 10px; right: 15px; font-size: 28px; color: #555; cursor: pointer; }

        #lightbox { position: fixed; inset: 0; background: rgba(0,0,0,0.96); display: none; justify-content: center; align-items: center; z-index: 2000; }
        #lightbox img { max-width: 95%; max-height: 85vh; border: 1px solid var(--gold-dark); }
        .lightbox-close { position: fixed; top: 12px; right: 16px; z-index: 2001; width: 44px; height: 44px; border: none; background: transparent; color: #fff; font-size: 36px; line-height: 1; cursor: pointer; transition: color 0.2s; }
        .lightbox-close:hover { color: var(--gold); }
    </style>
</head>
<body class="antialiased">

    <div id="lightbox" onclick="closeLightbox()">
        <button type="button" class="lightbox-close" onclick="event.stopPropagation(); closeLightbox();" aria-label="Закрыть">&times;</button>
        <img id="lightbox-img" src="" alt="">
    </div>

    <header class="py-8 text-center bg-black">
        <a href="https://garden-lounge.pro/udelnaya/menu">
            <img src="<cms:set asset_field='visual_logo' /><cms:set asset_default='/admiralteyskaya/couch/uploads/image/logo3.webp' /><cms:embed 'gl-resolve-asset-src.html' />" alt="Logo" class="h-28 mx-auto">
        </a>
    </header>

    <div class="nav-sticky">
        <nav class="tabs-wrap">
            <button type="button" class="tab-btn active uppercase font-bold tracking-widest text-xs" onclick="filterMenu('shisha', this)">&#1050;&#1072;&#1083;&#1100;&#1103;&#1085;&#1099;</button>
            <button type="button" class="tab-btn uppercase font-bold tracking-widest text-xs" onclick="filterMenu('kitchen', this)">&#1050;&#1091;&#1093;&#1085;&#1103;</button>
            <button type="button" class="tab-btn uppercase font-bold tracking-widest text-xs" onclick="filterMenu('bar', this)">&#1041;&#1072;&#1088;</button>
            <button type="button" class="tab-btn uppercase font-bold tracking-widest text-xs" onclick="showPromos(this)">&#1040;&#1082;&#1094;&#1080;&#1080;</button>
        </nav>
        <div class="gold-divider-nav"></div>
    </div>

    <main class="main-wrapper flex flex-col items-center pb-20">
        <div class="menu-grid" id="menu-items"></div>

        <cms:pages masterpage='udelnaya/akzii.php' limit='1'>
        <div id="promos-container" style="display: none; width: 100%; position: relative;">
            <div class="film-grain"></div>
            <div class="max-w-[600px] mx-auto px-5 relative z-10">
                <cms:set promos_variant='visual' />
                <cms:embed 'promos-menu-block.html' />
            </div>
        </div>
        </cms:pages>

        <div class="flex justify-center mt-12 mb-6">
            <img src="<cms:set asset_field='visual_divider' /><cms:set asset_default='/admiralteyskaya/couch/uploads/image/div.webp' /><cms:embed 'gl-resolve-asset-src.html' />" class="max-w-[200px] opacity-50" alt="">
        </div>

        <div class="action-area">
            <a href="https://garden-lounge.pro/udelnaya/menu" class="btn-base">
                <span class="subtitle-gold">Вернуться Назад</span>
            </a>
            <div onclick="openLoyaltyModal()" class="btn-base btn-gold-fill">
                Программа лояльности
            </div>
            <a href="https://garden-lounge.pro/udelnaya/menu/text" class="btn-base">
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

    <cms:embed 'visual-menu-script.html' />
</body>
</html>
<?php COUCH::invoke(); ?>

