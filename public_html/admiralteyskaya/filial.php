<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Управление: Филиал' name='final_section' executable='0' order='70'>

    <cms:editable name='final_group_info' label='Информация о филиале' type='group' />
    <cms:editable name='final_title' label='Название филиала' group='final_group_info' type='text'>Garden Lounge Udelnaya</cms:editable>
    <cms:editable name='final_subtitle' label='Подзаголовок' group='final_group_info' type='text'>Второй филиал тайного сада</cms:editable>
    <cms:editable name='final_img' label='Обложка филиала' group='final_group_info' type='image'>https://garden-lounge.pro/img/akkuratova.webp</cms:editable>

    <cms:repeatable name='final_gallery' label='Галерея филиала' group='final_group_info'>
        <cms:editable name='final_gallery_img' label='Фото' type='image' />
        <cms:editable name='final_gallery_alt' label='Подпись / alt' type='text' />
    </cms:repeatable>

    <cms:editable name='final_address' label='Адрес' group='final_group_info' type='text'>СПб., ул. Аккуратова, д. 13</cms:editable>
    <cms:editable name='final_metro' label='Метро' group='final_group_info' type='text'>м. Удельная</cms:editable>

    <cms:editable name='final_group_btn' label='Кнопка действия' type='group' />
    <cms:editable name='final_btn_text' label='Текст кнопки' group='final_group_btn' type='text'>Перейти на сайт</cms:editable>
    <cms:editable name='final_btn_link' label='Ссылка кнопки' group='final_group_btn' type='text'>https://garden-lounge.pro</cms:editable>

    <cms:editable name='final_sep' label='Картинка разделителя' type='image'>https://misterpufik.ru/div.png</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
