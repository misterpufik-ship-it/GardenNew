<?php
if (!defined('K_TEMPLATE_NAME')) {
    define('K_TEMPLATE_NAME', 'udelnaya/globals.php');
}
require_once dirname(__DIR__) . '/couch/cms.php';
?>
<cms:template title='Уделка Настройки' executable='0'>

    <!-- ГРУППА: ОБЩАЯ ИНФОРМАЦИЯ -->
    <cms:editable name='group_general' label='Общая информация' type='group' />
        <cms:editable name='site_description_text' label='Описание бренда (в футере)' group='group_general' type='textarea' height='100'>Магический вечнозеленый сад, скрытый от городской суеты в самом сердце - на севере Санкт-Петербурга.</cms:editable>

    <!-- ГРУППА: СОЦ СЕТИ -->
    <cms:editable name='group_socials' label='Социальные сети' type='group' />
        <cms:editable name='link_instagram' label='Instagram (ссылка)' group='group_socials' type='text'>https://www.instagram.com/garden_lounge_spb/</cms:editable>
        <cms:editable name='link_telegram' label='Telegram канал (ссылка)' group='group_socials' type='text'>https://t.me/Garden_lounge_spb</cms:editable>
        <cms:editable name='link_vk' label='VK (ссылка)' group='group_socials' type='text'>https://vk.com/loungegarden</cms:editable>
        <cms:editable name='link_youtube' label='YouTube (ссылка)' group='group_socials' type='text'>https://www.youtube.com/@garden.lounge</cms:editable>

    <!-- ГРУППА: ФИЛИАЛ АДМИРАЛТЕЙСКАЯ -->
    <cms:editable name='group_admiral' label='Филиал: Адмиралтейская' type='group' />
        <cms:editable name='admiral_address' label='Адрес' group='group_admiral' type='text'>Наб. реки Мойки 67-69</cms:editable>
        <cms:editable name='admiral_map_link' label='Ссылка на Яндекс.Карты' group='group_admiral' type='text'>https://yandex.ru/maps/-/CLBURN0n</cms:editable>
        <cms:editable name='admiral_phone' label='Телефон (как отображается)' group='group_admiral' type='text'>+7 995 624-68-08</cms:editable>
        <cms:editable name='admiral_phone_clean' label='Телефон для ссылки (без пробелов, +7...)' group='group_admiral' type='text'>+79956246808</cms:editable>
        <cms:editable name='admiral_tg_chat' label='Ссылка на чат Telegram' group='group_admiral' type='text'>https://t.me/Garden_lounge_spb</cms:editable>
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
        <cms:editable name='seo_title_default' label='Заголовок (Title)' group='group_seo' type='text'>Garden Lounge в Приморском районе — кальянная и лаунж-бар, м. Удельная</cms:editable>
        <cms:editable name='seo_desc_default' label='Описание (Description)' group='group_seo' type='textarea'>Garden Lounge на ул. Аккуратова 13, Приморский район: премиальные кальяны, кухня, VIP-комнаты, PS5 и бронь столика у метро Удельная. Тел. +7 950 047-33-65.</cms:editable>
        <cms:editable name='seo_keywords_default' label='Ключевые слова' group='group_seo' type='textarea'>Garden Lounge Приморский район, кальянная Приморский район, кальянная в Приморском районе, кальянная у метро Удельная, лаунж бар Приморский район, кальянная на севере СПб, кальянная СПб, ул. Аккуратова 13, VIP-комнаты, PS5, кухня</cms:editable>
        <cms:editable name='seo_image_default' label='Картинка для соцсетей (OG Image)' group='group_seo' type='image'>https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/kalyannaya-garden-lounge-udelnaya-interer-spb.jpg</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>


