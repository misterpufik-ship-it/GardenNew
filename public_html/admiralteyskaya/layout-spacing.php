<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Отступы между блоками' executable='0' order='6'>

    <cms:editable name='spacing_info' label='Справка' type='message' order='1'>
        Отступ — расстояние сверху и снизу каждого блока на странице филиала.
        Между двумя соседними блоками визуально будет примерно сумма нижнего отступа первого и верхнего отступа второго.
        Для каждого филиала можно задать свои значения. Включите «Применить ко всем блокам», чтобы задать одно значение сразу для всех разделов.
    </cms:editable>

    <cms:editable name='group_spacing_adm' label='Адмиралтейская' type='group' collapsed='1' order='10' />

    <cms:editable name='spacing_adm_sync_all' label='Применить ко всем блокам' group='group_spacing_adm' type='checkbox' opt_values='Да=1' order='11'>1</cms:editable>
    <cms:editable name='spacing_adm_all_desk' label='Общий отступ — десктоп (px)' group='group_spacing_adm' type='text' order='12'>20</cms:editable>
    <cms:editable name='spacing_adm_all_mob' label='Общий отступ — мобильный (px)' group='group_spacing_adm' type='text' order='13'>14</cms:editable>

    <cms:editable name='spacing_adm_philosophy_desk' label='Концепция — десктоп (px)' group='group_spacing_adm' type='text' not_active='spacing_adm_sync_all=1' order='20'>20</cms:editable>
    <cms:editable name='spacing_adm_philosophy_mob' label='Концепция — мобильный (px)' group='group_spacing_adm' type='text' not_active='spacing_adm_sync_all=1' order='21'>14</cms:editable>
    <cms:editable name='spacing_adm_experience_desk' label='Experience (Интерьер) — десктоп (px)' group='group_spacing_adm' type='text' not_active='spacing_adm_sync_all=1' order='22'>20</cms:editable>
    <cms:editable name='spacing_adm_experience_mob' label='Experience (Интерьер) — мобильный (px)' group='group_spacing_adm' type='text' not_active='spacing_adm_sync_all=1' order='23'>14</cms:editable>
    <cms:editable name='spacing_adm_menu_desk' label='Меню — десктоп (px)' group='group_spacing_adm' type='text' not_active='spacing_adm_sync_all=1' order='24'>20</cms:editable>
    <cms:editable name='spacing_adm_menu_mob' label='Меню — мобильный (px)' group='group_spacing_adm' type='text' not_active='spacing_adm_sync_all=1' order='25'>14</cms:editable>
    <cms:editable name='spacing_adm_akzii_desk' label='Акции — десктоп (px)' group='group_spacing_adm' type='text' not_active='spacing_adm_sync_all=1' order='26'>20</cms:editable>
    <cms:editable name='spacing_adm_akzii_mob' label='Акции — мобильный (px)' group='group_spacing_adm' type='text' not_active='spacing_adm_sync_all=1' order='27'>14</cms:editable>
    <cms:editable name='spacing_adm_reservation_desk' label='Бронирование — десктоп (px)' group='group_spacing_adm' type='text' not_active='spacing_adm_sync_all=1' order='28'>20</cms:editable>
    <cms:editable name='spacing_adm_reservation_mob' label='Бронирование — мобильный (px)' group='group_spacing_adm' type='text' not_active='spacing_adm_sync_all=1' order='29'>14</cms:editable>
    <cms:editable name='spacing_adm_contacts_desk' label='Контакты — десктоп (px)' group='group_spacing_adm' type='text' not_active='spacing_adm_sync_all=1' order='30'>20</cms:editable>
    <cms:editable name='spacing_adm_contacts_mob' label='Контакты — мобильный (px)' group='group_spacing_adm' type='text' not_active='spacing_adm_sync_all=1' order='31'>14</cms:editable>
    <cms:editable name='spacing_adm_filial_desk' label='Филиал — десктоп (px)' group='group_spacing_adm' type='text' not_active='spacing_adm_sync_all=1' order='32'>20</cms:editable>
    <cms:editable name='spacing_adm_filial_mob' label='Филиал — мобильный (px)' group='group_spacing_adm' type='text' not_active='spacing_adm_sync_all=1' order='33'>14</cms:editable>

    <cms:editable name='group_spacing_udel' label='Удельная' type='group' collapsed='1' order='40' />

    <cms:editable name='spacing_udel_sync_all' label='Применить ко всем блокам' group='group_spacing_udel' type='checkbox' opt_values='Да=1' order='41'>1</cms:editable>
    <cms:editable name='spacing_udel_all_desk' label='Общий отступ — десктоп (px)' group='group_spacing_udel' type='text' order='42'>20</cms:editable>
    <cms:editable name='spacing_udel_all_mob' label='Общий отступ — мобильный (px)' group='group_spacing_udel' type='text' order='43'>14</cms:editable>

    <cms:editable name='spacing_udel_philosophy_desk' label='Концепция — десктоп (px)' group='group_spacing_udel' type='text' not_active='spacing_udel_sync_all=1' order='50'>20</cms:editable>
    <cms:editable name='spacing_udel_philosophy_mob' label='Концепция — мобильный (px)' group='group_spacing_udel' type='text' not_active='spacing_udel_sync_all=1' order='51'>14</cms:editable>
    <cms:editable name='spacing_udel_experience_desk' label='Experience (Интерьер) — десктоп (px)' group='group_spacing_udel' type='text' not_active='spacing_udel_sync_all=1' order='52'>20</cms:editable>
    <cms:editable name='spacing_udel_experience_mob' label='Experience (Интерьер) — мобильный (px)' group='group_spacing_udel' type='text' not_active='spacing_udel_sync_all=1' order='53'>14</cms:editable>
    <cms:editable name='spacing_udel_menu_desk' label='Меню — десктоп (px)' group='group_spacing_udel' type='text' not_active='spacing_udel_sync_all=1' order='54'>20</cms:editable>
    <cms:editable name='spacing_udel_menu_mob' label='Меню — мобильный (px)' group='group_spacing_udel' type='text' not_active='spacing_udel_sync_all=1' order='55'>14</cms:editable>
    <cms:editable name='spacing_udel_akzii_desk' label='Акции — десктоп (px)' group='group_spacing_udel' type='text' not_active='spacing_udel_sync_all=1' order='56'>20</cms:editable>
    <cms:editable name='spacing_udel_akzii_mob' label='Акции — мобильный (px)' group='group_spacing_udel' type='text' not_active='spacing_udel_sync_all=1' order='57'>14</cms:editable>
    <cms:editable name='spacing_udel_reservation_desk' label='Бронирование — десктоп (px)' group='group_spacing_udel' type='text' not_active='spacing_udel_sync_all=1' order='58'>20</cms:editable>
    <cms:editable name='spacing_udel_reservation_mob' label='Бронирование — мобильный (px)' group='group_spacing_udel' type='text' not_active='spacing_udel_sync_all=1' order='59'>14</cms:editable>
    <cms:editable name='spacing_udel_contacts_desk' label='Контакты — десктоп (px)' group='group_spacing_udel' type='text' not_active='spacing_udel_sync_all=1' order='60'>20</cms:editable>
    <cms:editable name='spacing_udel_contacts_mob' label='Контакты — мобильный (px)' group='group_spacing_udel' type='text' not_active='spacing_udel_sync_all=1' order='61'>14</cms:editable>
    <cms:editable name='spacing_udel_filial_desk' label='Филиал — десктоп (px)' group='group_spacing_udel' type='text' not_active='spacing_udel_sync_all=1' order='62'>20</cms:editable>
    <cms:editable name='spacing_udel_filial_mob' label='Филиал — мобильный (px)' group='group_spacing_udel' type='text' not_active='spacing_udel_sync_all=1' order='63'>14</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
