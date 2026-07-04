<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Hero — мобильный лого' executable='0' order='8'>

    <cms:editable name='hero_info' label='Справка' type='message' order='1'>
        Настройки первого экрана на мобильных: смещение лого вниз и расстояние до кнопки «Забронировать».
        Чем больше «Смещение лого вниз», тем ниже лого на экране. «Отступ до кнопки» — расстояние между лого и кнопкой в пикселях.
    </cms:editable>

    <cms:editable name='group_hero_adm' label='Адмиралтейская' type='group' collapsed='1' order='10' />

    <cms:editable name='hero_adm_logo_down_mob' label='Смещение лого вниз — мобильный (px)' group='group_hero_adm' type='text' order='11'>18</cms:editable>
    <cms:editable name='hero_adm_logo_btn_gap_mob' label='Отступ лого до «Забронировать» — мобильный (px)' group='group_hero_adm' type='text' order='12'>8</cms:editable>

    <cms:editable name='group_hero_udel' label='Удельная' type='group' collapsed='1' order='20' />

    <cms:editable name='hero_udel_logo_down_mob' label='Смещение лого вниз — мобильный (px)' group='group_hero_udel' type='text' order='21'>18</cms:editable>
    <cms:editable name='hero_udel_logo_btn_gap_mob' label='Отступ лого до «Забронировать» — мобильный (px)' group='group_hero_udel' type='text' order='22'>8</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
