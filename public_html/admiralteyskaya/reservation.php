<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Адмирал Бронирование' name='reservation_section' executable='0' order='50'>
    
    <cms:editable name='res_group_titles' label='Заголовки секции' type='group' />
    <cms:editable name='res_title' label='Главный заголовок' group='res_group_titles' type='text'>Reservation</cms:editable>
    <cms:editable name='res_subtitle' label='Подзаголовок' group='res_group_titles' type='text'>Забронировать столик</cms:editable>

    <cms:editable name='res_group_modal' label='Тексты после отправки' type='group' />
    <cms:editable name='res_modal_title' label='Заголовок модального окна' group='res_group_modal' type='text'>Спасибо!</cms:editable>
    <cms:editable name='res_modal_text' label='Текст подтверждения' group='res_group_modal' type='textarea'>Ваше бронирование принято. Администратор свяжется с Вами для подтверждения.</cms:editable>

    <cms:editable name='res_group_decor' label='Декор' type='group' />
    <cms:editable name='res_sep' label='Картинка разделителя' group='res_group_decor' type='image'>:div.webp</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
