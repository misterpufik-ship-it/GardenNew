<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Главная' name='home_section' executable='0' order='1'>

    <cms:editable name='home_intro' label='Справка' type='message' order='1'>
        Настройки главной страницы https://garden-lounge.pro — логотип, филиалы, кнопки, соцсети, оформление иконок и кнопок.
    </cms:editable>

    <cms:editable name='group_seo' label='SEO' type='group' order='10' />
    <cms:editable name='home_seo_title' label='Заголовок (Title)' group='group_seo' type='text' order='11'>Garden Lounge — кальянная и лаунж-бар в Санкт-Петербурге</cms:editable>
    <cms:editable name='home_seo_desc' label='Описание (Description)' group='group_seo' type='textarea' order='12'>Garden Lounge — два филиала в Санкт-Петербурге: Адмиралтейская в центре и Удельная на севере. Выберите заведение, посмотрите меню или забронируйте столик.</cms:editable>
    <cms:editable name='home_seo_keywords' label='Ключевые слова' group='group_seo' type='textarea' order='13'>Garden Lounge, Garden Lounge SPB, кальянная Санкт-Петербург, лаунж бар Санкт-Петербург</cms:editable>
    <cms:editable name='home_seo_og_image' label='Картинка для соцсетей (OG Image)' group='group_seo' type='image' order='14'>https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/garden-main.jpg</cms:editable>
    <cms:editable name='home_seo_sr_text' label='Скрытый SEO-текст (для роботов)' group='group_seo' type='textarea' order='15'>Garden Lounge — сеть лаунж-баров в Санкт-Петербурге. Филиалы: наб. реки Мойки 67-69 (м. Адмиралтейская) и ул. Аккуратова 13 (м. Удельная).</cms:editable>

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
    <cms:editable name='home_adm_btn_link' label='Ссылка кнопки' group='group_adm' type='text' order='37'>/admiralteyskaya/</cms:editable>
    <cms:editable name='home_adm_phone_label' label='Подпись кнопки «Позвонить»' group='group_adm' type='text' order='38'>Позвонить на Адмиралтейскую</cms:editable>
    <cms:editable name='home_adm_tg_label' label='Подпись кнопки Telegram' group='group_adm' type='text' order='39'>Написать в Telegram на Адмиралтейскую</cms:editable>
    <cms:repeatable name='home_adm_gallery' label='Фото слайдера' group='group_adm' order='40'>
        <cms:editable name='home_adm_gallery_img' label='Фото' type='image' />
        <cms:editable name='home_adm_gallery_alt' label='Alt / SEO' type='text' />
    </cms:repeatable>

    <cms:editable name='group_adm_btn' label='Оформление кнопки «Войти в оазис»' type='group' order='41' />
    <cms:editable name='home_adm_btn_color' label='Цвет текста' group='group_adm_btn' type='text' order='42'>#a68a5c</cms:editable>
    <cms:editable name='home_adm_btn_bg' label='Фон кнопки' group='group_adm_btn' type='text' order='43'>rgba(18, 16, 14, 0.55)</cms:editable>
    <cms:editable name='home_adm_btn_border_color' label='Цвет рамки' group='group_adm_btn' type='text' order='44'>rgba(166, 138, 92, 0.75)</cms:editable>
    <cms:editable name='home_adm_btn_border_width' label='Толщина рамки (px)' group='group_adm_btn' type='text' order='45'>1</cms:editable>
    <cms:editable name='home_adm_btn_font_size' label='Размер шрифта (px)' group='group_adm_btn' type='text' order='46'>11</cms:editable>
    <cms:editable name='home_adm_btn_letter_spacing' label='Межбуквенный интервал (em)' group='group_adm_btn' type='text' order='47'>0.18</cms:editable>
    <cms:editable name='home_adm_btn_padding_y' label='Отступ сверху/снизу (px)' group='group_adm_btn' type='text' order='48'>16</cms:editable>
    <cms:editable name='home_adm_btn_padding_x' label='Отступ слева/справа (px)' group='group_adm_btn' type='text' order='49'>32</cms:editable>
    <cms:editable name='home_adm_btn_min_width' label='Мин. ширина (px)' group='group_adm_btn' type='text' order='50'>220</cms:editable>
    <cms:editable name='home_adm_btn_radius' label='Скругление углов (px)' group='group_adm_btn' type='text' order='51'>0</cms:editable>
    <cms:editable name='home_adm_btn_animation' label='Анимация пульсации' group='group_adm_btn' type='dropdown' opt_values='Включена=1 | Выключена=0' order='52'>1</cms:editable>
    <cms:editable name='home_adm_btn_opacity_min' label='Мин. прозрачность (0–1)' group='group_adm_btn' type='text' order='53'>0.42</cms:editable>
    <cms:editable name='home_adm_btn_opacity_max' label='Макс. прозрачность (0–1)' group='group_adm_btn' type='text' order='54'>0.88</cms:editable>
    <cms:editable name='home_adm_btn_hover_color' label='Цвет текста при наведении' group='group_adm_btn' type='text' order='55'>#c5a059</cms:editable>
    <cms:editable name='home_adm_btn_hover_bg' label='Фон при наведении' group='group_adm_btn' type='text' order='56'>rgba(18, 16, 14, 0.78)</cms:editable>
    <cms:editable name='home_adm_btn_hover_border' label='Рамка при наведении' group='group_adm_btn' type='text' order='57'>rgba(197, 160, 89, 0.95)</cms:editable>

    <cms:editable name='group_udel' label='Филиал: Удельная' type='group' order='60' />
    <cms:editable name='home_udel_title' label='Заголовок (метро)' group='group_udel' type='text' order='61'>м. Удельная</cms:editable>
    <cms:editable name='home_udel_address' label='Адрес (текст)' group='group_udel' type='text' order='62'>ул. Аккуратова, 13</cms:editable>
    <cms:editable name='home_udel_map' label='Ссылка на карту' group='group_udel' type='text' order='63'>https://yandex.ru/maps/-/CPxBuAyI</cms:editable>
    <cms:editable name='home_udel_phone' label='Телефон (для ссылки tel:)' group='group_udel' type='text' order='64'>+79500473365</cms:editable>
    <cms:editable name='home_udel_telegram' label='Telegram филиала' group='group_udel' type='text' order='65'>https://t.me/gardenlounge_udelnaya</cms:editable>
    <cms:editable name='home_udel_btn_text' label='Текст кнопки' group='group_udel' type='text' order='66'>Выбрать сад</cms:editable>
    <cms:editable name='home_udel_btn_link' label='Ссылка кнопки' group='group_udel' type='text' order='67'>/udelnaya/</cms:editable>
    <cms:editable name='home_udel_phone_label' label='Подпись кнопки «Позвонить»' group='group_udel' type='text' order='68'>Позвонить на Удельную</cms:editable>
    <cms:editable name='home_udel_tg_label' label='Подпись кнопки Telegram' group='group_udel' type='text' order='69'>Написать в Telegram на Удельную</cms:editable>
    <cms:repeatable name='home_udel_gallery' label='Фото слайдера' group='group_udel' order='70'>
        <cms:editable name='home_udel_gallery_img' label='Фото' type='image' />
        <cms:editable name='home_udel_gallery_alt' label='Alt / SEO' type='text' />
    </cms:repeatable>

    <cms:editable name='group_udel_btn' label='Оформление кнопки «Выбрать сад»' type='group' order='71' />
    <cms:editable name='home_udel_btn_color' label='Цвет текста' group='group_udel_btn' type='text' order='72'>#a68a5c</cms:editable>
    <cms:editable name='home_udel_btn_bg' label='Фон кнопки' group='group_udel_btn' type='text' order='73'>rgba(18, 16, 14, 0.55)</cms:editable>
    <cms:editable name='home_udel_btn_border_color' label='Цвет рамки' group='group_udel_btn' type='text' order='74'>rgba(166, 138, 92, 0.75)</cms:editable>
    <cms:editable name='home_udel_btn_border_width' label='Толщина рамки (px)' group='group_udel_btn' type='text' order='75'>1</cms:editable>
    <cms:editable name='home_udel_btn_font_size' label='Размер шрифта (px)' group='group_udel_btn' type='text' order='76'>11</cms:editable>
    <cms:editable name='home_udel_btn_letter_spacing' label='Межбуквенный интервал (em)' group='group_udel_btn' type='text' order='77'>0.18</cms:editable>
    <cms:editable name='home_udel_btn_padding_y' label='Отступ сверху/снизу (px)' group='group_udel_btn' type='text' order='78'>16</cms:editable>
    <cms:editable name='home_udel_btn_padding_x' label='Отступ слева/справа (px)' group='group_udel_btn' type='text' order='79'>32</cms:editable>
    <cms:editable name='home_udel_btn_min_width' label='Мин. ширина (px)' group='group_udel_btn' type='text' order='80'>220</cms:editable>
    <cms:editable name='home_udel_btn_radius' label='Скругление углов (px)' group='group_udel_btn' type='text' order='81'>0</cms:editable>
    <cms:editable name='home_udel_btn_animation' label='Анимация пульсации' group='group_udel_btn' type='dropdown' opt_values='Включена=1 | Выключена=0' order='82'>1</cms:editable>
    <cms:editable name='home_udel_btn_opacity_min' label='Мин. прозрачность (0–1)' group='group_udel_btn' type='text' order='83'>0.42</cms:editable>
    <cms:editable name='home_udel_btn_opacity_max' label='Макс. прозрачность (0–1)' group='group_udel_btn' type='text' order='84'>0.88</cms:editable>
    <cms:editable name='home_udel_btn_hover_color' label='Цвет текста при наведении' group='group_udel_btn' type='text' order='85'>#c5a059</cms:editable>
    <cms:editable name='home_udel_btn_hover_bg' label='Фон при наведении' group='group_udel_btn' type='text' order='86'>rgba(18, 16, 14, 0.78)</cms:editable>
    <cms:editable name='home_udel_btn_hover_border' label='Рамка при наведении' group='group_udel_btn' type='text' order='87'>rgba(197, 160, 89, 0.95)</cms:editable>

    <cms:editable name='group_socials' label='Соцсети (низ страницы)' type='group' order='90' />
    <cms:editable name='home_instagram' label='Instagram — ссылка' group='group_socials' type='text' order='91'>https://instagram.com/garden_lounge_spb/</cms:editable>
    <cms:editable name='home_show_instagram' label='Instagram — показывать' group='group_socials' type='dropdown' opt_values='Да=1 | Нет=0' order='92'>1</cms:editable>
    <cms:editable name='home_vk' label='VK — ссылка' group='group_socials' type='text' order='93'>https://vk.com/loungegarden</cms:editable>
    <cms:editable name='home_show_vk' label='VK — показывать' group='group_socials' type='dropdown' opt_values='Да=1 | Нет=0' order='94'>1</cms:editable>
    <cms:editable name='home_youtube' label='YouTube — ссылка' group='group_socials' type='text' order='95'>https://youtube.com/@garden.lounge</cms:editable>
    <cms:editable name='home_show_youtube' label='YouTube — показывать' group='group_socials' type='dropdown' opt_values='Да=1 | Нет=0' order='96'>1</cms:editable>
    <cms:editable name='home_telegram' label='Telegram — ссылка' group='group_socials' type='text' order='97'>https://t.me/gardenlounge_admiral</cms:editable>
    <cms:editable name='home_show_telegram' label='Telegram — показывать' group='group_socials' type='dropdown' opt_values='Да=1 | Нет=0' order='98'>1</cms:editable>

    <cms:editable name='group_icons' label='Оформление иконок' type='group' order='100' />
    <cms:editable name='home_icon_color' label='Цвет иконок' group='group_icons' type='text' order='101'>#C5A059</cms:editable>
    <cms:editable name='home_icon_border_color' label='Цвет рамки иконок' group='group_icons' type='text' order='102'>rgba(197, 160, 89, 0.82)</cms:editable>
    <cms:editable name='home_icon_border_width' label='Толщина рамки (px)' group='group_icons' type='text' order='103'>2</cms:editable>
    <cms:editable name='home_icon_size' label='Размер контактных иконок (px)' group='group_icons' type='text' order='104'>48</cms:editable>
    <cms:editable name='home_icon_bg' label='Фон контактных иконок' group='group_icons' type='text' order='105'>rgba(0, 0, 0, 0.6)</cms:editable>
    <cms:editable name='home_social_size' label='Размер соц. иконок (px)' group='group_icons' type='text' order='106'>42</cms:editable>
    <cms:editable name='home_social_border_width' label='Толщина рамки соц. иконок (px)' group='group_icons' type='text' order='107'>1</cms:editable>
    <cms:editable name='home_icon_hover_style' label='Эффект при наведении' group='group_icons' type='dropdown' opt_values='Золотой заливкой=gold | Светлый=light | Без заливки=none' order='108'>gold</cms:editable>
    <cms:editable name='home_phone_animation' label='Анимация телефона' group='group_icons' type='dropdown' opt_values='Включена=1 | Выключена=0' order='109'>1</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
