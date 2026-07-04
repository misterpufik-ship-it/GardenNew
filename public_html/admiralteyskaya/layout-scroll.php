<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Прокрутка по меню' executable='0' order='7'>

    <cms:editable name='scroll_info' label='Справка' type='message' order='1'>
        Отступ прокрутки — расстояние от верха экрана до заголовка раздела при клике по пунктам меню (шапка, мобильное меню, футер).
        Если после клика виден конец предыдущего блока — уменьшите значение. Если заголовок уходит под шапку — увеличьте.
        Включите «Применить ко всем пунктам», чтобы задать одно значение сразу для всех разделов.
    </cms:editable>

    <cms:editable name='scroll_sync_all' label='Применить ко всем пунктам' type='checkbox' opt_values='Да=1' order='2'>1</cms:editable>
    <cms:editable name='scroll_all_desk' label='Общий отступ — десктоп (px)' type='text' order='3'>72</cms:editable>
    <cms:editable name='scroll_all_mob' label='Общий отступ — мобильный (px)' type='text' order='4'>64</cms:editable>

    <cms:editable name='scroll_philosophy_desk' label='Концепция (#about-us) — десктоп (px)' type='text' not_active='scroll_sync_all=1' order='10'>72</cms:editable>
    <cms:editable name='scroll_philosophy_mob' label='Концепция (#about-us) — мобильный (px)' type='text' not_active='scroll_sync_all=1' order='11'>64</cms:editable>
    <cms:editable name='scroll_experience_desk' label='Интерьер (#photo) — десктоп (px)' type='text' not_active='scroll_sync_all=1' order='12'>72</cms:editable>
    <cms:editable name='scroll_experience_mob' label='Интерьер (#photo) — мобильный (px)' type='text' not_active='scroll_sync_all=1' order='13'>64</cms:editable>
    <cms:editable name='scroll_menu_desk' label='Меню (#menu-block) — десктоп (px)' type='text' not_active='scroll_sync_all=1' order='14'>72</cms:editable>
    <cms:editable name='scroll_menu_mob' label='Меню (#menu-block) — мобильный (px)' type='text' not_active='scroll_sync_all=1' order='15'>64</cms:editable>
    <cms:editable name='scroll_akzii_desk' label='Акции (#special) — десктоп (px)' type='text' not_active='scroll_sync_all=1' order='16'>72</cms:editable>
    <cms:editable name='scroll_akzii_mob' label='Акции (#special) — мобильный (px)' type='text' not_active='scroll_sync_all=1' order='17'>64</cms:editable>
    <cms:editable name='scroll_reservation_desk' label='Бронирование (#reservation) — десктоп (px)' type='text' not_active='scroll_sync_all=1' order='18'>72</cms:editable>
    <cms:editable name='scroll_reservation_mob' label='Бронирование (#reservation) — мобильный (px)' type='text' not_active='scroll_sync_all=1' order='19'>64</cms:editable>
    <cms:editable name='scroll_contacts_desk' label='Контакты (#contact) — десктоп (px)' type='text' not_active='scroll_sync_all=1' order='20'>72</cms:editable>
    <cms:editable name='scroll_contacts_mob' label='Контакты (#contact) — мобильный (px)' type='text' not_active='scroll_sync_all=1' order='21'>64</cms:editable>
    <cms:editable name='scroll_filial_desk' label='Филиал (#branch-filial) — десктоп (px)' type='text' not_active='scroll_sync_all=1' order='22'>72</cms:editable>
    <cms:editable name='scroll_filial_mob' label='Филиал (#branch-filial) — мобильный (px)' type='text' not_active='scroll_sync_all=1' order='23'>64</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
