<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Гамбургер-меню' executable='0' order='9'>

    <cms:editable name='mm_info' label='Справка' type='message' order='1'>
        Только мобильное гамбургер-меню (экраны уже 768px). Десктоп не затрагивается. Отступы задаются как минимум (px) + доля экрана (vh) + максимум (px).
    </cms:editable>

    <cms:editable name='group_mm_adm' label='Адмиралтейская' type='group' collapsed='1' order='10' />

    <cms:editable name='mm_adm_preset' label='Пресет отступов' group='group_mm_adm' type='dropdown' opt_values='standard=Стандарт|compact=Компакт|spacious=Просторно' order='11'>standard</cms:editable>

    <cms:editable name='mm_adm_shell_top_min' label='Отступ сверху до лого — мин (px)' group='group_mm_adm' type='text' order='12'>28</cms:editable>
    <cms:editable name='mm_adm_shell_top_vh' label='Отступ сверху до лого — vh' group='group_mm_adm' type='text' order='13'>5</cms:editable>
    <cms:editable name='mm_adm_shell_top_max' label='Отступ сверху до лого — макс (px)' group='group_mm_adm' type='text' order='14'>44</cms:editable>

    <cms:editable name='mm_adm_logo_gap_min' label='Лого → пункты меню — мин (px)' group='group_mm_adm' type='text' order='15'>8</cms:editable>
    <cms:editable name='mm_adm_logo_gap_vh' label='Лого → пункты меню — vh' group='group_mm_adm' type='text' order='16'>1.2</cms:editable>
    <cms:editable name='mm_adm_logo_gap_max' label='Лого → пункты меню — макс (px)' group='group_mm_adm' type='text' order='17'>14</cms:editable>

    <cms:editable name='mm_adm_menu_contact_min' label='Меню → контакты — мин (px)' group='group_mm_adm' type='text' order='18'>10</cms:editable>
    <cms:editable name='mm_adm_menu_contact_vh' label='Меню → контакты — vh' group='group_mm_adm' type='text' order='19'>2</cms:editable>
    <cms:editable name='mm_adm_menu_contact_max' label='Меню → контакты — макс (px)' group='group_mm_adm' type='text' order='20'>20</cms:editable>

    <cms:editable name='mm_adm_contact_pad_min' label='Место перед контактами — мин (px)' group='group_mm_adm' type='text' order='21'>28</cms:editable>
    <cms:editable name='mm_adm_contact_pad_vh' label='Место перед контактами — vh' group='group_mm_adm' type='text' order='22'>4.5</cms:editable>
    <cms:editable name='mm_adm_contact_pad_max' label='Место перед контактами — макс (px)' group='group_mm_adm' type='text' order='23'>42</cms:editable>

    <cms:editable name='mm_adm_contact_push_min' label='Опустить контакты — мин (px)' group='group_mm_adm' type='text' order='24'>4</cms:editable>
    <cms:editable name='mm_adm_contact_push_vh' label='Опустить контакты — vh' group='group_mm_adm' type='text' order='25'>1</cms:editable>
    <cms:editable name='mm_adm_contact_push_max' label='Опустить контакты — макс (px)' group='group_mm_adm' type='text' order='26'>12</cms:editable>

    <cms:editable name='mm_adm_social_gap_min' label='Соцсети — мин (px)' group='group_mm_adm' type='text' order='27'>2</cms:editable>
    <cms:editable name='mm_adm_social_gap_mid' label='Соцсети — значение (px)' group='group_mm_adm' type='text' order='28'>4</cms:editable>
    <cms:editable name='mm_adm_social_gap_max' label='Соцсети — макс (px)' group='group_mm_adm' type='text' order='29'>6</cms:editable>

    <cms:editable name='mm_adm_branch_label' label='Пункт «Второй филиал»' group='group_mm_adm' type='text' order='30'>ВТОРОЙ ФИЛИАЛ</cms:editable>

    <cms:editable name='group_mm_udel' label='Удельная' type='group' collapsed='1' order='40' />

    <cms:editable name='mm_udel_preset' label='Пресет отступов' group='group_mm_udel' type='dropdown' opt_values='standard=Стандарт|compact=Компакт|spacious=Просторно' order='41'>standard</cms:editable>

    <cms:editable name='mm_udel_shell_top_min' label='Отступ сверху до лого — мин (px)' group='group_mm_udel' type='text' order='42'>28</cms:editable>
    <cms:editable name='mm_udel_shell_top_vh' label='Отступ сверху до лого — vh' group='group_mm_udel' type='text' order='43'>5</cms:editable>
    <cms:editable name='mm_udel_shell_top_max' label='Отступ сверху до лого — макс (px)' group='group_mm_udel' type='text' order='44'>44</cms:editable>

    <cms:editable name='mm_udel_logo_gap_min' label='Лого → пункты меню — мин (px)' group='group_mm_udel' type='text' order='45'>8</cms:editable>
    <cms:editable name='mm_udel_logo_gap_vh' label='Лого → пункты меню — vh' group='group_mm_udel' type='text' order='46'>1.2</cms:editable>
    <cms:editable name='mm_udel_logo_gap_max' label='Лого → пункты меню — макс (px)' group='group_mm_udel' type='text' order='47'>14</cms:editable>

    <cms:editable name='mm_udel_menu_contact_min' label='Меню → контакты — мин (px)' group='group_mm_udel' type='text' order='48'>10</cms:editable>
    <cms:editable name='mm_udel_menu_contact_vh' label='Меню → контакты — vh' group='group_mm_udel' type='text' order='49'>2</cms:editable>
    <cms:editable name='mm_udel_menu_contact_max' label='Меню → контакты — макс (px)' group='group_mm_udel' type='text' order='50'>20</cms:editable>

    <cms:editable name='mm_udel_contact_pad_min' label='Место перед контактами — мин (px)' group='group_mm_udel' type='text' order='51'>28</cms:editable>
    <cms:editable name='mm_udel_contact_pad_vh' label='Место перед контактами — vh' group='group_mm_udel' type='text' order='52'>4.5</cms:editable>
    <cms:editable name='mm_udel_contact_pad_max' label='Место перед контактами — макс (px)' group='group_mm_udel' type='text' order='53'>42</cms:editable>

    <cms:editable name='mm_udel_contact_push_min' label='Опустить контакты — мин (px)' group='group_mm_udel' type='text' order='54'>4</cms:editable>
    <cms:editable name='mm_udel_contact_push_vh' label='Опустить контакты — vh' group='group_mm_udel' type='text' order='55'>1</cms:editable>
    <cms:editable name='mm_udel_contact_push_max' label='Опустить контакты — макс (px)' group='group_mm_udel' type='text' order='56'>12</cms:editable>

    <cms:editable name='mm_udel_social_gap_min' label='Соцсети — мин (px)' group='group_mm_udel' type='text' order='57'>2</cms:editable>
    <cms:editable name='mm_udel_social_gap_mid' label='Соцсети — значение (px)' group='group_mm_udel' type='text' order='58'>4</cms:editable>
    <cms:editable name='mm_udel_social_gap_max' label='Соцсети — макс (px)' group='group_mm_udel' type='text' order='59'>6</cms:editable>

    <cms:editable name='mm_udel_branch_label' label='Пункт «Второй филиал»' group='group_mm_udel' type='text' order='60'>ВТОРОЙ ФИЛИАЛ</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
