<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Футер и SEO' executable='0' order='210'>

    <!-- ГРУППА: ОТСТУПЫ МЕЖДУ БЛОКАМИ -->
    <cms:editable name='group_spacing' label='Отступы между блоками' type='group' order='5' />
        <cms:editable name='spacing_info' label='Справка' group='group_spacing' type='message' order='6'>
            Отступ — расстояние сверху и снизу каждого блока на странице филиала (Концепция, Experience, Меню и т.д.).
            Между двумя соседними блоками визуально будет примерно сумма нижнего отступа первого и верхнего отступа второго.
            Включите галочку «Применить ко всем блокам», чтобы задать одно значение сразу для всех разделов.
        </cms:editable>
        <cms:editable name='spacing_sync_all' label='Применить ко всем блокам' group='group_spacing' type='checkbox' opt_values='Да=1' order='7'>1</cms:editable>
        <cms:editable name='spacing_all_desk' label='Общий отступ — десктоп (px)' group='group_spacing' type='text' order='8'>28</cms:editable>
        <cms:editable name='spacing_all_mob' label='Общий отступ — мобильный (px)' group='group_spacing' type='text' order='9'>20</cms:editable>

        <cms:editable name='spacing_philosophy_desk' label='Концепция — десктоп (px)' group='group_spacing' type='text' not_active='spacing_sync_all=1' order='10'>28</cms:editable>
        <cms:editable name='spacing_philosophy_mob' label='Концепция — мобильный (px)' group='group_spacing' type='text' not_active='spacing_sync_all=1' order='11'>20</cms:editable>
        <cms:editable name='spacing_experience_desk' label='Experience (Интерьер) — десктоп (px)' group='group_spacing' type='text' not_active='spacing_sync_all=1' order='12'>28</cms:editable>
        <cms:editable name='spacing_experience_mob' label='Experience (Интерьер) — мобильный (px)' group='group_spacing' type='text' not_active='spacing_sync_all=1' order='13'>20</cms:editable>
        <cms:editable name='spacing_menu_desk' label='Меню — десктоп (px)' group='group_spacing' type='text' not_active='spacing_sync_all=1' order='14'>28</cms:editable>
        <cms:editable name='spacing_menu_mob' label='Меню — мобильный (px)' group='group_spacing' type='text' not_active='spacing_sync_all=1' order='15'>20</cms:editable>
        <cms:editable name='spacing_akzii_desk' label='Акции — десктоп (px)' group='group_spacing' type='text' not_active='spacing_sync_all=1' order='16'>28</cms:editable>
        <cms:editable name='spacing_akzii_mob' label='Акции — мобильный (px)' group='group_spacing' type='text' not_active='spacing_sync_all=1' order='17'>20</cms:editable>
        <cms:editable name='spacing_reservation_desk' label='Бронирование — десктоп (px)' group='group_spacing' type='text' not_active='spacing_sync_all=1' order='18'>28</cms:editable>
        <cms:editable name='spacing_reservation_mob' label='Бронирование — мобильный (px)' group='group_spacing' type='text' not_active='spacing_sync_all=1' order='19'>20</cms:editable>
        <cms:editable name='spacing_contacts_desk' label='Контакты — десктоп (px)' group='group_spacing' type='text' not_active='spacing_sync_all=1' order='20'>28</cms:editable>
        <cms:editable name='spacing_contacts_mob' label='Контакты — мобильный (px)' group='group_spacing' type='text' not_active='spacing_sync_all=1' order='21'>20</cms:editable>
        <cms:editable name='spacing_filial_desk' label='Филиал — десктоп (px)' group='group_spacing' type='text' not_active='spacing_sync_all=1' order='22'>28</cms:editable>
        <cms:editable name='spacing_filial_mob' label='Филиал — мобильный (px)' group='group_spacing' type='text' not_active='spacing_sync_all=1' order='23'>20</cms:editable>

    <!-- ГРУППА: ОБЩАЯ ИНФОРМАЦИЯ -->
    <cms:editable name='group_general' label='Общая информация' type='group' />
        <cms:editable name='site_description_text' label='Описание бренда (в футере)' group='group_general' type='textarea' height='100'>Магический вечнозеленый сад, скрытый от городской суеты в самом сердце Петербурга.</cms:editable>

    <!-- ГРУППА: СОЦ СЕТИ -->
    <cms:editable name='group_socials' label='Социальные сети' type='group' />
        <cms:editable name='link_instagram' label='Instagram (ссылка)' group='group_socials' type='text'>https://instagram.com/garden_lounge_spb/</cms:editable>
        <cms:editable name='link_telegram' label='Telegram канал (ссылка)' group='group_socials' type='text'>https://t.me/gardenlounge_admiral</cms:editable>
        <cms:editable name='link_vk' label='VK (ссылка)' group='group_socials' type='text'>https://vk.com/loungegarden</cms:editable>
        <cms:editable name='link_youtube' label='YouTube (ссылка)' group='group_socials' type='text'>https://youtube.com/@garden.lounge</cms:editable>

    <!-- ГРУППА: ФИЛИАЛ АДМИРАЛТЕЙСКАЯ -->
    <cms:editable name='group_admiral' label='Филиал: Адмиралтейская' type='group' />
        <cms:editable name='admiral_address' label='Адрес' group='group_admiral' type='text'>Наб. реки Мойки 67-69</cms:editable>
        <cms:editable name='admiral_map_link' label='Ссылка на Яндекс.Карты' group='group_admiral' type='text'>https://yandex.ru/maps/-/CLBURN0n</cms:editable>
        <cms:editable name='admiral_phone' label='Телефон (как отображается)' group='group_admiral' type='text'>+7 995 624-68-08</cms:editable>
        <cms:editable name='admiral_phone_clean' label='Телефон для ссылки (без пробелов, +7...)' group='group_admiral' type='text'>+79956246808</cms:editable>
        <cms:editable name='admiral_tg_chat' label='Ссылка на чат Telegram' group='group_admiral' type='text'>https://t.me/gardenlounge_admiral</cms:editable>
        <cms:editable name='admiral_hours_week' label='Часы работы (Пн-Чт, Вс)' group='group_admiral' type='text'>Пн–Чт; Вс: 12:00 – 01:00</cms:editable>
        <cms:editable name='admiral_hours_weekend' label='Часы работы (Пт-Сб)' group='group_admiral' type='text'>Пт–Сб: 12:00 – 03:00</cms:editable>

    <!-- ГРУППА: ФИЛИАЛ УДЕЛЬНАЯ -->
    <cms:editable name='group_udelnaya' label='Филиал: Удельная' type='group' />
        <cms:editable name='udel_address' label='Адрес' group='group_udelnaya' type='text'>Ул. Аккуратова 13</cms:editable>
        <cms:editable name='udel_map_link' label='Ссылка на Яндекс.Карты' group='group_udelnaya' type='text'>https://yandex.ru/maps/-/CPtpbQPg</cms:editable>
        <cms:editable name='udel_phone' label='Телефон (как отображается)' group='group_udelnaya' type='text'>+7 950 047-33-65</cms:editable>
        <cms:editable name='udel_phone_clean' label='Телефон для ссылки (без пробелов, +7...)' group='group_udelnaya' type='text'>+79500473365</cms:editable>
        <cms:editable name='udel_tg_chat' label='Ссылка на чат Telegram' group='group_udelnaya' type='text'>https://t.me/Garden_lounge_spb</cms:editable>
        <cms:editable name='udel_hours_week' label='Часы работы (Пн-Чт, Вс)' group='group_udelnaya' type='text'>Пн–Чт; Вс: 13:00 – 01:00</cms:editable>
        <cms:editable name='udel_hours_weekend' label='Часы работы (Пт-Сб)' group='group_udelnaya' type='text'>Пт–Сб: 13:00 – 03:00</cms:editable>

    <!-- ГРУППА: SEO ПО УМОЛЧАНИЮ -->
    <cms:editable name='group_seo' label='SEO по умолчанию' type='group' />
        <cms:editable name='seo_title_default' label='Заголовок (Title)' group='group_seo' type='text'>Garden Lounge на Адмиралтейской — кальянная и лаунж-бар в центре СПб</cms:editable>
        <cms:editable name='seo_desc_default' label='Описание (Description)' group='group_seo' type='textarea'>Garden Lounge на наб. реки Мойки 67-69: премиальные кальяны, кухня, VIP-комнаты и бронь столика рядом с метро Адмиралтейская. Тел. +7 995 624-68-08.</cms:editable>
        <cms:editable name='seo_keywords_default' label='Ключевые слова' group='group_seo' type='textarea'>Garden Lounge, кальянная СПб, кальянная у Адмиралтейской, лаунж бар СПб, кальянная в центре СПб, VIP кальянная СПб, кальянная с кухней, lounge bar, hookah bar, кальянная в центре, кальянная с необычным интерьером, набережная реки Мойки 67-69, Адмиралтейская, VIP-комнаты, PS5, кухня</cms:editable>
        <cms:editable name='seo_image_default' label='Картинка для соцсетей (OG Image)' group='group_seo' type='image'>https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/garden-main.jpg</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
