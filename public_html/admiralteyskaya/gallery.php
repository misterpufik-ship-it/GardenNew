<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Галерея' name='gallery_section' executable='0' order='170'>
    
    <cms:editable name='gallery_main_title' label='Главный заголовок' type='text'>Experience</cms:editable>
    <cms:editable name='gallery_sub_title' label='Подзаголовок' type='text'>Визуальная эстетика</cms:editable>

    <cms:editable name='gallery_grp_interior' label='Interior — интерьер' type='group' collapsed='1' order='10' />
    <cms:repeatable name='gallery_interior_items' label='Фото интерьера' group='gallery_grp_interior'>
        <cms:editable name='gallery_img' label='Фото' type='image' />
        <cms:editable name='gallery_img_title' label='Подпись к фото' type='text' />
        <cms:editable name='gallery_img_alt' label='ALT для SEO' type='text' />
    </cms:repeatable>

    <cms:editable name='gallery_grp_menu' label='Kitchen — кухня' type='group' collapsed='1' order='20' />
    <cms:repeatable name='gallery_menu_items' label='Фото кухни' group='gallery_grp_menu'>
        <cms:editable name='gallery_img' label='Фото' type='image' />
        <cms:editable name='gallery_img_title' label='Подпись к фото' type='text' />
        <cms:editable name='gallery_img_alt' label='ALT для SEO' type='text' />
    </cms:repeatable>

    <cms:editable name='gallery_grp_vibe' label='Vibe — атмосфера' type='group' collapsed='1' order='30' />
    <cms:repeatable name='gallery_vibe_items' label='Фото атмосферы' group='gallery_grp_vibe'>
        <cms:editable name='gallery_img' label='Фото' type='image' />
        <cms:editable name='gallery_img_title' label='Подпись к фото' type='text' />
        <cms:editable name='gallery_img_alt' label='ALT для SEO' type='text' />
    </cms:repeatable>

    <cms:editable name='gallery_footer_text' label='Текст в самом низу' type='text'>Откройте мир уникальных локаций и гастрономического удовольствия</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
