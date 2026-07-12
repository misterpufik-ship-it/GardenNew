<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Блок Философия' order='20'>
    <cms:editable name='about_title' label='Главный заголовок' type='text'>Philosophy</cms:editable>
    <cms:editable name='about_concept' label='Текст над чертой' type='text'>Концепция</cms:editable>
    
    <cms:editable name='about_content' label='Основной текст' type='richtext'>
        Магический вечнозеленый сад, скрытый от городской суеты в самом сердце Петербурга. 
        <br><br>
        Здесь время замедляет свой ход. Роскошный интерьер, утопающий в живых тропиках, мелодичный шум фонтана и уютное тепло камина создают атмосферу абсолютной гармонии и уединения.
    </cms:editable>
    
    <cms:editable name='about_slogan' label='Слоган (внизу)' type='textarea'>Garden Lounge — место, где рождаются ритуалы, достойные ваших воспоминаний</cms:editable>
    <cms:editable name='about_sep_img' label='Картинка разделителя' type='image'>https://garden-lounge.pro/img/div.png</cms:editable>
</cms:template>

<style>
    /* Стили остаются прежними, они не влияют на работу админки */
    .philosophy-section-container {
        margin: 0; padding: 0; background-color: #000000;
        width: 100vw; position: relative; left: 50%; right: 50%;
        margin-left: -50vw; margin-right: -50vw; overflow: hidden;
    }
    .philosophy-wrapper {
        position: relative; width: 100%; min-height: 500px;
        display: flex; align-items: center; justify-content: center;
        background-color: #000000; color: #EAEAEA;
        font-family: 'Montserrat', sans-serif; padding: 60px 20px; 
    }
    .content-limiter { max-width: 600px; margin: 0 auto; width: 100%; position: relative; z-index: 10; }
    .title-philosophy { font-family: 'Cormorant Garamond', serif; font-size: 30px; font-weight: 300; font-style: italic; color: #ffffff; text-align: center; margin: 0; }
    .gold-line { width: 160px; height: 1px; margin: 16px auto; background: linear-gradient(to right, transparent, #C5A059, transparent); opacity: 0.8; }
    .title-concept { 
        font-family: 'Montserrat', sans-serif; font-size: 12px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.4em; text-align: center;
        background: linear-gradient(to right, #8e7037 0%, #C5A059 40%, #FFEebb 50%, #C5A059 60%, #8e7037 100%);
        background-size: 200% auto; -webkit-background-clip: text; background-clip: text; color: transparent; animation: shineGold 5s linear infinite;
    }
    .slogan-rituals {
        font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.2em; text-align: center; line-height: 1.8; margin: 40px auto; max-width: 400px;
        background: linear-gradient(to right, #8e7037 0%, #C5A059 40%, #FFEebb 50%, #C5A059 60%, #8e7037 100%);
        background-size: 200% auto; -webkit-background-clip: text; background-clip: text; color: transparent; animation: shineGold 5s linear infinite;
    }
    @keyframes shineGold { to { background-position: 100% center; } }
    .fade-up { animation: fadeUpEffect 1.2s ease; }
    @keyframes fadeUpEffect { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .film-grain { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: url('/img/noise.svg'); opacity: 0.04; pointer-events: none; z-index: 1; }
    .separator-img { max-width: 256px; width: 100%; height: auto; opacity: 0.9; margin: 0 auto; display: block; }
</style>

<div id="about-us" class="philosophy-section-container">
    <div class="philosophy-wrapper">
        <div class="film-grain"></div>

        <div class="content-limiter">
            <div class="fade-up">
                <h2 class="title-philosophy"><cms:show about_title /></h2>
            </div>

            <div class="fade-up" style="animation-delay: 0.2s;">
                <div class="gold-line"></div>
            </div>

            <div class="fade-up" style="animation-delay: 0.3s;">
                <p class="title-concept"><cms:show about_concept /></p>
            </div>
            
            <div class="fade-up" style="animation-delay: 0.5s;">
                <div class="text-sm text-gray-300 font-light leading-relaxed mt-12 mb-8 tracking-wide text-center">
                    <cms:show about_content />
                </div>
                
                <div class="w-12 h-[1px] bg-[#C5A059]/40 my-10 mx-auto"></div>
                
                <p class="slogan-rituals">
                    <cms:show about_slogan />
                </p>

                <div class="flex justify-center">
                    <img src="<cms:show about_sep_img />" alt="Separator" class="separator-img">
                </div>
            </div>
        </div>
    </div>
</div>

<?php COUCH::invoke(); ?>