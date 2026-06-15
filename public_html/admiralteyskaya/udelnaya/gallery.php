<?php
define('K_TEMPLATE_NAME', 'udelnaya/gallery.php');
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
<cms:template title='У. Галерея' name='gallery_section' executable='0' order='330'>
    
    <cms:editable name='gallery_main_title' label='Главный заголовок' type='text'>Experience</cms:editable>
    <cms:editable name='gallery_sub_title' label='Подзаголовок' type='text'>Визуальная эстетика</cms:editable>

    <cms:repeatable name='gallery_items' label='Фотографии галереи'>
        <cms:editable name='gallery_img' label='Фото' type='image' />
        <cms:editable name='gallery_img_title' label='Подпись к фото' type='text' />
        <cms:editable name='gallery_category' label='Категория (Вкладка)' 
            opt_values='Interior=interior | Menu=menu | Vibe=vibe' 
            type='dropdown' 
        />
    </cms:repeatable>

    <cms:editable name='gallery_footer_text' label='Текст в самом низу' type='text'>Откройте мир уникальных локаций и гастрономического удовольствия</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>

