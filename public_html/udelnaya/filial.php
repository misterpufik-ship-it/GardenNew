<?php
define('K_TEMPLATE_NAME', 'udelnaya/filial.php');
$garden_cms = null;
foreach ([
    __DIR__ . '/admiralteyskaya/couch/cms.php',
    __DIR__ . '/../admiralteyskaya/couch/cms.php',
    __DIR__ . '/../../admiralteyskaya/couch/cms.php',
    __DIR__ . '/../../../admiralteyskaya/couch/cms.php',
    __DIR__ . '/../../../../admiralteyskaya/couch/cms.php',
    __DIR__ . '/../couch/cms.php',
    __DIR__ . '/../../couch/cms.php',
    __DIR__ . '/../../../couch/cms.php',
    __DIR__ . '/../../../../couch/cms.php',
] as $candidate) {
    if (file_exists($candidate)) {
        $garden_cms = $candidate;
        break;
    }
}
if (!$garden_cms) {
    die('Garden Lounge CMS bootstrap not found');
}
require_once $garden_cms;
?>
<cms:template title='Уделка Филиал' name='final_section' executable='0' order='70'>

    <cms:editable name='final_group_info' label='Информация о филиале' type='group' />
    <cms:editable name='final_title' label='Название филиала' group='final_group_info' type='text'>Garden Lounge Admiralteyskaya</cms:editable>
    <cms:editable name='final_subtitle' label='Подзаголовок' group='final_group_info' type='text'>Второй филиал тайного сада</cms:editable>
    <cms:editable name='final_address' label='Адрес' group='final_group_info' type='text'>СПб., наб. реки Мойки, д. 67-69</cms:editable>
    <cms:editable name='final_metro' label='Метро' group='final_group_info' type='text'>м. Адмиралтейская</cms:editable>

    <cms:editable name='final_group_gallery' label='Фотогалерея' type='group' />
    <cms:repeatable name='final_gallery_items' label='Фото галереи' group='final_group_gallery'>
        <cms:editable name='final_gallery_img' label='Фото' type='image' />
        <cms:editable name='final_gallery_title' label='Подпись' type='text' />
        <cms:editable name='final_gallery_alt' label='Alt / SEO' type='text' />
        <cms:editable name='final_gallery_category' label='Вкладка'
            opt_values='Interior=interior | Menu=menu | Vibe=vibe'
            type='dropdown' />
    </cms:repeatable>

    <cms:editable name='final_group_btn' label='Кнопка действия' type='group' />
    <cms:editable name='final_btn_text' label='Текст кнопки' group='final_group_btn' type='text'>Перейти на сайт</cms:editable>
    <cms:editable name='final_btn_link' label='Ссылка кнопки' group='final_group_btn' type='text'>https://garden-lounge.pro/admiralteyskaya/</cms:editable>

    <cms:editable name='final_sep' label='Картинка разделителя' type='image'>:div.webp</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>

