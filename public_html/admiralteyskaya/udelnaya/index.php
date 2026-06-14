<?php
define('K_TEMPLATE_NAME', 'udelnaya/index.php');
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
<cms:template title='РђРґРјРёСЂР°Р»С‚РµР№СЃРєР°СЏ' order='2'>
    
    <cms:editable name='phil_title' label='Р—Р°РіРѕР»РѕРІРѕРє (С‚РѕС‚, С‡С‚Рѕ Philosophy)' type='text'>Philosophy</cms:editable>
    <cms:editable name='phil_concept' label='РўРµРєСЃС‚ РєРѕРЅС†РµРїС†РёРё (РЅР°РґРїРёСЃСЊ)' type='text'>РљРѕРЅС†РµРїС†РёСЏ</cms:editable>
    <cms:editable name='phil_content' label='РћСЃРЅРѕРІРЅРѕР№ С‚РµРєСЃС‚ (Р РµРґР°РєС‚РѕСЂ)' type='richtext'>
        РњР°РіРёС‡РµСЃРєРёР№ РІРµС‡РЅРѕР·РµР»РµРЅС‹Р№ СЃР°Рґ, СЃРєСЂС‹С‚С‹Р№ РѕС‚ РіРѕСЂРѕРґСЃРєРѕР№ СЃСѓРµС‚С‹ РІ СЃР°РјРѕРј СЃРµСЂРґС†Рµ РџРµС‚РµСЂР±СѓСЂРіР°.
        <br><br>
        Р—РґРµСЃСЊ РІСЂРµРјСЏ Р·Р°РјРµРґР»СЏРµС‚ СЃРІРѕР№ С…РѕРґ. Р РѕСЃРєРѕС€РЅС‹Р№ РёРЅС‚РµСЂСЊРµСЂ, СѓС‚РѕРїР°СЋС‰РёР№ РІ Р¶РёРІС‹С… С‚СЂРѕРїРёРєР°С…, РјРµР»РѕРґРёС‡РЅС‹Р№ С€СѓРј С„РѕРЅС‚Р°РЅР° Рё СѓСЋС‚РЅРѕРµ С‚РµРїР»Рѕ РєР°РјРёРЅР° СЃРѕР·РґР°СЋС‚ Р°С‚РјРѕСЃС„РµСЂСѓ Р°Р±СЃРѕР»СЋС‚РЅРѕР№ РіР°СЂРјРѕРЅРёРё Рё СѓРµРґРёРЅРµРЅРёСЏ.
    </cms:editable>
    <cms:editable name='phil_slogan' label='РЎР»РѕРіР°РЅ (РІРЅРёР·Сѓ)' type='textarea'>Garden Lounge вЂ” РјРµСЃС‚Рѕ, РіРґРµ СЂРѕР¶РґР°СЋС‚СЃСЏ СЂРёС‚СѓР°Р»С‹, РґРѕСЃС‚РѕР№РЅС‹Рµ РІР°С€РёС… РІРѕСЃРїРѕРјРёРЅР°РЅРёР№</cms:editable>
    <cms:editable name='phil_sep' label='РљР°СЂС‚РёРЅРєР° СЂР°Р·РґРµР»РёС‚РµР»СЏ' type='image'>:div.webp</cms:editable>

    <cms:editable name='seo_group' label='SEO Рё РћРїС‚РёРјРёР·Р°С†РёСЏ РїРѕРґ РР' type='group' order='10' />
        <cms:editable name='phil_img_alt' label='Alt-С‚РµРєСЃС‚ РґР»СЏ РєР°СЂС‚РёРЅРєРё' group='seo_group' type='text'>Р­СЃС‚РµС‚РёС‡РЅС‹Р№ Р»Р°СѓРЅРґР¶ Р±Р°СЂ РЎР°РЅРєС‚-РџРµС‚РµСЂР±СѓСЂРі</cms:editable>
        <cms:editable name='phil_lsi' label='LSI РљР»СЋС‡Рё' group='seo_group' type='textarea'>РєР°Р»СЊСЏРЅРЅР°СЏ СЃРїР±, РїСЂРµРјРёСѓРј Р»Р°СѓРЅРґР¶</cms:editable>
