<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Названия разделов' executable='0' order='230'>
    <cms:editable name='label_layout_mobile_menu' label='Гамбургер-меню (Общие)' type='text' order='9'>Гамбургер-меню</cms:editable>
    <cms:editable name='label_layout_hero' label='Hero — мобильный лого (Общие)' type='text' order='8'>Hero — мобильный лого</cms:editable>
    <cms:editable name='label_layout_scroll' label='Прокрутка по меню (Общие)' type='text' order='7'>Прокрутка по меню</cms:editable>
    <cms:editable name='label_layout_spacing' label='Отступы между блоками (Общие)' type='text' order='6'>Отступы между блоками</cms:editable>
    <cms:editable name='label_booking_settings' label='Бронирование Telegram (Общие)' type='text' order='2'>Бронирование Telegram</cms:editable>
    <cms:editable name='label_preloader_settings' label='Прелоадер (Общие)' type='text' order='3'>Прелоадер</cms:editable>
    <cms:editable name='label_age_gate_settings' label='Заглушка 18+ (Общие)' type='text' order='4'>Заглушка 18+</cms:editable>

    <cms:editable name='labels_info' label='Как менять названия' type='message' order='3'>
        Эти поля меняют подписи разделов в левом меню админки. Если поле оставить пустым, будет использовано стандартное название.
    </cms:editable>

    <cms:editable name='group_home' label='Главная' type='group' order='5' />
    <cms:editable name='label_home' label='Главная (garden-lounge.pro)' group='group_home' type='text' order='6'>Главная</cms:editable>

    <cms:editable name='group_a' label='Адмиралтейская' type='group' order='10' />
    <cms:editable name='label_header' label='Шапка сайта (Header)' group='group_a' type='text' order='11'>Шапка сайта (Header)</cms:editable>
    <cms:editable name='label_about' label='Концепция' group='group_a' type='text' order='12'>Концепция</cms:editable>
    <cms:editable name='label_akzii' label='Акции' group='group_a' type='text' order='13'>Акции</cms:editable>
    <cms:editable name='label_menu' label='Меню (общие настройки)' group='group_a' type='text' order='14'>Меню (общие настройки)</cms:editable>
    <cms:editable name='label_menu_text' label='Меню RU' group='group_a' type='text' order='15'>Меню RU</cms:editable>
    <cms:editable name='label_menu_en' label='Меню EN' group='group_a' type='text' order='16'>Меню EN</cms:editable>
    <cms:editable name='label_menu_visual' label='Меню визуальное' group='group_a' type='text' order='17'>Меню визуальное</cms:editable>
    <cms:editable name='label_sticky_sticker' label='Липкий стикер — Адмиралтейская' group='group_a' type='text' order='18'>Липкий стикер — Адмиралтейская</cms:editable>
    <cms:editable name='label_gallery' label='Галерея' group='group_a' type='text' order='19'>Галерея</cms:editable>
    <cms:editable name='label_reservation' label='Бронирование' group='group_a' type='text' order='20'>Бронирование</cms:editable>
    <cms:editable name='label_contacts' label='Контакты' group='group_a' type='text' order='21'>Контакты</cms:editable>
    <cms:editable name='label_filial' label='Филиал' group='group_a' type='text' order='22'>Филиал</cms:editable>
    <cms:editable name='label_faq' label='Вопросы и ответы' group='group_a' type='text' order='23'>Вопросы и ответы</cms:editable>
    <cms:editable name='label_globals' label='Футер и SEO' group='group_a' type='text' order='24'>Футер и SEO</cms:editable>
    <cms:editable name='label_index' label='Общая страница' group='group_a' type='text' order='25'>Общая страница</cms:editable>

    <cms:editable name='group_u' label='Удельная' type='group' order='40' />
    <cms:editable name='label_u_header' label='Шапка сайта (Header)' group='group_u' type='text' order='41'>Шапка сайта (Header)</cms:editable>
    <cms:editable name='label_u_about' label='Концепция' group='group_u' type='text' order='42'>Концепция</cms:editable>
    <cms:editable name='label_u_akzii' label='Акции' group='group_u' type='text' order='43'>Акции</cms:editable>
    <cms:editable name='label_u_menu' label='Меню (общие настройки)' group='group_u' type='text' order='44'>Меню (общие настройки)</cms:editable>
    <cms:editable name='label_u_menu_text' label='Меню RU' group='group_u' type='text' order='45'>Меню RU</cms:editable>
    <cms:editable name='label_u_menu_en' label='Меню EN' group='group_u' type='text' order='46'>Меню EN</cms:editable>
    <cms:editable name='label_u_menu_visual' label='Меню визуальное' group='group_u' type='text' order='47'>Меню визуальное</cms:editable>
    <cms:editable name='label_u_sticky_sticker' label='Липкий стикер — Удельная' group='group_u' type='text' order='48'>Липкий стикер — Удельная</cms:editable>
    <cms:editable name='label_u_gallery' label='Галерея' group='group_u' type='text' order='49'>Галерея</cms:editable>
    <cms:editable name='label_u_reservation' label='Бронирование' group='group_u' type='text' order='50'>Бронирование</cms:editable>
    <cms:editable name='label_u_contacts' label='Контакты' group='group_u' type='text' order='51'>Контакты</cms:editable>
    <cms:editable name='label_u_filial' label='Филиал' group='group_u' type='text' order='52'>Филиал</cms:editable>
    <cms:editable name='label_u_faq' label='Вопросы и ответы' group='group_u' type='text' order='53'>Вопросы и ответы</cms:editable>
    <cms:editable name='label_u_globals' label='Футер и SEO' group='group_u' type='text' order='54'>Футер и SEO</cms:editable>
    <cms:editable name='label_u_index' label='Общая страница' group='group_u' type='text' order='55'>Общая страница</cms:editable>
</cms:template>
