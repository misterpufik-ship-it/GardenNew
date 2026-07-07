<?php
if (!defined('K_TEMPLATE_NAME')) {
    define('K_TEMPLATE_NAME', 'udelnaya/header.php');
}
require_once dirname(__DIR__) . '/couch/cms.php';
?>
<cms:template title='Уделка Шапка' name='header_section' executable='0' order='10'>
    
    <cms:editable name='group_contacts' label='Контакты и Соцсети' type='group' />
    <cms:editable name='header_phone' label='Телефон (текст)' group='group_contacts' type='text'>+7 950 047-33-65</cms:editable>
    <cms:editable name='header_phone_link' label='Телефон (ссылка без пробелов)' group='group_contacts' type='text'>+79500473365</cms:editable>
    <cms:editable name='link_vk' label='VK ссылка' group='group_contacts' type='text'>https://vk.com/loungegarden</cms:editable>
    <cms:editable name='link_inst' label='Instagram ссылка' group='group_contacts' type='text'>https://instagram.com/garden_lounge_spb/</cms:editable>
    <cms:editable name='link_yt' label='YouTube ссылка' group='group_contacts' type='text'>https://youtube.com/@garden.lounge</cms:editable>
    <cms:editable name='link_tg' label='Telegram ссылка' group='group_contacts' type='text'>https://t.me/Garden_lounge_spb</cms:editable>

    <cms:editable name='group_menu' label='Навигация' type='group' />
    <cms:repeatable name='nav_menu' label='Пункты меню' group='group_menu'>
        <cms:editable name='item_name' label='Название пункта' type='text' />
        <cms:editable name='item_link' label='ID блока (например: #about-us)' type='text' />
        <cms:editable name='is_button' label='Выделить рамкой?' opt_values='Нет=0 | Да=1' type='dropdown' />
    </cms:repeatable>

    <cms:editable name='group_hero' label='Главный экран (Hero)' type='group' />
    <cms:editable name='hero_tagline' label='Слоган под лого' group='group_hero' type='text'>Магический вечнозеленый сад в самом сердце - на севере Санкт-Петербурга</cms:editable>
    <cms:editable name='hero_bg_desk' label='Фон (Десктоп)' group='group_hero' type='image'>:kalyannaya-garden-lounge-udelnaya-interer-spb.webp</cms:editable>
    <cms:editable name='hero_bg_mob' label='Фон (Мобилка)' group='group_hero' type='image'>:kalyannaya-garden-lounge-udelnaya-interer-spb.webp</cms:editable>
    <cms:editable name='hero_img_alt' label='Alt для главного фото' group='group_hero' type='text'>Интерьер кальянной Garden Lounge на Удельной, лаунж-бар на улице Аккуратова 13 в Санкт-Петербурге</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>