</cms:template>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <cms:pages masterpage='udelnaya/globals.php' limit='1'>
        <cms:set global_title=seo_title_default 'global' />
        <cms:set global_desc=seo_desc_default 'global' />
        <cms:set adm_address=udel_address 'global' />
        <cms:set adm_phone=udel_phone 'global' />
        <cms:set adm_phone_clean=udel_phone_clean 'global' />
        <cms:set adm_map=udel_map_link 'global' />
        <cms:set social_vk=link_vk 'global' />
        <cms:set social_instagram=link_instagram 'global' />
        <cms:set social_telegram=link_telegram 'global' />
        <cms:set social_youtube=link_youtube 'global' />
    </cms:pages>

    <title><cms:if page_title><cms:show page_title /><cms:else /><cms:show global_title /></cms:if></title>
    <meta name="description" content="<cms:if page_desc><cms:show page_desc /><cms:else /><cms:show global_desc /></cms:if>">

    <link rel="icon" type="image/png" href="favicon.png">
    <cms:pages masterpage='udelnaya/header.php' limit='1'>
        <link rel="preload" as="image" href="<cms:if hero_bg_desk='https://garden-lounge.pro/udelnaya/couch/uploads/image/garden-main.jpg'>couch/uploads/image/garden-main.webp<cms:else /><cms:show hero_bg_desk /></cms:if>" media="(min-width: 768px)" fetchpriority="high">
        <link rel="preload" as="image" href="<cms:if hero_bg_desk='https://garden-lounge.pro/udelnaya/couch/uploads/image/garden-main.jpg'>couch/uploads/image/garden-main.webp<cms:else /><cms:show hero_bg_desk /></cms:if>" media="(max-width: 767px)" fetchpriority="high">
        <link rel="preload" as="image" href="couch/uploads/image/logo3.webp" fetchpriority="high">
    </cms:pages>
    
    <cms:embed 'styles.html' />
        <meta name="keywords" content="Garden Lounge, кальянная Удельная, кальянная СПб, лаунж бар Удельная, кальянная у метро Удельная, ул. Аккуратова 13, VIP-комнаты, PS5, кухня">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://garden-lounge.pro/udelnaya/">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://garden-lounge.pro/udelnaya/">
    <meta property="og:title" content="Garden Lounge на Удельной — кальянная и лаунж-бар в СПб">
    <meta property="og:description" content="Garden Lounge на Удельной: кальяны, кухня, напитки, VIP-комнаты, PS5 и бронирование столика на ул. Аккуратова 13.">
    <meta property="og:image" content="https://garden-lounge.pro/udelnaya/couch/uploads/image/garden-main.jpg">
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://garden-lounge.pro/udelnaya/">
    <meta property="twitter:title" content="Garden Lounge на Удельной — кальянная и лаунж-бар в СПб">
    <meta property="twitter:description" content="Кальянная Garden Lounge у метро Удельная: меню, кухня, бар, VIP-комнаты и бронь столика.">
    <meta property="twitter:image" content="https://garden-lounge.pro/udelnaya/couch/uploads/image/garden-main.jpg">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": ["BarOrPub", "Restaurant"],
        "@id": "https://garden-lounge.pro/udelnaya/#localbusiness",
        "name": "Garden Lounge на Удельной",
        "url": "https://garden-lounge.pro/udelnaya/",
        "image": "https://garden-lounge.pro/udelnaya/couch/uploads/image/garden-main.jpg",
        "telephone": "<cms:show adm_phone />",
        "priceRange": "$$",
        "servesCuisine": ["Hookah lounge", "Kitchen", "Bar"],
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "<cms:show adm_address />",
            "addressLocality": "РЎР°РЅРєС‚-РџРµС‚РµСЂР±СѓСЂРі",
            "addressCountry": "RU"
        },
        "openingHoursSpecification": [
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Sunday"],
                "opens": "12:00",
                "closes": "01:00"
            },
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": ["Friday", "Saturday"],
                "opens": "12:00",
                "closes": "03:00"
            }
        ],
        "sameAs": [
            "<cms:show social_vk />",
            "<cms:show social_instagram />",
            "<cms:show social_telegram />",
            "<cms:show social_youtube />"
        ],
        "hasMap": "<cms:show adm_map />"
    }
    </script>
    
    <link rel="stylesheet" href="main.css">
    
   
    
    <link rel="preload" as="style" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=Montserrat:wght@200;300;400;500;600&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=Montserrat:wght@200;300;400;500;600&display=swap">
    </noscript>

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

    <cms:pages masterpage='udelnaya/header.php' limit='1'><cms:embed 'header.html' /></cms:pages>
    <cms:pages masterpage='udelnaya/about.php' limit='1'><cms:embed 'about.html' /></cms:pages>
    <cms:pages masterpage='udelnaya/gallery.php' limit='1'><cms:embed 'gallery.html' /></cms:pages>
    <cms:pages masterpage='udelnaya/menu.php' limit='1'><cms:embed 'menu.html' /></cms:pages>
    <cms:pages masterpage='udelnaya/akzii.php' limit='1'><cms:embed 'akzii.html' /></cms:pages>
    <cms:pages masterpage='udelnaya/reservation.php' limit='1'><cms:embed 'reservation.html' /></cms:pages>
    <cms:pages masterpage='udelnaya/contacts.php' limit='1'><cms:embed 'contacts.html' /></cms:pages>
    <cms:pages masterpage='udelnaya/filial.php' limit='1'><cms:embed 'filial.html' /></cms:pages>

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



