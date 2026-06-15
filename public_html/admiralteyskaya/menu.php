<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Меню (общие настройки)' name='menu_section' executable='0' order='130'>
    
    <cms:editable name='menu_group_titles' label='Заголовки' type='group' />
    <cms:editable name='menu_main_title' label='Главный заголовок' group='menu_group_titles' type='text'>Menu</cms:editable>
    <cms:editable name='menu_sub_title' label='Подзаголовок' group='menu_group_titles' type='text'>Эстетика вкуса</cms:editable>

    <cms:editable name='menu_group_visual' label='Визуальное меню (Левая часть)' type='group' />
    <cms:editable name='menu_visual_img' label='Обложка визуального меню' group='menu_group_visual' type='image'>:gf11.webp</cms:editable>
    <cms:editable name='menu_visual_link' label='Ссылка на визуальное меню' group='menu_group_visual' type='text'>https://garden-lounge.pro/admiralteyskaya/menu/visual/</cms:editable>

    <cms:editable name='menu_group_links' label='Кнопки справа' type='group' />
    <cms:editable name='menu_text_link' label='Ссылка: Текстовое меню' group='menu_group_links' type='text'>https://garden-lounge.pro/admiralteyskaya/menu/text/</cms:editable>
    <cms:editable name='menu_eng_link' label='Ссылка: English Menu' group='menu_group_links' type='text'>https://garden-lounge.pro/admiralteyskaya/menu/english/</cms:editable>

    <cms:editable name='menu_footer_text' label='Текст внизу' type='text'>Гастрономическая поэзия</cms:editable>
    <cms:editable name='menu_sep' label='Картинка разделителя' type='image'>:div.webp</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
