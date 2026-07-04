<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Прокрутка по меню' executable='0' order='7'>

    <cms:editable name='scroll_info' label='Справка' type='message' order='1'>
        Отступ прокрутки — расстояние от верха экрана до заголовка раздела при клике по пунктам меню (шапка, мобильное меню, футер).
        Если после клика виден конец предыдущего блока — уменьшите значение. Если заголовок уходит под шапку — увеличьте.
        Для каждого филиала можно задать свои значения. Включите «Применить ко всем пунктам», чтобы задать одно значение сразу для всех разделов.
    </cms:editable>

    <cms:editable name='group_scroll_adm' label='Адмиралтейская' type='group' collapsed='1' order='10' />

    <cms:editable name='scroll_adm_sync_all' label='Применить ко всем пунктам' group='group_scroll_adm' type='checkbox' opt_values='Да=1' order='11'>1</cms:editable>
    <cms:editable name='scroll_adm_all_desk' label='Общий отступ — десктоп (px)' group='group_scroll_adm' type='text' order='12'>72</cms:editable>
    <cms:editable name='scroll_adm_all_mob' label='Общий отступ — мобильный (px)' group='group_scroll_adm' type='text' order='13'>64</cms:editable>

    <cms:editable name='scroll_adm_philosophy_desk' label='Концепция (#about-us) — десктоп (px)' group='group_scroll_adm' type='text' not_active='scroll_adm_sync_all=1' order='20'>72</cms:editable>
    <cms:editable name='scroll_adm_philosophy_mob' label='Концепция (#about-us) — мобильный (px)' group='group_scroll_adm' type='text' not_active='scroll_adm_sync_all=1' order='21'>64</cms:editable>
    <cms:editable name='scroll_adm_experience_desk' label='Интерьер (#photo) — десктоп (px)' group='group_scroll_adm' type='text' not_active='scroll_adm_sync_all=1' order='22'>72</cms:editable>
    <cms:editable name='scroll_adm_experience_mob' label='Интерьер (#photo) — мобильный (px)' group='group_scroll_adm' type='text' not_active='scroll_adm_sync_all=1' order='23'>64</cms:editable>
    <cms:editable name='scroll_adm_menu_desk' label='Меню (#menu-block) — десктоп (px)' group='group_scroll_adm' type='text' not_active='scroll_adm_sync_all=1' order='24'>72</cms:editable>
    <cms:editable name='scroll_adm_menu_mob' label='Меню (#menu-block) — мобильный (px)' group='group_scroll_adm' type='text' not_active='scroll_adm_sync_all=1' order='25'>64</cms:editable>
    <cms:editable name='scroll_adm_akzii_desk' label='Акции (#special) — десктоп (px)' group='group_scroll_adm' type='text' not_active='scroll_adm_sync_all=1' order='26'>72</cms:editable>
    <cms:editable name='scroll_adm_akzii_mob' label='Акции (#special) — мобильный (px)' group='group_scroll_adm' type='text' not_active='scroll_adm_sync_all=1' order='27'>64</cms:editable>
    <cms:editable name='scroll_adm_reservation_desk' label='Бронирование (#reservation) — десктоп (px)' group='group_scroll_adm' type='text' not_active='scroll_adm_sync_all=1' order='28'>72</cms:editable>
    <cms:editable name='scroll_adm_reservation_mob' label='Бронирование (#reservation) — мобильный (px)' group='group_scroll_adm' type='text' not_active='scroll_adm_sync_all=1' order='29'>64</cms:editable>
    <cms:editable name='scroll_adm_contacts_desk' label='Контакты (#contact) — десктоп (px)' group='group_scroll_adm' type='text' not_active='scroll_adm_sync_all=1' order='30'>72</cms:editable>
    <cms:editable name='scroll_adm_contacts_mob' label='Контакты (#contact) — мобильный (px)' group='group_scroll_adm' type='text' not_active='scroll_adm_sync_all=1' order='31'>64</cms:editable>
    <cms:editable name='scroll_adm_filial_desk' label='Филиал (#branch-filial) — десктоп (px)' group='group_scroll_adm' type='text' not_active='scroll_adm_sync_all=1' order='32'>72</cms:editable>
    <cms:editable name='scroll_adm_filial_mob' label='Филиал (#branch-filial) — мобильный (px)' group='group_scroll_adm' type='text' not_active='scroll_adm_sync_all=1' order='33'>64</cms:editable>

    <cms:editable name='group_scroll_udel' label='Удельная' type='group' collapsed='1' order='40' />

    <cms:editable name='scroll_udel_sync_all' label='Применить ко всем пунктам' group='group_scroll_udel' type='checkbox' opt_values='Да=1' order='41'>1</cms:editable>
    <cms:editable name='scroll_udel_all_desk' label='Общий отступ — десктоп (px)' group='group_scroll_udel' type='text' order='42'>72</cms:editable>
    <cms:editable name='scroll_udel_all_mob' label='Общий отступ — мобильный (px)' group='group_scroll_udel' type='text' order='43'>64</cms:editable>

    <cms:editable name='scroll_udel_philosophy_desk' label='Концепция (#about-us) — десктоп (px)' group='group_scroll_udel' type='text' not_active='scroll_udel_sync_all=1' order='50'>72</cms:editable>
    <cms:editable name='scroll_udel_philosophy_mob' label='Концепция (#about-us) — мобильный (px)' group='group_scroll_udel' type='text' not_active='scroll_udel_sync_all=1' order='51'>64</cms:editable>
    <cms:editable name='scroll_udel_experience_desk' label='Интерьер (#photo) — десктоп (px)' group='group_scroll_udel' type='text' not_active='scroll_udel_sync_all=1' order='52'>72</cms:editable>
    <cms:editable name='scroll_udel_experience_mob' label='Интерьер (#photo) — мобильный (px)' group='group_scroll_udel' type='text' not_active='scroll_udel_sync_all=1' order='53'>64</cms:editable>
    <cms:editable name='scroll_udel_menu_desk' label='Меню (#menu-block) — десктоп (px)' group='group_scroll_udel' type='text' not_active='scroll_udel_sync_all=1' order='54'>72</cms:editable>
    <cms:editable name='scroll_udel_menu_mob' label='Меню (#menu-block) — мобильный (px)' group='group_scroll_udel' type='text' not_active='scroll_udel_sync_all=1' order='55'>64</cms:editable>
    <cms:editable name='scroll_udel_akzii_desk' label='Акции (#special) — десктоп (px)' group='group_scroll_udel' type='text' not_active='scroll_udel_sync_all=1' order='56'>72</cms:editable>
    <cms:editable name='scroll_udel_akzii_mob' label='Акции (#special) — мобильный (px)' group='group_scroll_udel' type='text' not_active='scroll_udel_sync_all=1' order='57'>64</cms:editable>
    <cms:editable name='scroll_udel_reservation_desk' label='Бронирование (#reservation) — десктоп (px)' group='group_scroll_udel' type='text' not_active='scroll_udel_sync_all=1' order='58'>72</cms:editable>
    <cms:editable name='scroll_udel_reservation_mob' label='Бронирование (#reservation) — мобильный (px)' group='group_scroll_udel' type='text' not_active='scroll_udel_sync_all=1' order='59'>64</cms:editable>
    <cms:editable name='scroll_udel_contacts_desk' label='Контакты (#contact) — десктоп (px)' group='group_scroll_udel' type='text' not_active='scroll_udel_sync_all=1' order='60'>72</cms:editable>
    <cms:editable name='scroll_udel_contacts_mob' label='Контакты (#contact) — мобильный (px)' group='group_scroll_udel' type='text' not_active='scroll_udel_sync_all=1' order='61'>64</cms:editable>
    <cms:editable name='scroll_udel_filial_desk' label='Филиал (#branch-filial) — десктоп (px)' group='group_scroll_udel' type='text' not_active='scroll_udel_sync_all=1' order='62'>72</cms:editable>
    <cms:editable name='scroll_udel_filial_mob' label='Филиал (#branch-filial) — мобильный (px)' group='group_scroll_udel' type='text' not_active='scroll_udel_sync_all=1' order='63'>64</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
