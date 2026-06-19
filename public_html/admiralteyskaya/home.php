<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Главная' name='home_section' executable='0' order='1' icon='home'>

    <cms:editable name='home_intro' label='Справка' type='message' order='1'>
        Настройки главной страницы https://garden-lounge.pro/ — логотип, филиалы, кнопки, галереи слайдеров, соцсети и оформление иконок.
    </cms:editable>

    <cms:editable name='group_seo' label='SEO' type='group' order='10' />
    <cms:editable name='home_seo_title' label='Заголовок (Title)' group='group_seo' type='text' order='11'>Garden Lounge - кальянные и лаунж-бары в Санкт-Петербурге</cms:editable>
    <cms:editable name='home_seo_desc' label='Описание (Description)' group='group_seo' type='textarea' order='12'>Garden Lounge в Санкт-Петербурге: выберите филиал на Адмиралтейской или Удельной. Кальянная, кухня, бар, VIP-комнаты, PS5 и звонок в выбранный филиал.</cms:editable>
    <cms:editable name='home_seo_keywords' label='Ключевые слова' group='group_seo' type='textarea' order='13'>Garden Lounge, кальянная СПб, лаунж бар СПб, кальянная Адмиралтейская, кальянная Удельная</cms:editable>
    <cms:editable name='home_seo_og_image' label='Картинка для соцсетей (OG Image)' group='group_seo' type='image' order='14'>https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/garden-main.jpg</cms:editable>
    <cms:editable name='home_seo_sr_text' label='Скрытый SEO-текст (для роботов)' group='group_seo' type='textarea' order='15'>Garden Lounge работает в двух филиалах Санкт-Петербурга: наб. реки Мойки 67-69 и ул. Аккуратова 13.</cms:editable>

    <cms:editable name='group_logo' label='Логотип' type='group' order='20' />
    <cms:editable name='home_logo' label='Изображение логотипа' group='group_logo' type='image' order='21'>:logo3.webp</cms:editable>
    <cms:editable name='home_logo_link' label='Ссылка с логотипа' group='group_logo' type='text' order='22'>/</cms:editable>
    <cms:editable name='home_logo_alt' label='Alt логотипа' group='group_logo' type='text' order='23'>Garden Lounge</cms:editable>
    <cms:editable name='home_favicon' label='Favicon' group='group_logo' type='image' order='24'>/udelnaya/favicon.png</cms:editable>

    <cms:editable name='group_adm' label='Филиал: Адмиралтейская' type='group' order='30' />
    <cms:editable name='home_adm_title' label='Заголовок (метро)' group='group_adm' type='text' order='31'>м. Адмиралтейская</cms:editable>
    <cms:editable name='home_adm_address' label='Адрес (текст)' group='group_adm' type='text' order='32'>наб. реки Мойки, 67-69</cms:editable>
    <cms:editable name='home_adm_map' label='Ссылка на карту' group='group_adm' type='text' order='33'>https://yandex.ru/maps/-/CPxBuF4-</cms:editable>
    <cms:editable name='home_adm_phone' label='Телефон (для ссылки tel:)' group='group_adm' type='text' order='34'>+79956246808</cms:editable>
    <cms:editable name='home_adm_telegram' label='Telegram филиала' group='group_adm' type='text' order='35'>https://t.me/gardenlounge_admiral</cms:editable>
    <cms:editable name='home_adm_btn_text' label='Текст кнопки' group='group_adm' type='text' order='36'>Войти в оазис</cms:editable>
    <cms:editable name='home_adm_btn_link' label='Ссылка кнопки / слайдера' group='group_adm' type='text' order='37'>/admiralteyskaya/</cms:editable>
    <cms:editable name='home_adm_phone_label' label='Подпись кнопки «Позвонить»' group='group_adm' type='text' order='38'>Позвонить на Адмиралтейскую</cms:editable>
    <cms:editable name='home_adm_tg_label' label='Подпись кнопки Telegram' group='group_adm' type='text' order='39'>Написать в Telegram на Адмиралтейскую</cms:editable>
    <cms:repeatable name='home_adm_gallery' label='Фото слайдера' group='group_adm' order='40'>
        <cms:editable name='home_gallery_img' label='Фото' type='image' />
        <cms:editable name='home_gallery_alt' label='Alt / SEO' type='text' />
    </cms:repeatable>

    <cms:editable name='group_udel' label='Филиал: Удельная' type='group' order='50' />
    <cms:editable name='home_udel_title' label='Заголовок (метро)' group='group_udel' type='text' order='51'>м. Удельная</cms:editable>
    <cms:editable name='home_udel_address' label='Адрес (текст)' group='group_udel' type='text' order='52'>ул. Аккуратова, 13</cms:editable>
    <cms:editable name='home_udel_map' label='Ссылка на карту' group='group_udel' type='text' order='53'>https://yandex.ru/maps/-/CPxBuAyI</cms:editable>
    <cms:editable name='home_udel_phone' label='Телефон (для ссылки tel:)' group='group_udel' type='text' order='54'>+79500473365</cms:editable>
    <cms:editable name='home_udel_telegram' label='Telegram филиала' group='group_udel' type='text' order='55'>https://t.me/gardenlounge_udelnaya</cms:editable>
    <cms:editable name='home_udel_btn_text' label='Текст кнопки' group='group_udel' type='text' order='56'>Выбрать сад</cms:editable>
    <cms:editable name='home_udel_btn_link' label='Ссылка кнопки / слайдера' group='group_udel' type='text' order='57'>/udelnaya/</cms:editable>
    <cms:editable name='home_udel_phone_label' label='Подпись кнопки «Позвонить»' group='group_udel' type='text' order='58'>Позвонить на Удельную</cms:editable>
    <cms:editable name='home_udel_tg_label' label='Подпись кнопки Telegram' group='group_udel' type='text' order='59'>Написать в Telegram на Удельную</cms:editable>
    <cms:repeatable name='home_udel_gallery' label='Фото слайдера' group='group_udel' order='60'>
        <cms:editable name='home_gallery_img' label='Фото' type='image' />
        <cms:editable name='home_gallery_alt' label='Alt / SEO' type='text' />
    </cms:repeatable>

    <cms:editable name='group_socials' label='Соцсети (низ страницы)' type='group' order='70' />
    <cms:editable name='home_instagram' label='Instagram — ссылка' group='group_socials' type='text' order='71'>https://instagram.com/garden_lounge_spb/</cms:editable>
    <cms:editable name='home_show_instagram' label='Instagram — показывать' group='group_socials' type='dropdown' opt_values='Да=1 | Нет=0' order='72'>1</cms:editable>
    <cms:editable name='home_vk' label='VK — ссылка' group='group_socials' type='text' order='73'>https://vk.com/loungegarden</cms:editable>
    <cms:editable name='home_show_vk' label='VK — показывать' group='group_socials' type='dropdown' opt_values='Да=1 | Нет=0' order='74'>1</cms:editable>
    <cms:editable name='home_youtube' label='YouTube — ссылка' group='group_socials' type='text' order='75'>https://youtube.com/@garden.lounge</cms:editable>
    <cms:editable name='home_show_youtube' label='YouTube — показывать' group='group_socials' type='dropdown' opt_values='Да=1 | Нет=0' order='76'>1</cms:editable>
    <cms:editable name='home_telegram' label='Telegram — ссылка' group='group_socials' type='text' order='77'>https://t.me/gardenlounge_admiral</cms:editable>
    <cms:editable name='home_show_telegram' label='Telegram — показывать' group='group_socials' type='dropdown' opt_values='Да=1 | Нет=0' order='78'>1</cms:editable>

    <cms:editable name='group_icons' label='Оформление иконок' type='group' order='80' />
    <cms:editable name='home_icon_color' label='Цвет иконок' group='group_icons' type='text' order='81'>#C5A059</cms:editable>
    <cms:editable name='home_icon_border_color' label='Цвет рамки иконок' group='group_icons' type='text' order='82'>rgba(197, 160, 89, 0.75)</cms:editable>
    <cms:editable name='home_icon_border_width' label='Толщина рамки (px)' group='group_icons' type='text' order='83'>2</cms:editable>
    <cms:editable name='home_icon_size' label='Размер контактных иконок (px)' group='group_icons' type='text' order='84'>48</cms:editable>
    <cms:editable name='home_icon_bg' label='Фон контактных иконок' group='group_icons' type='text' order='85'>rgba(0, 0, 0, 0.6)</cms:editable>
    <cms:editable name='home_social_size' label='Размер соц. иконок (px)' group='group_icons' type='text' order='86'>40</cms:editable>
    <cms:editable name='home_social_border_width' label='Толщина рамки соц. иконок (px)' group='group_icons' type='text' order='87'>3</cms:editable>
    <cms:editable name='home_icon_hover_style' label='Эффект при наведении' group='group_icons' type='dropdown' opt_values='Золотая заливка=gold | Светлее=light | Без заливки=none' order='88'>gold</cms:editable>
    <cms:editable name='home_phone_animation' label='Анимация телефона' group='group_icons' type='dropdown' opt_values='Включена=1 | Выключена=0' order='89'>1</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
