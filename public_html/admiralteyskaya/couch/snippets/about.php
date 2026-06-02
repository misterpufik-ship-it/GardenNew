<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Р‘Р»РѕРє Р¤РёР»РѕСЃРѕС„РёСЏ' order='20'>
    <cms:editable name='about_title' label='Р“Р»Р°РІРЅС‹Р№ Р·Р°РіРѕР»РѕРІРѕРє' type='text'>Philosophy</cms:editable>
    <cms:editable name='about_concept' label='РўРµРєСЃС‚ РЅР°Рґ С‡РµСЂС‚РѕР№' type='text'>РљРѕРЅС†РµРїС†РёСЏ</cms:editable>

    <cms:editable name='about_content' label='РћСЃРЅРѕРІРЅРѕР№ С‚РµРєСЃС‚' type='richtext'>
        РњР°РіРёС‡РµСЃРєРёР№ РІРµС‡РЅРѕР·РµР»РµРЅС‹Р№ СЃР°Рґ, СЃРєСЂС‹С‚С‹Р№ РѕС‚ РіРѕСЂРѕРґСЃРєРѕР№ СЃСѓРµС‚С‹ РІ СЃР°РјРѕРј СЃРµСЂРґС†Рµ РџРµС‚РµСЂР±СѓСЂРіР°.
        <br><br>
        Р—РґРµСЃСЊ РІСЂРµРјСЏ Р·Р°РјРµРґР»СЏРµС‚ СЃРІРѕР№ С…РѕРґ. Р РѕСЃРєРѕС€РЅС‹Р№ РёРЅС‚РµСЂСЊРµСЂ, СѓС‚РѕРїР°СЋС‰РёР№ РІ Р¶РёРІС‹С… С‚СЂРѕРїРёРєР°С…, РјРµР»РѕРґРёС‡РЅС‹Р№ С€СѓРј С„РѕРЅС‚Р°РЅР° Рё СѓСЋС‚РЅРѕРµ С‚РµРїР»Рѕ РєР°РјРёРЅР° СЃРѕР·РґР°СЋС‚ Р°С‚РјРѕСЃС„РµСЂСѓ Р°Р±СЃРѕР»СЋС‚РЅРѕР№ РіР°СЂРјРѕРЅРёРё Рё СѓРµРґРёРЅРµРЅРёСЏ.
    </cms:editable>

    <cms:editable name='about_slogan' label='РЎР»РѕРіР°РЅ (РІРЅРёР·Сѓ)' type='textarea'>Garden Lounge вЂ” РјРµСЃС‚Рѕ, РіРґРµ СЂРѕР¶РґР°СЋС‚СЃСЏ СЂРёС‚СѓР°Р»С‹, РґРѕСЃС‚РѕР№РЅС‹Рµ РІР°С€РёС… РІРѕСЃРїРѕРјРёРЅР°РЅРёР№</cms:editable>
    <cms:editable name='about_sep_img' label='РљР°СЂС‚РёРЅРєР° СЂР°Р·РґРµР»РёС‚РµР»СЏ' type='image'>/couch/uploads/image/div.webp</cms:editable>
</cms:template>

<style>
    /* РЎС‚РёР»Рё РѕСЃС‚Р°СЋС‚СЃСЏ РїСЂРµР¶РЅРёРјРё, РѕРЅРё РЅРµ РІР»РёСЏСЋС‚ РЅР° СЂР°Р±РѕС‚Сѓ Р°РґРјРёРЅРєРё */
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
    @keyframes shineGold { to { background-position: 200% center; } }
    .fade-up { animation: fadeUpEffect 1.2s ease; }
    @keyframes fadeUpEffect { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .film-grain { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: url('https://grainy-gradients.vercel.app/noise.svg'); opacity: 0.04; pointer-events: none; z-index: 1; }
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