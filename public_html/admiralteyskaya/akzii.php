<?php require_once( 'couch/cms.php' ); ?>

<cms:template title='Акции' name='akzii_section' executable='0' order='120'>

    <cms:editable name='translation_script' type='message' order='0'>
        <cms:embed 'auto-translate-admin.html' />
    </cms:editable>

    <cms:editable name='group_titles' label='Заголовки секции' type='group' />

    <cms:editable name='akzii_main_title'
        label='Главный заголовок RU'
        group='group_titles'
        type='text'>Our Privileges</cms:editable>

    <cms:editable name='akzii_main_title_en'
        label='Главный заголовок EN (English menu)'
        group='group_titles'
        type='text'>Our Privileges</cms:editable>

    <cms:editable name='akzii_sub_title'
        label='Подзаголовок RU'
        group='group_titles'
        type='text'>Специальные предложения</cms:editable>

    <cms:editable name='akzii_sub_title_en'
        label='Подзаголовок EN (English menu)'
        group='group_titles'
        type='text'>Special Offers</cms:editable>

    <cms:editable name='group_promo' label='Список предложений' type='group' />

    <cms:repeatable name='promo_list' label='Таблица акций' group='group_promo'>
        <cms:editable name='promo_name' label='Название RU' type='text' />
        <cms:editable name='promo_name_en' label='Название EN' type='text' />
        <cms:editable name='promo_desc' label='Описание RU' type='textarea' />
        <cms:editable name='promo_desc_en' label='Описание EN' type='textarea' />
        <cms:editable name='promo_offer' label='Условие RU (золотой текст)' type='text' />
        <cms:editable name='promo_offer_en' label='Условие EN' type='text' />
    </cms:repeatable>

    <cms:editable name='group_footer' label='Нижняя часть и декор' type='group' />

    <cms:editable name='akzii_footer'
        label='Финальная фраза RU'
        group='group_footer'
        type='text'>Идеальное место для ценителей прекрасного.</cms:editable>

    <cms:editable name='akzii_footer_en'
        label='Финальная фраза EN (English menu)'
        group='group_footer'
        type='text'>The perfect place for connoisseurs.</cms:editable>

    <cms:editable name='akzii_sep'
        label='Картинка разделителя (узор)'
        group='group_footer'
        type='image'>:div.webp</cms:editable>

</cms:template>

<?php COUCH::invoke(); ?>
