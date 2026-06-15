<?php require_once( '../../couch/cms.php' ); ?>
<cms:template title='Адмирал Меню En' icon='globe' />

<cms:pages masterpage='menu/text/index.php' limit='1'>
    <cms:set my_lang='en' 'global' />
    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><cms:if page_title_en><cms:show page_title_en /><cms:else /><cms:show page_title /></cms:if></title>
        
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Montserrat:wght@200;300;400;500;600&display=swap" rel="stylesheet">

        <style>
            body { background-color: #000; color: #fff; font-family: 'Montserrat', sans-serif; margin: 0; overflow-x: hidden; }
            .font-serif-lux { font-family: 'Cormorant Garamond', serif; }
            :root { --gold:#C5A059; --gold-light:#FFEebb; --gold-dark:#8e7037; --bg-deep:#000; --border-muted:#1a1a1a; }
            @keyframes shineGold { to { background-position: 200% center; } }
            
            .gold-shimmer{
                background: linear-gradient(to right, var(--gold-dark) 0%, var(--gold) 40%, var(--gold-light) 50%, var(--gold) 60%, var(--gold-dark) 100%);
                background-size: 200% auto; -webkit-background-clip:text; background-clip:text; color: transparent; animation: shineGold 5s linear infinite;
            }
            .nav-sticky { position: sticky; top:0; z-index:50; background-color: rgba(0,0,0,0.95); backdrop-filter: blur(10px); border-bottom: 1px solid var(--border-muted); }
            .gold-divider-nav{ width:100%; height:1px; background: linear-gradient(90deg, transparent 0%, var(--gold) 50%, transparent 100%); opacity:0.8; margin-top: 6px; }
            
            .tab-btn{ position: relative; transition: all .3s ease; color:#888; background:none; border:none; cursor:pointer; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em;}
            .tab-btn.active{ background: linear-gradient(to right, var(--gold-dark) 0%, var(--gold) 40%, var(--gold-light) 50%, var(--gold) 60%, var(--gold-dark) 100%); background-size:200% auto; -webkit-background-clip:text; background-clip:text; color:transparent; animation: shineGold 5s linear infinite; }
            .tab-btn.active::after{ content:''; position:absolute; bottom:-4px; left:0; width:100%; height:2px; background: linear-gradient(to right, var(--gold-dark) 0%, var(--gold) 40%, var(--gold-light) 50%, var(--gold) 60%, var(--gold-dark) 100%); background-size:200% auto; animation: shineGold 5s linear infinite; }
            
            .tabs-wrap { display:flex; flex-wrap:wrap; justify-content:center; gap:14px 18px; padding: 14px 10px 8px; }
            .subtabs-wrap { display:none; flex-wrap:wrap; justify-content:center; gap:8px 10px; padding: 10px 10px 6px; }
            .subtab-btn{ border: 1px solid rgba(197,160,89,0.3); border-radius: 999px; padding: 6px 12px; font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase; color: #aaa; transition: all .25s ease; background: rgba(0,0,0,0.2); cursor:pointer; }
            .subtab-btn.active{ color: #000; background: linear-gradient(to right, var(--gold-dark) 0%, var(--gold) 40%, var(--gold-light) 50%, var(--gold) 60%, var(--gold-dark) 100%); background-size: 200% auto; animation: shineGold 5s linear infinite; border-color: rgba(197,160,89,0.9); }
            
            .tab-content { display:none; }
            .tab-content.active { display:block; animation: fadeIn .3s ease-out; }
            @keyframes fadeIn { from{ opacity:0; transform: translateY(5px);} to{opacity:1; transform: translateY(0);} }
            
            .category-title{ font-family: 'Cormorant Garamond', serif; font-style: italic; font-size: 1.7rem; margin-top: 2rem; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: .5rem; text-align: center; }
            .subcat-title{ font-family:'Montserrat', sans-serif; font-weight:700; letter-spacing: .2em; text-transform: uppercase; font-size:.7rem; text-align:center; margin-top: 1.5rem; margin-bottom: .8rem; }
            .price-tag{ font-family:'Montserrat', sans-serif; font-weight:500; font-size:1.1rem; white-space:nowrap; }
            .note-after{ margin-top: 4px; font-size: 11px; color: #777; }
            
            /* Стили для акций */
            .taplink-block-wrapper { width:100vw; position:relative; left:50%; margin-left:-50vw; background-color: #000; padding: 40px 0; overflow: hidden; }
            .content-limiter { max-width:600px; margin:0 auto; padding: 0 20px; position: relative; z-index: 10; }
            .promo-card { border:1px solid rgba(197,160,89,0.2); background-color: rgba(20,20,20,0.4); padding:20px; text-align:center; margin-bottom: 15px; }
            .gold-line-fade { width:160px; height:1px; background: linear-gradient(90deg, transparent, var(--gold), transparent); margin: 16px auto; }
            .shimmer-gold { background: linear-gradient(to right, #8e7037 0%, #C5A059 40%, #FFEebb 50%, #C5A059 60%, #8e7037 100%); background-size:200% auto; color:transparent; -webkit-background-clip:text; background-clip:text; animation: shineGold 5s linear infinite; display:inline-block; }

            /* НИЖНИЕ КНОПКИ */
            .action-area { display: flex; flex-direction: column; align-items: center; gap: 10px; margin-top: 40px; width: 100%; }
            @media (min-width: 768px) { .action-area { flex-direction: row; justify-content: center; gap: 15px; } }
            .btn-base {
                display: flex; align-items: center; justify-content: center;
                width: 100%; max-width: 280px; height: 52px;    
                border: 1px solid rgba(197, 160, 89, 0.3);
                text-transform: uppercase; font-size: 10px; letter-spacing: 0.15em;
                text-decoration: none; text-align: center; box-sizing: border-box; transition: 0.3s; cursor: pointer;
            }
            .btn-base:hover { border-color: #C5A059; }
            .btn-gold-fill { 
                background: linear-gradient(to right, #8e7037, #C5A059, #FFEebb, #C5A059, #8e7037); 
                background-size: 200% auto; animation: shineGold 5s linear infinite; 
                color: #000 !important; font-weight: 700; border: none; 
            }
            .subtitle-gold {
                background: linear-gradient(to right, #8e7037 0%, #C5A059 40%, #FFEebb 50%, #C5A059 60%, #8e7037 100%);
                background-size: 200% auto; -webkit-background-clip: text; background-clip: text;
                color: transparent; animation: shineGold 5s linear infinite;
            }

            /* СТИЛИ ПОПАПА ЛОЯЛЬНОСТИ */
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
        </style>
    </head>

    <body>
        <header class="py-6 text-center bg-black">
            <div class="flex justify-center">
                <img src="<cms:show site_logo />" class="h-24 w-auto object-contain">
            </div>
        </header>

        <div class="nav-sticky">
            <nav class="tabs-wrap">
                <button onclick="switchTab('hookahs')" class="tab-btn active"><cms:show lbl_tab_1_en /></button>
                <button onclick="switchTab('kitchen')" class="tab-btn"><cms:show lbl_tab_2_en /></button>
                <button onclick="switchTab('bar-alc')" class="tab-btn"><cms:show lbl_tab_3_en /></button>
                <button onclick="switchTab('bar-non')" class="tab-btn"><cms:show lbl_tab_4_en /></button>
                <button onclick="switchTab('promos')" class="tab-btn"><cms:show lbl_tab_5_en /></button>
            </nav>

            <div id="subtabs-kitchen" class="subtabs-wrap">
                <button class="subtab-btn active" data-sub="snacks" onclick="switchSubtab('kitchen','snacks')"><cms:show kt_sub_snacks_en /></button>
                <button class="subtab-btn" data-sub="salads" onclick="switchSubtab('kitchen','salads')"><cms:show kt_sub_salads_en /></button>
                <button class="subtab-btn" data-sub="rolls" onclick="switchSubtab('kitchen','rolls')"><cms:show kt_sub_rolls_en /></button>
                <button class="subtab-btn" data-sub="soups" onclick="switchSubtab('kitchen','soups')"><cms:show kt_sub_soups_en /></button>
                <button class="subtab-btn" data-sub="poke_bowl_wok" onclick="switchSubtab('kitchen','poke_bowl_wok')"><cms:show kt_sub_poke_en /></button>
                <button class="subtab-btn" data-sub="hot" onclick="switchSubtab('kitchen','hot')"><cms:show kt_sub_hot_en /></button>
                <button class="subtab-btn" data-sub="desserts" onclick="switchSubtab('kitchen','desserts')"><cms:show kt_sub_desserts_en /></button>
                <button class="subtab-btn" data-sub="other" onclick="switchSubtab('kitchen','other')">Other</button>
            </div>
            <div id="subtabs-bar-alc" class="subtabs-wrap">
                <button class="subtab-btn active" data-sub="beer" onclick="switchSubtab('bar-alc','beer')"><cms:show bt_sub_beer_en /></button>
                <button class="subtab-btn" data-sub="wine" onclick="switchSubtab('bar-alc','wine')"><cms:show bt_sub_wine_en /></button>
                <button class="subtab-btn" data-sub="cocktails" onclick="switchSubtab('bar-alc','cocktails')"><cms:show bt_sub_cocktails_en /></button>
                <button class="subtab-btn" data-sub="spirits" onclick="switchSubtab('bar-alc','spirits')"><cms:show bt_sub_strong_en /></button>
                <button class="subtab-btn" data-sub="other" onclick="switchSubtab('bar-alc','other')">Other</button>
            </div>
            <div id="subtabs-bar-non" class="subtabs-wrap">
                <button class="subtab-btn active" data-sub="tea_coffee" onclick="switchSubtab('bar-non','tea_coffee')"><cms:show dt_sub_tea_en /></button>
                <button class="subtab-btn" data-sub="lemonades" onclick="switchSubtab('bar-non','lemonades')"><cms:show dt_sub_lemon_en /></button>
                <button class="subtab-btn" data-sub="other" onclick="switchSubtab('bar-non','other')">Other</button>
            </div>

            <div class="gold-divider-nav"></div>
        </div>

        <main class="max-w-2xl mx-auto px-4 py-8 min-h-screen">
            
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
                                    <div class="text-white text-lg"><cms:if i_name_en><cms:show i_name_en /><cms:else /><cms:show i_name /></cms:if></div>
                                    <span class="price-tag gold-shimmer"><cms:show i_price /> ₽</span>
                                    <cms:if i_desc_en || i_desc><div class="col-span-2 text-[12px] text-gray-400 mt-1 leading-relaxed"><cms:if i_desc_en><cms:show i_desc_en /><cms:else /><cms:show i_desc /></cms:if></div></cms:if>
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
                                    <div class="text-white text-lg"><cms:if kit_name_en><cms:show kit_name_en /><cms:else /><cms:show kit_name /></cms:if></div>
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
                                <cms:if i_subheader_en || i_subheader><div class="gold-shimmer text-xs uppercase font-bold mt-3 mb-1"><cms:if i_subheader_en><cms:show i_subheader_en /><cms:else /><cms:show i_subheader /></cms:if></div></cms:if>
                                <div class="grid grid-cols-[1fr_auto] gap-x-4 items-start">
                                    <div class="text-white text-lg"><cms:if i_name_en><cms:show i_name_en /><cms:else /><cms:show i_name /></cms:if></div>
                                    <span class="price-tag gold-shimmer"><cms:show i_price /> ₽</span>
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
                                    <div class="text-white text-lg"><cms:if i_name_en><cms:show i_name_en /><cms:else /><cms:show i_name /></cms:if></div>
                                    <span class="price-tag gold-shimmer"><cms:show i_price /> ₽</span>
                                    <cms:if i_desc_en || i_desc><div class="col-span-2 text-[12px] text-gray-400 mt-1 leading-relaxed"><cms:if i_desc_en><cms:show i_desc_en /><cms:else /><cms:show i_desc /></cms:if></div></cms:if>
                                </div>
                            </div>
                        </cms:if>
                    </div>
                </cms:show_repeatable>
            </div>

            <div id="promos" class="tab-content">
                <div class="taplink-block-wrapper">
                    <div class="film-grain" style="position:absolute; top:0; left:0; width:100%; height:100%; background:url('https://grainy-gradients.vercel.app/noise.svg'); opacity:.04; pointer-events:none; z-index:1;"></div>
                    <div class="content-limiter">
                        <header class="text-center mb-12">
                            <h1 class="font-serif-lux text-3xl text-white font-light italic m-0">
                                <cms:if promo_title_en><cms:show promo_title_en /><cms:else /><cms:show promo_title /></cms:if>
                            </h1>
                            <div class="gold-line-fade"></div>
                            <p class="text-[12px] uppercase tracking-[0.4em] shimmer-gold font-medium m-0">
                                <cms:if promo_subtitle_en><cms:show promo_subtitle_en /><cms:else /><cms:show promo_subtitle /></cms:if>
                            </p>
                        </header>
                        <div class="space-y-3">
                            <cms:show_repeatable 'list_promos_v2'>
                                <div class="promo-card">
                                    <h2 class="font-serif-lux text-2xl text-white italic mb-1"><cms:if p_title_en><cms:show p_title_en /><cms:else /><cms:show p_title /></cms:if></h2>
                                    <p class="text-[12px] text-gray-400 font-light leading-relaxed mb-3 tracking-wide"><cms:if p_desc_en><cms:show p_desc_en /><cms:else /><cms:show p_desc /></cms:if></p>
                                    <div class="w-6 h-px bg-[#C5A059]/30 mx-auto mb-3"></div>
                                    <p class="text-[9px] uppercase tracking-[0.2em] shimmer-gold font-medium"><cms:if p_tag_en><cms:show p_tag_en /><cms:else /><cms:show p_tag /></cms:if></p>
                                </div>
                            </cms:show_repeatable>
                        </div>
                        <footer class="mt-8 text-center">
                            <p class="text-[10px] uppercase tracking-[0.3em] font-medium m-0 italic shimmer-gold"><cms:if promo_footer_en><cms:show promo_footer_en /><cms:else /><cms:show promo_footer /></cms:if></p>
                        </footer>
                        <div style="margin-top: 40px; text-align: center; opacity: 0.7;">
                            <img src="https://garden-lounge.pro/img/div.png" alt="Separator" style="max-width:280px; margin:0 auto;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="action-area">
                <a href="https://garden-lounge.pro/admiralteyskaya/menu" class="btn-base">
                    <span class="subtitle-gold">Go Back</span>
                </a>
                <div onclick="openLoyaltyModal()" class="btn-base btn-gold-fill">
                    Loyalty Program
                </div>
                <a href="https://garden-lounge.pro/admiralteyskaya/menu/visual/" class="btn-base">
                    <span class="subtitle-gold">Visual Menu</span>
                </a>
            </div>
            <div class="text-center mt-10 text-[9px] uppercase tracking-[0.4em] text-[#8e7037] opacity-50 mb-10" style="text-align: center; margin-top: 40px;">experience gastronomic delight</div>

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
            const MAIN_TABS_WITH_SUBTABS = { 'kitchen': 'subtabs-kitchen', 'bar-alc': 'subtabs-bar-alc', 'bar-non': 'subtabs-bar-non' };
            const ACTIVE_SUBTAB = { 'kitchen': 'snacks', 'bar-alc': 'beer', 'bar-non': 'tea_coffee', 'hookahs': 'all', 'promos': 'all' };

            function showSubtabsFor(tabId){
                document.querySelectorAll('.subtabs-wrap').forEach(el => el.style.display = 'none');
                if(MAIN_TABS_WITH_SUBTABS[tabId]){
                    const el = document.getElementById(MAIN_TABS_WITH_SUBTABS[tabId]);
                    if(el) el.style.display = 'flex';
                }
            }

            function applySubtabFilter(tabId){
                const container = document.getElementById(tabId);
                if(!container) return;
                const sub = ACTIVE_SUBTAB[tabId];
                const rows = container.querySelectorAll('.menu-row');
                rows.forEach(r => {
                    const v = r.getAttribute('data-subtab');
                    if(tabId === 'hookahs' || tabId === 'promos') {
                        r.style.display = 'block';
                    } else {
                        r.style.display = (v === sub) ? 'block' : 'none';
                    }
                });
            }

            function switchTab(tabId) {
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                const target = document.getElementById(tabId);
                if(target) target.classList.add('active');
                
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                const btn = Array.from(document.querySelectorAll('.tab-btn')).find(b => b.getAttribute('onclick').includes(tabId));
                if(btn) btn.classList.add('active');

                showSubtabsFor(tabId);
                applySubtabFilter(tabId);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            function switchSubtab(tabId, subId){
                ACTIVE_SUBTAB[tabId] = subId;
                const wrapId = MAIN_TABS_WITH_SUBTABS[tabId];
                if(wrapId){
                    const wrap = document.getElementById(wrapId);
                    wrap.querySelectorAll('.subtab-btn').forEach(b => b.classList.remove('active'));
                    const targetBtn = wrap.querySelector(`[data-sub="${subId}"]`);
                    if(targetBtn) targetBtn.classList.add('active');
                }
                applySubtabFilter(tabId);
            }

            // MODAL FUNCTIONS
            function openLoyaltyModal() { 
                document.getElementById('loyalty-modal').style.display = 'flex'; 
                document.body.style.overflow = 'hidden'; 
            }
            function closeLoyaltyModal() { 
                document.getElementById('loyalty-modal').style.display = 'none'; 
                document.body.style.overflow = 'auto'; 
            }

            document.addEventListener('DOMContentLoaded', () => {
                switchTab('hookahs');
            });
        </script>
    </body>
    </html>
</cms:pages>
<?php COUCH::invoke(); ?>
