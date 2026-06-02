<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='РђРґРјРёСЂР°Р»С‚РµР№СЃРєР°СЏ' order='2'>

    <cms:editable name='phil_title' label='Р—Р°РіРѕР»РѕРІРѕРє (С‚РѕС‚, С‡С‚Рѕ Philosophy)' type='text'>Philosophy</cms:editable>
    <cms:editable name='phil_concept' label='РўРµРєСЃС‚ РєРѕРЅС†РµРїС†РёРё (РЅР°РґРїРёСЃСЊ)' type='text'>РљРѕРЅС†РµРїС†РёСЏ</cms:editable>
    <cms:editable name='phil_content' label='РћСЃРЅРѕРІРЅРѕР№ С‚РµРєСЃС‚ (Р РµРґР°РєС‚РѕСЂ)' type='richtext'>
        РњР°РіРёС‡РµСЃРєРёР№ РІРµС‡РЅРѕР·РµР»РµРЅС‹Р№ СЃР°Рґ, СЃРєСЂС‹С‚С‹Р№ РѕС‚ РіРѕСЂРѕРґСЃРєРѕР№ СЃСѓРµС‚С‹ РІ СЃР°РјРѕРј СЃРµСЂРґС†Рµ РџРµС‚РµСЂР±СѓСЂРіР°.
        <br><br>
        Р—РґРµСЃСЊ РІСЂРµРјСЏ Р·Р°РјРµРґР»СЏРµС‚ СЃРІРѕР№ С…РѕРґ. Р РѕСЃРєРѕС€РЅС‹Р№ РёРЅС‚РµСЂСЊРµСЂ, СѓС‚РѕРїР°СЋС‰РёР№ РІ Р¶РёРІС‹С… С‚СЂРѕРїРёРєР°С…, РјРµР»РѕРґРёС‡РЅС‹Р№ С€СѓРј С„РѕРЅС‚Р°РЅР° Рё СѓСЋС‚РЅРѕРµ С‚РµРїР»Рѕ РєР°РјРёРЅР° СЃРѕР·РґР°СЋС‚ Р°С‚РјРѕСЃС„РµСЂСѓ Р°Р±СЃРѕР»СЋС‚РЅРѕР№ РіР°СЂРјРѕРЅРёРё Рё СѓРµРґРёРЅРµРЅРёСЏ.
    </cms:editable>
    <cms:editable name='phil_slogan' label='РЎР»РѕРіР°РЅ (РІРЅРёР·Сѓ)' type='textarea'>Garden Lounge вЂ” РјРµСЃС‚Рѕ, РіРґРµ СЂРѕР¶РґР°СЋС‚СЃСЏ СЂРёС‚СѓР°Р»С‹, РґРѕСЃС‚РѕР№РЅС‹Рµ РІР°С€РёС… РІРѕСЃРїРѕРјРёРЅР°РЅРёР№</cms:editable>
    <cms:editable name='phil_sep' label='РљР°СЂС‚РёРЅРєР° СЂР°Р·РґРµР»РёС‚РµР»СЏ' type='image'>/couch/uploads/image/div.webp</cms:editable>

    <cms:editable name='seo_group' label='SEO Рё РћРїС‚РёРјРёР·Р°С†РёСЏ РїРѕРґ РР (СЃРєСЂС‹С‚РѕРµ)' type='group' order='10' />
        <cms:editable name='phil_img_alt' label='Alt-С‚РµРєСЃС‚ РґР»СЏ РєР°СЂС‚РёРЅРєРё (РґР»СЏ РР-Р·СЂРµРЅРёСЏ)' group='seo_group' type='text'>Р­СЃС‚РµС‚РёС‡РЅС‹Р№ Р»Р°СѓРЅРґР¶ Р±Р°СЂ РЎР°РЅРєС‚-РџРµС‚РµСЂР±СѓСЂРі, РёРЅС‚РµСЂСЊРµСЂ</cms:editable>
        <cms:editable name='phil_lsi' label='LSI РљР»СЋС‡Рё РґР»СЏ РР (С‡РµСЂРµР· Р·Р°РїСЏС‚СѓСЋ)' group='seo_group' type='textarea'>РґРёР·Р°Р№РЅРµСЂСЃРєР°СЏ РєР°Р»СЊСЏРЅРЅР°СЏ РЎРџР±, РїСЂРµРјРёСѓРј Р»Р°СѓРЅРґР¶ РІ С†РµРЅС‚СЂРµ, РјРµСЃС‚Рѕ СЃ Р¶РёРІС‹РјРё СЂР°СЃС‚РµРЅРёСЏРјРё, С„РѕРЅС‚Р°РЅ, СѓСЋС‚РЅРѕРµ РјРµСЃС‚Рѕ</cms:editable>
