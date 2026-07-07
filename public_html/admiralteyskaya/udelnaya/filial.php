<?php
if (!defined('K_TEMPLATE_NAME')) {
    define('K_TEMPLATE_NAME', 'udelnaya/filial.php');
}
require_once dirname(__DIR__) . '/couch/cms.php';
?>
<cms:template title='Уделка Филиал' name='final_section' executable='0' order='70'>

    <cms:editable name='final_group_info' label='Информация о филиале' type='group' />
    <cms:editable name='final_title' label='Название филиала' group='final_group_info' type='text'>Garden Lounge Admiralteyskaya</cms:editable>
    <cms:editable name='final_subtitle' label='Подзаголовок' group='final_group_info' type='text'>Второй филиал тайного сада</cms:editable>
    <cms:editable name='final_address' label='Адрес' group='final_group_info' type='text'>СПб., наб. реки Мойки, д. 67-69</cms:editable>
    <cms:editable name='final_metro' label='Метро' group='final_group_info' type='text'>м. Адмиралтейская</cms:editable>

    <cms:editable name='final_group_gallery' label='Фотогалерея (авто)' type='group' />
    <cms:editable name='final_gallery_note' label='Источник фото' group='final_group_gallery' type='textarea' hidden='1'>Слайдер подтягивается автоматически из раздела «Галерея» филиала Адмиралтейская. Редактируйте фото там.</cms:editable>

    <cms:editable name='final_group_btn' label='Кнопка действия' type='group' />
    <cms:editable name='final_btn_text' label='Текст кнопки' group='final_group_btn' type='text'>Перейти на сайт</cms:editable>
    <cms:editable name='final_btn_link' label='Ссылка кнопки' group='final_group_btn' type='text'>https://garden-lounge.pro/admiralteyskaya/</cms:editable>

    <cms:editable name='final_sep' label='Картинка разделителя' type='image'>:div.webp</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>

