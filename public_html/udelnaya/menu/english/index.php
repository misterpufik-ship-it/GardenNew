<?php
define('K_TEMPLATE_NAME', 'udelnaya/menu/english/index.php');
$garden_cms = null;
foreach ([
    __DIR__ . '/admiralteyskaya/couch/cms.php',
    __DIR__ . '/../admiralteyskaya/couch/cms.php',
    __DIR__ . '/../../admiralteyskaya/couch/cms.php',
    __DIR__ . '/../../../admiralteyskaya/couch/cms.php',
    __DIR__ . '/../../../../admiralteyskaya/couch/cms.php',
    __DIR__ . '/../couch/cms.php',
    __DIR__ . '/../../couch/cms.php',
    __DIR__ . '/../../../couch/cms.php',
    __DIR__ . '/../../../../couch/cms.php',
] as $candidate) {
    if (file_exists($candidate)) {
        $garden_cms = $candidate;
        break;
    }
}
if (!$garden_cms) {
    die('Garden Lounge CMS bootstrap not found');
}
require_once $garden_cms;
?>
<cms:template title='Уделка Меню En' icon='globe' />

<cms:pages masterpage='udelnaya/menu/text/index.php' limit='1'>
    <cms:set my_lang='en' 'global' />

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/age-gate/assets.php'; gl_render_head_assets(); ?>
        <title>Menu Garden Lounge Udelnaya — hookahs, kitchen, bar</title>
        <meta name="description" content="<cms:if meta_desc_en><cms:show meta_desc_en /><cms:else />English menu of Garden Lounge near Udelnaya: hookahs, kitchen, bar, drinks and special offers in northern Saint Petersburg.</cms:if>">
        <link rel="canonical" href="https://garden-lounge.pro/udelnaya/menu/english/">
        <cms:php>
        global $CTX;
        require_once $_SERVER['DOCUMENT_ROOT'] . '/age-gate/menu-schema.php';
        $desc = trim((string) $CTX->get('meta_desc_en'));
        if ($desc === '') {
            $desc = 'English menu of Garden Lounge near Udelnaya: hookahs, kitchen, bar and drinks.';
        }
        gl_menu_og_render(array(
            'branch' => 'udelnaya',
            'url' => 'https://garden-lounge.pro/udelnaya/menu/english/',
            'title' => 'Menu Garden Lounge Udelnaya — hookahs, kitchen, bar',
            'description' => $desc,
        ));
        </cms:php>
        <?php
        require_once $_SERVER['DOCUMENT_ROOT'] . '/age-gate/menu-schema.php';
        gl_menu_seo_schema_render(array(
            'branch' => 'udelnaya',
            'page' => 'english',
            'url' => 'https://garden-lounge.pro/udelnaya/menu/english/',
            'name' => 'Menu Garden Lounge Udelnaya — hookahs, kitchen, bar',
            'description' => 'English menu of Garden Lounge near Udelnaya: hookahs, kitchen, bar and drinks.',
            'lang' => 'en-US',
        ));
        ?>

        <?php gl_menu_page_head_assets(); ?>

        <style>
        body { background-color: #000; color: #fff; font-family: 'Montserrat', sans-serif; margin: 0; overflow-x: hidden; }
        .font-serif-lux { font-family: 'Cormorant Garamond', serif; }
        :root { --gold:#C5A059; --gold-dark:#8e7037; }

        @keyframes shineGold { to { background-position: 200% center; } }
        .gold-shimmer {
            background: linear-gradient(to right, var(--gold-dark) 0%, var(--gold) 40%, #FFEebb 50%, var(--gold) 60%, var(--gold-dark) 100%);
            background-size: 200% auto; -webkit-background-clip:text; background-clip:text;
            color: transparent; animation: shineGold 5s linear infinite;
        }

        .nav-sticky { position: sticky; top:0; z-index:50; background-color: rgba(0,0,0,0.95); backdrop-filter: blur(10px); border-bottom: 1px solid #1a1a1a; }
        .gold-divider-nav { width:100%; height:1px; background: linear-gradient(90deg, transparent 0%, var(--gold) 50%, transparent 100%); opacity:0.8; margin-top: 6px; }

        .tab-btn { position: relative; transition: all .3s ease; color:#888; background:none; border:none; cursor:pointer; font-family: inherit; padding: 0; }
        .tab-btn.active { color: var(--gold); }
        .tab-btn.active::after { content:''; position:absolute; bottom:-4px; left:0; width:100%; height:2px; background: var(--gold); }
        @media (min-width: 768px) {
            .tabs-wrap .tab-btn { font-size: 11px; letter-spacing: 0.16em; padding-bottom: 7px; }
        }

        .tabs-wrap { display:flex; flex-wrap:wrap; justify-content:center; gap:14px 18px; padding: 14px 10px 8px; }
        .subtabs-wrap { display:none; flex-wrap:wrap; justify-content:center; gap:10px 12px; padding: 10px 10px 15px; }
        .subtab-btn { border: 1px solid rgba(197,160,89,0.35); border-radius: 999px; padding: 6px 12px; font-size: 11px; text-transform: uppercase; color: #d0d0d0; cursor:pointer; background: transparent; font-family: inherit; line-height: 1.2; }
        .subtab-btn.active { color: #000; background: var(--gold); }
        @media (max-width: 767px) {
            .subtabs-wrap { gap: 6px 8px; padding: 8px 10px 12px; }
            .subtab-btn { padding: 5px 10px; font-size: 10px; letter-spacing: 0.04em; line-height: 1.2; white-space: nowrap; }
        }

        .tab-content { display:none; }
        .tab-content.active { display:block; animation: fadeIn .35s ease-out; }
        @keyframes fadeIn { from{ opacity:0; transform: translateY(8px);} to{opacity:1; transform: translateY(0);} }

        .category-title { font-family: 'Cormorant Garamond', serif; font-style: italic; font-weight: 400; font-size: 1.875rem; margin: 2.2rem 0 1.6rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: .5rem; text-align: center; }
        .subcat-title { font-weight:700; letter-spacing: .28em; text-transform: uppercase; font-size:.75rem; text-align:center; margin: 1.7rem 0 .9rem; }
        .price-tag { font-weight:500; font-size:1.25rem; white-space:nowrap; }

        .badge-container { display: inline-flex; gap: 4px; vertical-align: middle; margin-left: 8px; position: relative; top: -1px; }
        .badge-item {
            height: 18px !important;
            font-size: 12px !important;
            font-weight: 800 !important;
            text-transform: uppercase;
            letter-spacing: 0.01em;
            padding: 0 5px !important;
            border-radius: 2px;
            white-space: nowrap;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(197, 160, 89, 0.6);
            color: var(--gold);
            background: rgba(197, 160, 89, 0.1);
        }
        .badge-chef { background: var(--gold); color: #000; border: 1px solid var(--gold); }
        .badge-spicy { height: 18px; border: 1px solid rgba(197,160,89,0.34); background: linear-gradient(180deg, rgba(197,160,89,0.14), rgba(197,160,89,0.04)); font-size: 10px; padding: 0 5px; margin-left: 2px; border-radius: 999px; gap: 3px; display: inline-flex; align-items: center; line-height: 1; box-shadow: inset 0 0 8px rgba(197,160,89,0.08); }
        .badge-spicy i { color: #C5A059; filter: drop-shadow(0 0 4px rgba(197,160,89,0.28)); font-family: "Font Awesome 6 Free" !important; font-weight: 900; font-style: normal; }
        .badge-spicy .fa-solid::before { font-family: "Font Awesome 6 Free" !important; font-weight: 900; }

        .taplink-block-wrapper { width:100vw; position:relative; left:50%; margin-left:-50vw; background-color: #000; padding: 40px 0; overflow: hidden; }
        .content-limiter { max-width:600px; margin:0 auto; padding: 0 20px; position: relative; z-index: 10; }
        .film-grain { position:absolute; top:0; left:0; width:100%; height:100%; background:url('/img/noise.svg'); opacity:.04; pointer-events:none; z-index:1; }
        .promo-card { border:1px solid rgba(197,160,89,0.2); background-color: rgba(20,20,20,0.4); padding:20px; text-align:center; margin-bottom: 15px; }
        .gold-line-fade { width:160px; height:1px; background: linear-gradient(90deg, transparent, var(--gold), transparent); margin: 16px auto; }
        .shimmer-gold { background: linear-gradient(to right, #8e7037 0%, #C5A059 40%, #FFEebb 50%, #C5A059 60%, #8e7037 100%); background-size:200% auto; color:transparent; -webkit-background-clip:text; background-clip:text; animation: shineGold 5s linear infinite; display:inline-block; }
        .promo-offer { font-size: 10px; line-height: 1.8; letter-spacing: 0.4em; text-transform: uppercase; font-weight: 500; margin: 0; }
        .akzii-footer-note { font-size: 10px; line-height: 1.8; letter-spacing: 0.3em; font-weight: 500; font-style: normal; margin: 0; }

        #loyalty-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(10px); display: none; justify-content: center; align-items: center; z-index: 3000; padding: 20px; }
        .modal-content { background: #0a0a0a; border: 1px solid var(--gold); padding: 40px 25px; width: 100%; max-width: 400px; text-align: center; position: relative; box-shadow: 0 0 30px rgba(197, 160, 89, 0.2); }
        .modal-title { font-family: 'Cormorant Garamond', serif; font-style: italic; font-size: 24px; margin-bottom: 25px; color: #fff; line-height: 1.2; }
        .modal-btn { display: flex; align-items: center; justify-content: center; gap: 12px; width: 100%; height: 54px; border: 1px solid rgba(197, 160, 89, 0.3); margin-bottom: 12px; color: #fff !important; text-decoration: none; text-transform: uppercase; font-size: 11px; letter-spacing: 0.1em; transition: 0.3s; }
        .modal-btn:hover { background: rgba(197, 160, 89, 0.1); border-color: var(--gold); }
        .modal-btn i { font-size: 18px; color: var(--gold); }
        .close-modal { position: absolute; top: 10px; right: 15px; font-size: 28px; color: rgba(255,255,255,0.3); cursor: pointer; line-height: 1; }
        .close-modal:hover { color: #fff; }

        .action-area { display: flex; flex-direction: column; align-items: center; gap: 10px; margin-top: 40px; }
        @media (min-width: 768px) { .action-area { flex-direction: row; justify-content: center; gap: 15px; } }
        .btn-base { display: flex; align-items: center; justify-content: center; width: 100%; max-width: 280px; height: 52px; border: 1px solid rgba(197,160,89,0.3); text-transform: uppercase; font-size: 10px; letter-spacing: 0.15em; text-decoration: none; transition: 0.3s; }
        .btn-gold-fill { background: var(--gold); color: #000; font-weight: 700; border: none; }
        .note-after { margin-top: 6px; font-size: 12px; color: #a9a9a9; line-height: 1.5; }
    </style>
    </head>

    <body>
        <header class="py-8 text-center bg-black">
            <a href="https://garden-lounge.pro/udelnaya/menu" class="inline-block">
                <img src="<cms:show site_logo />" class="h-28 w-auto object-contain mx-auto" alt="Logo">
            </a>
        </header>

        <div class="nav-sticky">
            <nav class="tabs-wrap">
                <button onclick="switchTab('hookahs')" class="tab-btn active uppercase font-bold tracking-widest text-xs"><cms:show lbl_tab_1_en /></button>
                <button onclick="switchTab('kitchen')" class="tab-btn uppercase font-bold tracking-widest text-xs"><cms:show lbl_tab_2_en /></button>
                <button onclick="switchTab('bar-alc')" class="tab-btn uppercase font-bold tracking-widest text-xs"><cms:show lbl_tab_3_en /></button>
                <button onclick="switchTab('bar-non')" class="tab-btn uppercase font-bold tracking-widest text-xs"><cms:show lbl_tab_4_en /></button>
                <button onclick="switchTab('promos')" class="tab-btn uppercase font-bold tracking-widest text-xs"><cms:show lbl_tab_5_en /></button>
            </nav>

            <div id="subtabs-kitchen" class="subtabs-wrap">
                <button class="subtab-btn active" data-sub="snacks" onclick="switchSubtab('kitchen','snacks')"><cms:show kt_sub_snacks_en /></button>
                <button class="subtab-btn" data-sub="salads" onclick="switchSubtab('kitchen','salads')"><cms:show kt_sub_salads_en /></button>
                <button class="subtab-btn" data-sub="rolls" onclick="switchSubtab('kitchen','rolls')"><cms:show kt_sub_rolls_en /></button>
                <button class="subtab-btn" data-sub="soups" onclick="switchSubtab('kitchen','soups')"><cms:show kt_sub_soups_en /></button>
                <button class="subtab-btn" data-sub="poke_bowl_wok" onclick="switchSubtab('kitchen','poke_bowl_wok')"><cms:show kt_sub_poke_en /></button>
                <button class="subtab-btn" data-sub="hot" onclick="switchSubtab('kitchen','hot')"><cms:show kt_sub_hot_en /></button>
                <button class="subtab-btn" data-sub="desserts" onclick="switchSubtab('kitchen','desserts')"><cms:show kt_sub_desserts_en /></button>
            </div>
            <div id="subtabs-bar-alc" class="subtabs-wrap">
                <button class="subtab-btn active" data-sub="beer" onclick="switchSubtab('bar-alc','beer')"><cms:show bt_sub_beer_en /></button>
                <button class="subtab-btn" data-sub="wine" onclick="switchSubtab('bar-alc','wine')"><cms:show bt_sub_wine_en /></button>
                <button class="subtab-btn" data-sub="cocktails" onclick="switchSubtab('bar-alc','cocktails')"><cms:show bt_sub_cocktails_en /></button>
                <button class="subtab-btn" data-sub="spirits" onclick="switchSubtab('bar-alc','spirits')"><cms:show bt_sub_strong_en /></button>
            </div>
            <div id="subtabs-bar-non" class="subtabs-wrap">
                <button class="subtab-btn active" data-sub="tea_coffee" onclick="switchSubtab('bar-non','tea_coffee')"><cms:show dt_sub_tea_en /></button>
                <button class="subtab-btn" data-sub="lemonades" onclick="switchSubtab('bar-non','lemonades')"><cms:show dt_sub_lemon_en /></button>
            </div>

            <div class="gold-divider-nav"></div>
        </div>

        <main class="max-w-2xl mx-auto px-6 py-10 pb-24 min-h-screen">

            <div id="hookahs" class="tab-content active">
                <cms:show_repeatable 'rep_hookahs_v2'>
                    <div class="menu-row" data-subtab="all">
                        <cms:if row_type='header'>
                            <h3 class="category-title gold-shimmer"><cms:if cat_title_en><cms:show cat_title_en /><cms:else /><cms:show cat_title /></cms:if></h3>
                        <cms:else_if row_type='subheader' />
                            <h4 class="subcat-title gold-shimmer"><cms:if subcat_title_en><cms:show subcat_title_en /><cms:else /><cms:show subcat_title /></cms:if></h4>
                        <cms:else />
                            <div class="w-full pb-2 border-b border-white/5 mb-4">
                                <div class="grid grid-cols-[1fr_auto] gap-x-4 items-start">
                                    <div class="text-white text-lg flex items-center flex-wrap">
                                        <cms:if i_name_en><cms:show i_name_en /><cms:else /><cms:show i_name /></cms:if>
                                        <div class="badge-container">
                                            <cms:if item_tags='New' || item_tags='New + 🌶️'><span class="badge-item">New</span></cms:if>
                                            <cms:if item_tags='Hit' || item_tags='Hit + 🌶️'><span class="badge-item">Hit</span></cms:if>
                                            <cms:if item_tags='Special'><span class="badge-item">Special</span></cms:if>
                                            <cms:if item_tags='Chef’s Choice'><span class="badge-item badge-chef">Chef</span></cms:if>
                                            <cms:if item_tags='🌶️' || item_tags='New + 🌶️' || item_tags='Hit + 🌶️'><span class="badge-spicy"><i class="fa-solid fa-pepper-hot"></i></span></cms:if>
                                            <cms:if item_tags='🌶️🌶️'><span class="badge-spicy"><i class="fa-solid fa-pepper-hot"></i><i class="fa-solid fa-pepper-hot"></i></span></cms:if>
                                            <cms:if item_tags='🌶️🌶️🌶️'><span class="badge-spicy"><i class="fa-solid fa-pepper-hot"></i><i class="fa-solid fa-pepper-hot"></i><i class="fa-solid fa-pepper-hot"></i></span></cms:if>
                                        </div>
                                    </div>
                                    <span class="price-tag gold-shimmer"><cms:show i_price /> ₽</span>
                                    <cms:if i_desc_en || i_desc><div class="col-span-2 text-[12px] text-gray-400 mt-1 leading-relaxed"><cms:if i_desc_en><cms:show i_desc_en /><cms:else /><cms:show i_desc /></cms:if></div></cms:if>
                                    <cms:if note_after_ru_en || note_after_ru><div class="col-span-2 note-after"><cms:if note_after_ru_en><cms:show note_after_ru_en /><cms:else /><cms:show note_after_ru /></cms:if></div></cms:if>
                                </div>
                            </div>
                        </cms:if>
                    </div>
                </cms:show_repeatable>
            </div>

            <div id="kitchen" class="tab-content">
                <cms:show_repeatable 'rep_kitchen_v2'>
                    <cms:set cur_sub="<cms:if kitchen_subtab><cms:show kitchen_subtab /><cms:else />other</cms:if>" />
                    <div class="menu-row" data-subtab="<cms:show cur_sub />">
                        <cms:if row_type='header'>
                            <h3 class="category-title gold-shimmer"><cms:if cat_title_en><cms:show cat_title_en /><cms:else /><cms:show cat_title /></cms:if></h3>
                        <cms:else_if row_type='subheader' />
                            <h4 class="subcat-title gold-shimmer"><cms:if subcat_title_en><cms:show subcat_title_en /><cms:else /><cms:show subcat_title /></cms:if></h4>
                        <cms:else />
                            <div class="w-full pb-2 border-b border-white/5 mb-4">
                                <div class="grid grid-cols-[1fr_auto] gap-x-4 items-start">
                                    <div class="text-white text-lg flex items-center flex-wrap">
                                        <cms:if kit_name_en><cms:show kit_name_en /><cms:else /><cms:show kit_name /></cms:if>
                                        <div class="badge-container">
                                            <cms:if item_tags='New' || item_tags='New + 🌶️'><span class="badge-item">New</span></cms:if>
                                            <cms:if item_tags='Hit' || item_tags='Hit + 🌶️'><span class="badge-item">Hit</span></cms:if>
                                            <cms:if item_tags='Special'><span class="badge-item">Special</span></cms:if>
                                            <cms:if item_tags='Chef’s Choice'><span class="badge-item badge-chef">Chef</span></cms:if>
                                            <cms:if item_tags='🌶️' || item_tags='New + 🌶️' || item_tags='Hit + 🌶️'><span class="badge-spicy"><i class="fa-solid fa-pepper-hot"></i></span></cms:if>
                                            <cms:if item_tags='🌶️🌶️'><span class="badge-spicy"><i class="fa-solid fa-pepper-hot"></i><i class="fa-solid fa-pepper-hot"></i></span></cms:if>
                                            <cms:if item_tags='🌶️🌶️🌶️'><span class="badge-spicy"><i class="fa-solid fa-pepper-hot"></i><i class="fa-solid fa-pepper-hot"></i><i class="fa-solid fa-pepper-hot"></i></span></cms:if>
                                        </div>
                                    </div>
                                    <span class="price-tag gold-shimmer"><cms:show kit_price /> ₽</span>
                                    <cms:if kit_desc_en || kit_desc><div class="col-span-2 text-[12px] text-gray-400 mt-1 leading-relaxed"><cms:if kit_desc_en><cms:show kit_desc_en /><cms:else /><cms:show kit_desc /></cms:if></div></cms:if>
                                    <cms:if note_after_ru_en || note_after_ru><div class="col-span-2 note-after"><cms:if note_after_ru_en><cms:show note_after_ru_en /><cms:else /><cms:show note_after_ru /></cms:if></div></cms:if>
                                </div>
                            </div>
                        </cms:if>
                    </div>
                </cms:show_repeatable>
            </div>

            <div id="bar-alc" class="tab-content">
                <cms:show_repeatable 'rep_bar_alc_v2'>
                    <cms:set cur_sub="<cms:if bar_alc_subtab><cms:show bar_alc_subtab /><cms:else />other</cms:if>" />
                    <div class="menu-row" data-subtab="<cms:show cur_sub />">
                        <cms:if row_type='header'>
                            <h3 class="category-title gold-shimmer"><cms:if cat_title_en><cms:show cat_title_en /><cms:else /><cms:show cat_title /></cms:if></h3>
                        <cms:else_if row_type='subheader' />
                             <h4 class="subcat-title gold-shimmer"><cms:if subcat_title_en><cms:show subcat_title_en /><cms:else /><cms:show subcat_title /></cms:if></h4>
                        <cms:else />
                            <div class="w-full pb-2 border-b border-white/5 mb-4">
                                <div class="grid grid-cols-[1fr_auto] gap-x-4 items-start">
                                    <div class="text-white text-lg flex items-center flex-wrap">
                                        <cms:if i_name_en><cms:show i_name_en /><cms:else /><cms:show i_name /></cms:if>
                                        <div class="badge-container">
                                            <cms:if item_tags='New'><span class="badge-item">New</span></cms:if>
                                            <cms:if item_tags='Hit'><span class="badge-item">Hit</span></cms:if>
                                            <cms:if item_tags='Special'><span class="badge-item">Special</span></cms:if>
                                            <cms:if item_tags='Chef’s Choice'><span class="badge-item badge-chef">Chef</span></cms:if>
                                        </div>
                                    </div>
                                    <span class="price-tag gold-shimmer"><cms:show i_price /> ₽</span>
                                    <cms:if i_subheader_en || i_subheader><div class="col-span-2 text-[12px] text-gray-400 mt-1 leading-relaxed"><cms:if i_subheader_en><cms:show i_subheader_en /><cms:else /><cms:show i_subheader /></cms:if></div></cms:if>
                                    <cms:if note_after_ru_en || note_after_ru><div class="col-span-2 note-after"><cms:if note_after_ru_en><cms:show note_after_ru_en /><cms:else /><cms:show note_after_ru /></cms:if></div></cms:if>
                                </div>
                            </div>
                        </cms:if>
                    </div>
                </cms:show_repeatable>
            </div>

            <div id="bar-non" class="tab-content">
                <cms:show_repeatable 'rep_bar_non_v2'>
                    <cms:set cur_sub="<cms:if drinks_subtab><cms:show drinks_subtab /><cms:else />other</cms:if>" />
                    <div class="menu-row" data-subtab="<cms:show cur_sub />">
                        <cms:if row_type='header'>
                            <h3 class="category-title gold-shimmer"><cms:if cat_title_en><cms:show cat_title_en /><cms:else /><cms:show cat_title /></cms:if></h3>
                        <cms:else_if row_type='subheader' />
                             <h4 class="subcat-title gold-shimmer"><cms:if subcat_title_en><cms:show subcat_title_en /><cms:else /><cms:show subcat_title /></cms:if></h4>
                        <cms:else />
                            <div class="w-full pb-2 border-b border-white/5 mb-4">
                                <div class="grid grid-cols-[1fr_auto] gap-x-4 items-start">
                                    <div class="text-white text-lg flex items-center flex-wrap">
                                        <cms:if i_name_en><cms:show i_name_en /><cms:else /><cms:show i_name /></cms:if>
                                        <div class="badge-container">
                                            <cms:if item_tags='New'><span class="badge-item">New</span></cms:if>
                                            <cms:if item_tags='Hit'><span class="badge-item">Hit</span></cms:if>
                                            <cms:if item_tags='Special'><span class="badge-item">Special</span></cms:if>
                                            <cms:if item_tags='Chef’s Choice'><span class="badge-item badge-chef">Chef</span></cms:if>
                                        </div>
                                    </div>
                                    <span class="price-tag gold-shimmer"><cms:show i_price /> ₽</span>
                                    <cms:if i_desc_en || i_desc><div class="col-span-2 text-[12px] text-gray-400 mt-1 leading-relaxed"><cms:if i_desc_en><cms:show i_desc_en /><cms:else /><cms:show i_desc /></cms:if></div></cms:if>
                                    <cms:if note_after_ru_en || note_after_ru><div class="col-span-2 note-after"><cms:if note_after_ru_en><cms:show note_after_ru_en /><cms:else /><cms:show note_after_ru /></cms:if></div></cms:if>
                                </div>
                            </div>
                        </cms:if>
                    </div>
                </cms:show_repeatable>
            </div>

            <div id="promos" class="tab-content">
                <div class="taplink-block-wrapper">
                    <div class="film-grain"></div>
                    <div class="content-limiter">
                        <cms:pages masterpage='udelnaya/akzii.php' limit='1'>
                            <cms:embed 'promos-menu-block-en.html' />
                        </cms:pages>
                    </div>
                </div>
            </div>

            <div class="action-area">
                <a href="https://garden-lounge.pro/udelnaya/menu" class="btn-base">
                    <span class="subtitle-gold">Go Back</span>
                </a>
                <div onclick="openLoyaltyModal()" class="btn-base btn-gold-fill">
                    Loyalty Program
                </div>
                <a href="https://garden-lounge.pro/udelnaya/menu/visual/" class="btn-base">
                    <span class="subtitle-gold">Visual Menu</span>
                </a>
            </div>

        </main>

        <div id="loyalty-modal" onclick="closeLoyaltyModal(event)">
            <div class="modal-content" onclick="event.stopPropagation()">
                <span class="close-modal" onclick="closeLoyaltyModal()">&times;</span>
                <div class="modal-title gold-shimmer">Registration Method</div>
                <a href="https://access.clientomer.ru/feedback/676900-1/" target="_blank" class="modal-btn">
                    <i class="fa-solid fa-wallet"></i> Register via Wallet
                </a>
                <a href="https://t.me/GardenLounge_Loyalty_Bot" target="_blank" class="modal-btn">
                    <i class="fa-brands fa-telegram"></i> Register via Telegram
                </a>
            </div>
        </div>

        <script>
    const SUBTABS = { 'kitchen': 'subtabs-kitchen', 'bar-alc': 'subtabs-bar-alc', 'bar-non': 'subtabs-bar-non' };
    const ACTIVE = { 'kitchen': 'snacks', 'bar-alc': 'beer', 'bar-non': 'tea_coffee' };

    function scrollMenuToTop() {
        const anchor = document.querySelector('.nav-sticky') || document.querySelector('main.max-w-2xl');
        if (!anchor) {
            window.scrollTo(0, 0);
            return;
        }
        const run = () => {
            const top = Math.max(0, anchor.getBoundingClientRect().top + window.pageYOffset);
            const scroller = document.scrollingElement || document.documentElement;
            scroller.scrollTop = top;
            window.scrollTo(0, top);
        };
        run();
        requestAnimationFrame(run);
    }

    function switchTab(id, scrollTop = true) {
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        const target = document.getElementById(id);
        if(target) target.classList.add('active');

        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        const btn = Array.from(document.querySelectorAll('.tab-btn')).find(b => b.getAttribute('onclick').includes(id));
        if(btn) btn.classList.add('active');

        document.querySelectorAll('.subtabs-wrap').forEach(w => w.style.display = 'none');
        if(SUBTABS[id]) document.getElementById(SUBTABS[id]).style.display = 'flex';
        filter(id);
        if (scrollTop) scrollMenuToTop();
    }

    function switchSubtab(tab, sub) {
        ACTIVE[tab] = sub;
        const wrap = document.getElementById(SUBTABS[tab]);
        wrap.querySelectorAll('.subtab-btn').forEach(b => b.classList.remove('active'));
        const activeSubBtn = Array.from(wrap.querySelectorAll('.subtab-btn')).find(b => b.getAttribute('data-sub') === sub);
        if(activeSubBtn) activeSubBtn.classList.add('active');
        filter(tab);
        scrollMenuToTop();
    }

    function filter(tab) {
        const sub = ACTIVE[tab];
        const container = document.getElementById(tab);
        if(!container) return;
        const rows = container.querySelectorAll('.menu-row');
        rows.forEach(r => {
            const v = r.getAttribute('data-subtab');
            r.style.display = (tab === 'hookahs' || tab === 'promos' || v === sub) ? 'block' : 'none';
        });
    }

    function openLoyaltyModal() {
        document.getElementById('loyalty-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeLoyaltyModal() {
        document.getElementById('loyalty-modal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    document.addEventListener('DOMContentLoaded', () => { switchTab('hookahs', false); });
</script>
    </body>
    </html>
</cms:pages>
<?php COUCH::invoke(); ?>


