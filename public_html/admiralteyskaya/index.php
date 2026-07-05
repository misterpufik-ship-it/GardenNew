<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Общая страница' order='220'>

    <cms:editable name='phil_title' label='Заголовок (тот, что Philosophy)' type='text' order='20'>Philosophy</cms:editable>
    <cms:editable name='phil_concept' label='Текст концепции (надпись)' type='text'>Концепция</cms:editable>
    <cms:editable name='phil_content' label='Основной текст (Редактор)' type='richtext'>
        Магический вечнозеленый сад, скрытый от городской суеты в самом сердце Петербурга.
        <br><br>
        Здесь время замедляет свой ход. Роскошный интерьер, утопающий в живых тропиках, мелодичный шум фонтана и уютное тепло камина создают атмосферу абсолютной гармонии и уединения.
    </cms:editable>
    <cms:editable name='phil_slogan' label='Слоган (внизу)' type='textarea'>Garden Lounge — место, где рождаются ритуалы, достойные ваших воспоминаний</cms:editable>
    <cms:editable name='phil_sep' label='Картинка разделителя' type='image'>:div.webp</cms:editable>

    <cms:editable name='seo_group' label='SEO и Оптимизация под ИИ' type='group' order='10' />
        <cms:editable name='phil_img_alt' label='Alt-текст для картинки' group='seo_group' type='text'>Эстетичный лаундж бар Санкт-Петербург</cms:editable>
        <cms:editable name='phil_lsi' label='LSI Ключи' group='seo_group' type='textarea'>кальянная спб, премиум лаундж</cms:editable>
</cms:template>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <cms:pages masterpage='globals.php' limit='1'>
        <cms:set global_title=seo_title_default 'global' />
        <cms:set global_desc=seo_desc_default 'global' />
        <cms:set adm_address=admiral_address 'global' />
        <cms:set adm_phone=admiral_phone 'global' />
        <cms:set adm_phone_clean=admiral_phone_clean 'global' />
        <cms:set adm_map=admiral_map_link 'global' />
        <cms:set social_vk=link_vk 'global' />
        <cms:set social_instagram=link_instagram 'global' />
        <cms:set social_telegram=link_telegram 'global' />
        <cms:set social_youtube=link_youtube 'global' />
    </cms:pages>

    <title><cms:if page_title><cms:show page_title /><cms:else /><cms:show global_title /></cms:if></title>
    <meta name="description" content="<cms:if page_desc><cms:show page_desc /><cms:else /><cms:show global_desc /></cms:if>">

    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/age-gate/assets.php'; gl_render_head_assets(); gl_preloader_render_head(); ?>
    <cms:pages masterpage='header.php' limit='1'>
        <link rel="preload" as="image" href="<cms:if hero_bg_desk='https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/garden-main.jpg'>couch/uploads/image/garden-main.webp<cms:else /><cms:show hero_bg_desk /></cms:if>" media="(min-width: 768px)" fetchpriority="high">
        <link rel="preload" as="image" href="<cms:if hero_bg_desk='https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/garden-main.jpg'>couch/uploads/image/garden-main.webp<cms:else /><cms:show hero_bg_desk /></cms:if>" media="(max-width: 767px)" fetchpriority="high">
        <link rel="preload" as="image" href="couch/uploads/image/logo3.webp" fetchpriority="high">
    </cms:pages>
    
    <cms:embed 'styles.html' />
    <cms:embed 'seo_tags.html' />
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": ["BarOrPub", "Restaurant"],
        "@id": "https://garden-lounge.pro/admiralteyskaya/#localbusiness",
        "name": "Garden Lounge на Адмиралтейской",
        "url": "https://garden-lounge.pro/admiralteyskaya/",
        "image": "https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/garden-main.jpg",
        "telephone": "<cms:show adm_phone />",
        "priceRange": "$$",
        "servesCuisine": ["Hookah lounge", "Kitchen", "Bar"],
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "<cms:show adm_address />",
            "addressLocality": "Санкт-Петербург",
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
    
    <link rel="stylesheet" href="main.css?v=<?php echo @filemtime(__DIR__ . '/main.css'); ?>">
    
   
    
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

    <?php gl_preloader_render(); ?>

    <cms:pages masterpage='header.php' limit='1'><cms:embed 'header.html' /></cms:pages>
    <cms:pages masterpage='about.php' limit='1'><cms:embed 'about-admiralteyskaya.html' /></cms:pages>
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
