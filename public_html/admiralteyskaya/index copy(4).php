<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='АА. Общая страница' order='2'>
    <cms:editable name='seo_group' label='SEO этой страницы (Приоритет)' type='group' order='1' />
        <cms:editable name='page_title' label='Заголовок (Title)' group='seo_group' type='text' />
        <cms:editable name='page_desc' label='Описание (Description)' group='seo_group' type='textarea' />
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
        "addressLocality": "Санкт-Петербург"
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
