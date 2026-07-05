<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Гамбургер-меню' executable='0' order='9'>

    <cms:editable name='mm_info' label='Справка' type='message' order='1'>
        Настройки мобильного гамбургер-меню для каждого филиала: отступы, подпись «Второй филиал», расстояние до блока контактов и соцсетей.
    </cms:editable>

    <cms:editable name='group_mm_adm' label='Адмиралтейская' type='group' collapsed='1' order='10' />

    <cms:editable name='mm_adm_shell_pad_top' label='Отступ сверху до лого (px)' group='group_mm_adm' type='text' order='11'>32</cms:editable>
    <cms:editable name='mm_adm_logo_menu_gap' label='Отступ лого → пункты меню (px)' group='group_mm_adm' type='text' order='12'>12</cms:editable>
    <cms:editable name='mm_adm_menu_contact_gap' label='Отступ меню → блок контактов (px)' group='group_mm_adm' type='text' order='13'>16</cms:editable>
    <cms:editable name='mm_adm_contact_pad_top' label='Пустое место вместо виньетки (px)' group='group_mm_adm' type='text' order='14'>36</cms:editable>
    <cms:editable name='mm_adm_contact_push' label='Опустить контакты ниже (px)' group='group_mm_adm' type='text' order='15'>8</cms:editable>
    <cms:editable name='mm_adm_social_gap' label='Расстояние между иконками соцсетей (px)' group='group_mm_adm' type='text' order='16'>4</cms:editable>
    <cms:editable name='mm_adm_branch_label' label='Пункт «Второй филиал»' group='group_mm_adm' type='text' order='17'>ВТОРОЙ ФИЛИАЛ</cms:editable>

    <cms:editable name='group_mm_udel' label='Удельная' type='group' collapsed='1' order='20' />

    <cms:editable name='mm_udel_shell_pad_top' label='Отступ сверху до лого (px)' group='group_mm_udel' type='text' order='21'>32</cms:editable>
    <cms:editable name='mm_udel_logo_menu_gap' label='Отступ лого → пункты меню (px)' group='group_mm_udel' type='text' order='22'>12</cms:editable>
    <cms:editable name='mm_udel_menu_contact_gap' label='Отступ меню → блок контактов (px)' group='group_mm_udel' type='text' order='23'>16</cms:editable>
    <cms:editable name='mm_udel_contact_pad_top' label='Пустое место вместо виньетки (px)' group='group_mm_udel' type='text' order='24'>36</cms:editable>
    <cms:editable name='mm_udel_contact_push' label='Опустить контакты ниже (px)' group='group_mm_udel' type='text' order='25'>8</cms:editable>
    <cms:editable name='mm_udel_social_gap' label='Расстояние между иконками соцсетей (px)' group='group_mm_udel' type='text' order='26'>4</cms:editable>
    <cms:editable name='mm_udel_branch_label' label='Пункт «Второй филиал»' group='group_mm_udel' type='text' order='27'>ВТОРОЙ ФИЛИАЛ</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
