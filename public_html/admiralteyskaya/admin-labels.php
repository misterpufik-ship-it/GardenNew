<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='А. Названия разделов' executable='0' order='90'>
    <cms:editable name='labels_info' label='Как менять названия' type='message' order='1'>
        Эти поля меняют подписи разделов в левом меню админки. Если поле оставить пустым, будет использовано стандартное название.
    </cms:editable>

    <cms:editable name='group_a' label='Адмиралтейская' type='group' order='10' />
    <cms:editable name='label_index' label='А. Общая страница' group='group_a' type='text' order='11'>А. Общая страница</cms:editable>
    <cms:editable name='label_header' label='А. Шапка сайта (Header)' group='group_a' type='text' order='12'>А. Шапка сайта (Header)</cms:editable>
    <cms:editable name='label_about' label='А. Философия' group='group_a' type='text' order='13'>А. Философия</cms:editable>
    <cms:editable name='label_akzii' label='А. Акции' group='group_a' type='text' order='14'>А. Акции</cms:editable>
    <cms:editable name='label_menu' label='А. Меню' group='group_a' type='text' order='15'>А. Меню</cms:editable>
    <cms:editable name='label_gallery' label='А. Галерея' group='group_a' type='text' order='16'>А. Галерея</cms:editable>
    <cms:editable name='label_reservation' label='А. Бронирование' group='group_a' type='text' order='17'>А. Бронирование</cms:editable>
    <cms:editable name='label_contacts' label='А. Контакты' group='group_a' type='text' order='18'>А. Контакты</cms:editable>
    <cms:editable name='label_filial' label='А. Филиал' group='group_a' type='text' order='19'>А. Филиал</cms:editable>
    <cms:editable name='label_globals' label='А. Настройки сайта (Футер и SEO)' group='group_a' type='text' order='20'>А. Настройки сайта (Футер и SEO)</cms:editable>
    <cms:editable name='label_menu_text' label='А. Меню текст' group='group_a' type='text' order='21'>А. Меню текст</cms:editable>
    <cms:editable name='label_menu_visual' label='А. Меню визуальное' group='group_a' type='text' order='22'>А. Меню визуальное</cms:editable>
    <cms:editable name='label_menu_en' label='А. Меню EN' group='group_a' type='text' order='23'>А. Меню EN</cms:editable>

    <cms:editable name='group_u' label='Удельная' type='group' order='40' />
    <cms:editable name='label_u_index' label='У. Общая страница' group='group_u' type='text' order='41'>У. Общая страница</cms:editable>
    <cms:editable name='label_u_header' label='У. Шапка сайта (Header)' group='group_u' type='text' order='42'>У. Шапка сайта (Header)</cms:editable>
    <cms:editable name='label_u_about' label='У. Философия' group='group_u' type='text' order='43'>У. Философия</cms:editable>
    <cms:editable name='label_u_akzii' label='У. Акции' group='group_u' type='text' order='44'>У. Акции</cms:editable>
    <cms:editable name='label_u_menu' label='У. Меню' group='group_u' type='text' order='45'>У. Меню</cms:editable>
    <cms:editable name='label_u_gallery' label='У. Галерея' group='group_u' type='text' order='46'>У. Галерея</cms:editable>
    <cms:editable name='label_u_reservation' label='У. Бронирование' group='group_u' type='text' order='47'>У. Бронирование</cms:editable>
    <cms:editable name='label_u_contacts' label='У. Контакты' group='group_u' type='text' order='48'>У. Контакты</cms:editable>
    <cms:editable name='label_u_filial' label='У. Филиал' group='group_u' type='text' order='49'>У. Филиал</cms:editable>
    <cms:editable name='label_u_globals' label='У. Настройки сайта (Футер и SEO)' group='group_u' type='text' order='50'>У. Настройки сайта (Футер и SEO)</cms:editable>
    <cms:editable name='label_u_menu_text' label='У. Меню текст' group='group_u' type='text' order='51'>У. Меню текст</cms:editable>
    <cms:editable name='label_u_menu_visual' label='У. Меню визуальное' group='group_u' type='text' order='52'>У. Меню визуальное</cms:editable>
    <cms:editable name='label_u_menu_en' label='У. Меню EN' group='group_u' type='text' order='53'>У. Меню EN</cms:editable>
</cms:template>
