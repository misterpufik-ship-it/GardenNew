<?php
if (!defined('K_TEMPLATE_NAME')) {
    define('K_TEMPLATE_NAME', 'udelnaya/index.php');
}
require_once dirname(__DIR__) . '/couch/cms.php';
?>
<cms:template title='УУ. Общая страница' order='2'>
    
    <cms:editable name='phil_title' label='Заголовок (тот, что Philosophy)' type='text'>Philosophy</cms:editable>
    <cms:editable name='phil_concept' label='Текст концепции (надпись)' type='text'>Концепция</cms:editable>
    <cms:editable name='phil_content' label='Основной текст (Редактор)' type='richtext'>
        Магический вечнозеленый сад, скрытый от городской суеты в самом сердце Петербурга.
        <br><br>
        Здесь время замедляет свой ход. Роскошный интерьер, утопающий в живых тропиках, мелодичный шум фонтана и уютное тепло камина создают атмосферу абсолютной гармонии и уединения.
    </cms:editable>
    <cms:editable name='phil_slogan' label='Слоган (внизу)' type='textarea'>Garden Lounge — тайный тропический оазис на севере города, где время замедляется.</cms:editable>
    <cms:editable name='phil_sep' label='Картинка разделителя' type='image'>:div.webp</cms:editable>

    <cms:editable name='seo_group' label='SEO и Оптимизация под ИИ' type='group' order='10' />
        <cms:editable name='phil_img_alt' label='Alt-текст для картинки' group='seo_group' type='text'>Эстетичный лаундж бар Санкт-Петербург</cms:editable>
        <cms:editable name='phil_lsi' label='LSI Ключи' group='seo_group' type='textarea'>кальянная спб, премиум лаундж</cms:editable>
</cms:template>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/age-gate/assets.php'; gl_render_base_href('udelnaya'); gl_render_font_assets(); ?>
    <?php gl_render_blocking_stylesheet(gl_branch_main_css_url('udelnaya')); ?>

    <cms:pages masterpage='udelnaya/globals.php' limit='1'>
        <cms:set global_title=seo_title_default 'global' />
        <cms:set global_desc=seo_desc_default 'global' />
        <cms:set seo_keywords=seo_keywords_default scope='global' />
        <cms:set seo_canonical='https://garden-lounge.pro/udelnaya' scope='global' />
        <cms:set seo_og_image='https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/kalyannaya-garden-lounge-udelnaya-interer-spb.jpg' scope='global' />
        <cms:set adm_address=udel_address 'global' />
        <cms:set adm_phone=udel_phone 'global' />
        <cms:set adm_phone_clean=udel_phone_clean 'global' />
        <cms:set adm_map=udel_map_link 'global' />
        <cms:set social_vk=link_vk 'global' />
        <cms:set social_instagram=link_instagram 'global' />
        <cms:set social_telegram=link_telegram 'global' />
        <cms:set social_youtube=link_youtube 'global' />
    </cms:pages>

    <cms:set global_title='Garden Lounge в Приморском районе — кальянная и лаунж-бар, м. Удельная' 'global' />
    <cms:set global_desc='Garden Lounge на ул. Аккуратова 13, Приморский район: премиальные кальяны, кухня, VIP-комнаты, PS5 и бронь столика у метро Удельная. Тел. +7 950 047-33-65.' 'global' />

    <title><cms:show global_title /></title>
    <meta name="description" content="<cms:show global_desc />">

    <cms:embed 'styles.html' />
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <?php gl_render_blocking_stylesheet('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'); ?>
    <?php gl_render_head_assets(); gl_preloader_render_head(); ?>
    <cms:pages masterpage='udelnaya/header.php' limit='1'>
        <cms:php>
        global $CTX;
        require_once $_SERVER['DOCUMENT_ROOT'] . '/age-gate/hero-helpers.php';
        $desk = (string) $CTX->get('hero_bg_desk');
        $mob = (string) $CTX->get('hero_bg_mob');
        if ($mob === '') {
            $mob = $desk;
        }
        gl_hero_render_preload_tags($desk, $mob);
        </cms:php>
    </cms:pages>
    
    <cms:embed 'seo_tags.html' />
    <cms:pages masterpage='udelnaya/contacts.php' limit='1'>
    <cms:php>
    global $CTX;
    require_once $_SERVER['DOCUMENT_ROOT'] . '/age-gate/schema-helpers.php';
    gl_render_localbusiness_schema(array(
        'id' => 'https://garden-lounge.pro/udelnaya#localbusiness',
        'name' => 'Garden Lounge на Удельной',
        'url' => 'https://garden-lounge.pro/udelnaya',
        'image' => 'https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/kalyannaya-garden-lounge-udelnaya-interer-spb.jpg',
        'telephone' => (string) $CTX->get('adm_phone'),
        'streetAddress' => (string) $CTX->get('adm_address'),
        'latitude' => 60.0165,
        'longitude' => 30.3142,
        'hasMap' => (string) $CTX->get('adm_map'),
        'sameAs' => array_values(array_filter(array(
            (string) $CTX->get('social_vk'),
            (string) $CTX->get('social_instagram'),
            (string) $CTX->get('social_telegram'),
            (string) $CTX->get('social_youtube'),
            'https://maps.app.goo.gl/rcwMbaXdfrbSTowd7',
        ))),
        'openingHours' => array(
            array(
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Sunday'),
                'opens' => '13:00',
                'closes' => '01:00',
            ),
            array(
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => array('Friday', 'Saturday'),
                'opens' => '13:00',
                'closes' => '03:00',
            ),
        ),
        'ratingValue' => (string) $CTX->get('rate_yandex_val'),
        'ratingCountText' => (string) $CTX->get('rate_yandex_count'),
    ));
    </cms:php>
    </cms:pages>

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

    <cms:set is_udelnaya='1' 'global' />
    <cms:pages masterpage='udelnaya/header.php' limit='1'>
        <cms:embed 'header.html' />
    </cms:pages>
    <cms:pages masterpage='udelnaya/about.php' limit='1'><cms:embed 'about-udelnaya.html' /></cms:pages>
    <cms:pages masterpage='udelnaya/gallery.php' limit='1'><cms:embed 'gallery.html' /></cms:pages>
    <cms:pages masterpage='udelnaya/menu.php' limit='1'><cms:embed 'menu.html' /></cms:pages>
    <cms:pages masterpage='udelnaya/akzii.php' limit='1'><cms:embed 'akzii.html' /></cms:pages>
    <cms:pages masterpage='udelnaya/reservation.php' limit='1'><cms:embed 'reservation.html' /></cms:pages>
    <cms:pages masterpage='udelnaya/contacts.php' limit='1'><cms:embed 'contacts.html' /></cms:pages>
    <cms:pages masterpage='udelnaya/filial.php' limit='1'><cms:embed 'filial.html' /></cms:pages>
    <cms:embed 'faq.html' />

    <style>
        .footer-brand-col { order: 1; }
        .footer-branch-udelnaya { order: 2; }
        .footer-branch-admiral { order: 3; }
        .footer-nav-col { order: 4; }
    </style>
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