</cms:template>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <cms:pages masterpage='globals.php' limit='1'>
        <cms:set global_title=default_seo_title 'global' />
        <cms:set global_desc=default_seo_desc 'global' />
        <cms:set adm_address=adm_address_field 'global' /> <cms:set adm_phone=adm_phone_field 'global' />     </cms:pages>

    <title>
        <cms:if page_title>
            <cms:show page_title />
        <cms:else />
            <cms:show global_title />
        </cms:if>
    </title>

    <meta name="description" content="<cms:if page_desc><cms:show page_desc /><cms:else /><cms:show global_desc /></cms:if>">

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BarOrPub",
      "name": "Garden Lounge",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "<cms:show adm_address />",
        "addressLocality": "РЎР°РЅРєС‚-РџРµС‚РµСЂР±СѓСЂРі"
      },
      "telephone": "<cms:show adm_phone />",
      "url": "<cms:show k_site_link />"
    }
    </script>

    <link rel="icon" type="image/png" href="favicon.png">
    <cms:embed 'styles.html' />
    <cms:embed 'seo_tags.html' />

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=Montserrat:wght@200;300;400;500;600&display=swap">

    <style>
        #scrollTopBtn {
            position: fixed !important; bottom: 30px !important; right: 30px !important;
            width: 45px !important; height: 45px !important;
            background-color: rgba(0, 0, 0, 0.8) !important;
            border: 1px solid rgba(197, 160, 89, 0.6) !important;
            color: #C5A059 !important; display: flex !important;
            align-items: center !important; justify-content: center !important;
            cursor: pointer !important; z-index: 999999 !important;
            transition: all 0.4s ease-in-out !important; opacity: 0; visibility: hidden;
            backdrop-filter: blur(10px) !important; border-radius: 50% !important;
        }
        #scrollTopBtn.show { opacity: 1 !important; visibility: visible !important; }
    </style>
</head>
<body>

    <cms:pages masterpage='header.php' limit='1'><cms:embed 'header.html' /></cms:pages>
    <cms:pages masterpage='about.php' limit='1'><cms:embed 'about.html' /></cms:pages>
    <cms:pages masterpage='gallery.php' limit='1'><cms:embed 'gallery.html' /></cms:pages>
    <cms:pages masterpage='menu.php' limit='1'><cms:embed 'menu.html' /></cms:pages>
    <cms:pages masterpage='akzii.php' limit='1'><cms:embed 'akzii.html' /></cms:pages>
    <cms:pages masterpage='reservation.php' limit='1'><cms:embed 'reservation.html' /></cms:pages>
    <cms:pages masterpage='contacts.php' limit='1'><cms:embed 'contacts.html' /></cms:pages>
    <cms:pages masterpage='filial.php' limit='1'><cms:embed 'filial.html' /></cms:pages>

    <cms:embed 'footer.html' />

    <div id="scrollTopBtn"><i class="fas fa-chevron-up"></i></div>

    <script>
        (function() {
            const btn = document.getElementById('scrollTopBtn');
            window.onscroll = () => {
                if (window.pageYOffset > 400) btn.classList.add('show');
                else btn.classList.remove('show');
            };
            btn.onclick = () => window.scrollTo({ top: 0, behavior: 'smooth' });
        })();
    </script>
</body>
</html>
<?php COUCH::invoke(); ?>