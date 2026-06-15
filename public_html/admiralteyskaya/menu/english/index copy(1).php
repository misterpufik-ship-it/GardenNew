<?php require_once( '../../couch/cms.php' ); ?>
<cms:template title='Меню EN' icon='globe' order='150'>
    <cms:editable name='info' type='message'>
        Этот шаблон автоматически отображает данные из русской версии (текст Адм).
        Все правки вносите в основном шаблоне по адресу: menu/text/index.php
    </cms:editable>
</cms:template>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <cms:pages masterpage='menu/text/index.php' limit='1'>
        <title><cms:show page_title_en /></title>
        <meta name="description" content="<cms:show meta_desc_en />">
        <meta property="og:title" content="<cms:show page_title_en />">
        <meta property="og:image" content="<cms:show site_logo />">

        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Montserrat:wght@200;300;400;500;600&display=swap" rel="stylesheet">

        <style>
            body { background-color: #000; color: #fff; font-family: 'Montserrat', sans-serif; margin: 0; overflow-x: hidden; }
            .font-serif-lux { font-family: 'Cormorant Garamond', serif; }
            :root { --gold: #C5A059; --gold-light: #FFEebb; --gold-dark: #8e7037; --bg-deep: #000000; --border-muted: #1a1a1a; }
            @keyframes shineGold { to { background-position: 200% center; } }
            .gold-shimmer {
                background: linear-gradient(to right, var(--gold-dark) 0%, var(--gold) 40%, var(--gold-light) 50%, var(--gold) 60%, var(--gold-dark) 100%);
                background-size: 200% auto; -webkit-background-clip: text; background-clip: text; color: transparent; animation: shineGold 5s linear infinite;
            }
            .nav-sticky { position: sticky; top: 0; z-index: 50; background-color: rgba(0, 0, 0, 0.95); backdrop-filter: blur(10px); }
            .gold-divider-nav { width: 100%; height: 1px; background: linear-gradient(90deg, transparent 0%, var(--gold) 50%, transparent 100%); opacity: 0.8; }
            .tab-btn { position: relative; transition: all 0.3s ease; color: #888; background: none; border: none; cursor: pointer; }
            .tab-btn.active { 
                background: linear-gradient(to right, var(--gold-dark) 0%, var(--gold) 40%, var(--gold-light) 50%, var(--gold) 60%, var(--gold-dark) 100%);
                background-size: 200% auto; -webkit-background-clip: text; background-clip: text; color: transparent; animation: shineGold 5s linear infinite;
            }
            .tab-btn.active::after {
                content: ''; position: absolute; bottom: -4px; left: 0; width: 100%; height: 2px;
                background: linear-gradient(to right, var(--gold-dark) 0%, var(--gold) 40%, var(--gold-light) 50%, var(--gold) 60%, var(--gold-dark) 100%);
                background-size: 200% auto; animation: shineGold 5s linear infinite;
            }
            .category-title { font-family: 'Cormorant Garamond', serif; font-style: italic; font-size: 1.875rem; margin-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem; text-align: center; }
            .price-tag { font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 1.25rem; white-space: nowrap; }
            .no-scrollbar::-webkit-scrollbar { display: none; }
            .tab-content { display: none; } .tab-content.active { display: block; animation: fadeIn 0.4s ease-out; }
            @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
            .taplink-block-wrapper { width: 100vw; position: relative; left: 50%; margin-left: -50vw; z-index: 10; background-color: var(--bg-deep); color: #EAEAEA; overflow: hidden; }
            .content-limiter { max-width: 600px; margin: 0 auto; width: 100%; position: relative; z-index: 10; padding: 40px 20px 30px 20px; }
            .film-grain { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: url('https://grainy-gradients.vercel.app/noise.svg'); opacity: 0.04; pointer-events: none; z-index: 1; }
            .shimmer-gold { background: linear-gradient(to right, #8e7037 0%, #C5A059 40%, #FFEebb 50%, #C5A059 60%, #8e7037 100%); background-size: 200% auto; color: transparent; -webkit-background-clip: text; background-clip: text; animation: shineGold 5s linear infinite; display: inline-block; }
            .promo-card { border: 1px solid rgba(197, 160, 89, 0.2); background-color: rgba(20, 20, 20, 0.4); padding: 20px 20px; text-align: center; transition: border-color 0.6s ease; }
            .gold-line-fade { width: 160px; height: 1px; background: linear-gradient(to right, transparent, var(--gold), transparent); margin: 16px auto; }
            .fade-up { animation: fadeUp 1s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; opacity: 0; transform: translateY(10px); }
            @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
        </style>
</head>
<body>

    <header class="py-8 text-center bg-black">
        <div class="flex justify-center items-center w-full">
            <a href="/admiralteyskaya/" class="block transition-transform duration-500 hover:scale-110">
                <img src="<cms:show site_logo />" alt="Logo" class="h-28 md:h-36 w-auto object-contain">
            </a>
        </div>
    </header>

    <div class="nav-sticky">
        <nav class="overflow-x-auto no-scrollbar flex items-center justify-between md:justify-center px-5 py-4 w-full md:gap-8">
            <button onclick="switchTab('hookahs')" class="tab-btn active text-xs font-bold uppercase tracking-widest whitespace-nowrap"><cms:show lbl_tab_1_en /></button>
            <span class="gold-shimmer text-xs md:hidden">|</span>
            <button onclick="switchTab('kitchen')" class="tab-btn text-xs font-bold uppercase tracking-widest whitespace-nowrap"><cms:show lbl_tab_2_en /></button>
            <span class="gold-shimmer text-xs md:hidden">|</span>
            <button onclick="switchTab('bar-alc')" class="tab-btn text-xs font-bold uppercase tracking-widest whitespace-nowrap"><cms:show lbl_tab_3_en /></button>
            <span class="gold-shimmer text-xs md:hidden">|</span>
            <button onclick="switchTab('bar-non')" class="tab-btn text-xs font-bold uppercase tracking-widest whitespace-nowrap"><cms:show lbl_tab_4_en /></button>
            <span class="gold-shimmer text-xs md:hidden">|</span>
            <button onclick="switchTab('promos')" class="tab-btn text-xs font-bold uppercase tracking-widest whitespace-nowrap"><cms:show lbl_tab_5_en /></button>
        </nav>
        <div class="gold-divider-nav"></div>
    </div>

    <main class="max-w-2xl mx-auto px-6 py-12 pb-24 min-h-screen">

        <div id="hookahs" class="tab-content active">
            <cms:show_repeatable 'rep_hookahs_v2'>
                <cms:if row_type == 'header'>
                    <cms:if k_count gt '1'></div></div></cms:if>
                    <div><h3 class="category-title gold-shimmer"><cms:show cat_title_en /></h3>
                    <div class="space-y-8">
                <cms:else />
                    <cms:if k_count == '1'><div><div class="space-y-8"></cms:if>
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-xl font-serif-lux text-white uppercase"><cms:show i_name_en /></h4>
                            <cms:if i_desc_en><p class="text-[10px] text-gray-500 mt-1 leading-tight"><cms:show i_desc_en /></p></cms:if>
                        </div>
                        <span class="price-tag gold-shimmer"><cms:show i_price /> ₽</span>
                    </div>
                </cms:if>
            </cms:show_repeatable>
            </div></div>
        </div>

        <div id="kitchen" class="tab-content">
            <cms:show_repeatable 'rep_kitchen_v2'>
                <cms:if row_type == 'header'>
                    <cms:if k_count gt '1'></div></div></cms:if>
                    <div><h3 class="category-title gold-shimmer"><cms:show cat_title_en /></h3>
                    <div class="space-y-6">
                <cms:else />
                    <cms:if k_count == '1'><div><div class="space-y-6"></cms:if>
                    <div class="flex justify-between items-start w-full">
                        <div>
                            <div class="text-white text-lg"><cms:show kit_name_en /></div>
                            <cms:if kit_desc_en><div class="text-[10px] text-gray-500 max-w-sm"><cms:show kit_desc_en /></div></cms:if>
                        </div>
                        <span class="price-tag gold-shimmer ml-4"><cms:show kit_price /> ₽</span>
                    </div>
                </cms:if>
            </cms:show_repeatable>
            </div></div>
        </div>

        <div id="bar-alc" class="tab-content">
            <cms:show_repeatable 'rep_bar_alc_v2'>
                <cms:if row_type == 'header'>
                    <cms:if k_count gt '1'></div></div></cms:if>
                    <div><h3 class="category-title gold-shimmer"><cms:show cat_title_en /></h3>
                    <div class="space-y-6">
                <cms:else />
                    <cms:if k_count == '1'><div><div class="space-y-6"></cms:if>
                    <div class="w-full">
                        <cms:if i_subheader_en><h4 class="gold-shimmer text-xs uppercase font-bold mt-4 mb-2"><cms:show i_subheader_en /></h4></cms:if>
                        <div class="flex justify-between items-start">
                            <span><cms:show i_name_en /></span> 
                            <span class="price-tag gold-shimmer text-base"><cms:show i_price /> ₽</span>
                        </div>
                    </div>
                </cms:if>
            </cms:show_repeatable>
            </div></div>
        </div>

        <div id="bar-non" class="tab-content">
            <cms:show_repeatable 'rep_bar_non_v2'>
                <cms:if row_type == 'header'>
                    <cms:if k_count gt '1'></div></div></cms:if>
                    <div><h3 class="category-title gold-shimmer"><cms:show cat_title_en /></h3>
                    <div class="space-y-6">
                <cms:else />
                    <cms:if k_count == '1'><div><div class="space-y-6"></cms:if>
                    <div class="text-sm w-full">
                        <cms:if i_subheader_en><h4 class="text-lg gold-shimmer font-serif-lux italic mb-2 text-center mt-6"><cms:show i_subheader_en /></h4></cms:if>
                        <div class="flex justify-between">
                            <span><cms:show i_name_en /></span> 
                            <span class="price-tag gold-shimmer text-base"><cms:show i_price /> ₽</span>
                        </div>
                        <cms:if i_desc_en><p class="text-[10px] text-gray-500 mt-1"><cms:show i_desc_en /></p></cms:if>
                    </div>
                </cms:if>
            </cms:show_repeatable>
            </div></div>
        </div>

        <div id="promos" class="tab-content">
            <div class="taplink-block-wrapper">
                <div class="film-grain"></div>
                <div class="content-limiter">
                    <header class="text-center mb-12 fade-up">
                        <h1 class="font-serif-lux text-3xl text-white font-light italic m-0"><cms:show promo_title_en /></h1>
                        <div class="gold-line-fade"></div>
                        <p class="text-[12px] uppercase tracking-[0.4em] shimmer-gold font-medium m-0"><cms:show promo_subtitle_en /></p>
                    </header>
                    <div class="space-y-3">
                        <cms:show_repeatable 'list_promos_v2'>
                            <div class="promo-card fade-up">
                                <h2 class="font-serif-lux text-2xl text-white italic mb-1"><cms:show p_title_en /></h2>
                                <p class="text-[12px] text-gray-400 font-light leading-relaxed mb-3 tracking-wide"><cms:show p_desc_en /></p>
                                <div class="w-6 h-px bg-[#C5A059]/30 mx-auto mb-3"></div>
                                <p class="text-[9px] uppercase tracking-[0.2em] shimmer-gold font-medium"><cms:show p_tag_en /></p>
                            </div>
                        </cms:show_repeatable>
                    </div>
                    <footer class="mt-8 text-center fade-up">
                        <p class="text-[10px] uppercase tracking-[0.3em] font-medium m-0 italic shimmer-gold"><cms:show promo_footer_en /></p>
                    </footer>
                </div>
            </div>
        </div>

    </main>
</cms:pages>

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            const activeBtn = Array.from(document.querySelectorAll('.tab-btn')).find(b => b.getAttribute('onclick').includes(tabId));
            if(activeBtn) activeBtn.classList.add('active');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
</body>
<?php COUCH::invoke(); ?>
</html>
