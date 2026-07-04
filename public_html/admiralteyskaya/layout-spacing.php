<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Отступы между блоками' executable='0' order='6'>

    <cms:editable name='spacing_info' label='Справка' type='message' order='1'>
        Отступ — расстояние сверху и снизу каждого блока на страницах филиалов (Адмиралтейская и Удельная).
        Между двумя соседними блоками визуально будет примерно сумма нижнего отступа первого и верхнего отступа второго.
        Включите галочку «Применить ко всем блокам», чтобы задать одно значение сразу для всех разделов.
    </cms:editable>

    <cms:editable name='spacing_sync_all' label='Применить ко всем блокам' type='checkbox' opt_values='Да=1' order='2'>1</cms:editable>
    <cms:editable name='spacing_all_desk' label='Общий отступ — десктоп (px)' type='text' order='3'>20</cms:editable>
    <cms:editable name='spacing_all_mob' label='Общий отступ — мобильный (px)' type='text' order='4'>14</cms:editable>

    <cms:editable name='spacing_philosophy_desk' label='Концепция — десктоп (px)' type='text' not_active='spacing_sync_all=1' order='10'>20</cms:editable>
    <cms:editable name='spacing_philosophy_mob' label='Концепция — мобильный (px)' type='text' not_active='spacing_sync_all=1' order='11'>14</cms:editable>
    <cms:editable name='spacing_experience_desk' label='Experience (Интерьер) — десктоп (px)' type='text' not_active='spacing_sync_all=1' order='12'>20</cms:editable>
    <cms:editable name='spacing_experience_mob' label='Experience (Интерьер) — мобильный (px)' type='text' not_active='spacing_sync_all=1' order='13'>14</cms:editable>
    <cms:editable name='spacing_menu_desk' label='Меню — десктоп (px)' type='text' not_active='spacing_sync_all=1' order='14'>20</cms:editable>
    <cms:editable name='spacing_menu_mob' label='Меню — мобильный (px)' type='text' not_active='spacing_sync_all=1' order='15'>14</cms:editable>
    <cms:editable name='spacing_akzii_desk' label='Акции — десктоп (px)' type='text' not_active='spacing_sync_all=1' order='16'>20</cms:editable>
    <cms:editable name='spacing_akzii_mob' label='Акции — мобильный (px)' type='text' not_active='spacing_sync_all=1' order='17'>14</cms:editable>
    <cms:editable name='spacing_reservation_desk' label='Бронирование — десктоп (px)' type='text' not_active='spacing_sync_all=1' order='18'>20</cms:editable>
    <cms:editable name='spacing_reservation_mob' label='Бронирование — мобильный (px)' type='text' not_active='spacing_sync_all=1' order='19'>14</cms:editable>
    <cms:editable name='spacing_contacts_desk' label='Контакты — десктоп (px)' type='text' not_active='spacing_sync_all=1' order='20'>20</cms:editable>
    <cms:editable name='spacing_contacts_mob' label='Контакты — мобильный (px)' type='text' not_active='spacing_sync_all=1' order='21'>14</cms:editable>
    <cms:editable name='spacing_filial_desk' label='Филиал — десктоп (px)' type='text' not_active='spacing_sync_all=1' order='22'>20</cms:editable>
    <cms:editable name='spacing_filial_mob' label='Филиал — мобильный (px)' type='text' not_active='spacing_sync_all=1' order='23'>14</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
