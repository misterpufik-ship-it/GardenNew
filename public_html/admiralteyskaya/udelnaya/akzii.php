<?php
define('K_TEMPLATE_NAME', 'udelnaya/akzii.php');
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

<cms:template title='Акции' name='akzii_section' executable='0' order='120'>
    
    <cms:editable name='group_titles' label='Заголовки секции' type='group' />
    
    <cms:editable name='akzii_main_title' 
        label='Главный заголовок' 
        group='group_titles' 
        type='text'>Our Privileges</cms:editable>
    
    <cms:editable name='akzii_sub_title' 
        label='Подзаголовок' 
        group='group_titles' 
        type='text'>Специальные предложения</cms:editable>

    <cms:editable name='group_promo' label='Список предложений' type='group' />
    
    <cms:repeatable name='promo_list' label='Таблица акций' group='group_promo'>
        <cms:editable name='promo_name' label='Название (например: Smoky Lunch)' type='text' />
        <cms:editable name='promo_desc' label='Описание (мелкий текст)' type='textarea' />
        <cms:editable name='promo_offer' label='Условие (Золотой текст)' type='text' />
    </cms:repeatable>

    <cms:editable name='group_footer' label='Нижняя часть и декор' type='group' />
    
    <cms:editable name='akzii_footer' 
        label='Финальная фраза' 
        group='group_footer' 
        type='text'>Идеальное место для ценителей прекрасного.</cms:editable>
    
    <cms:editable name='akzii_sep' 
        label='Картинка разделителя (узор)' 
        group='group_footer' 
        type='image'>:div.webp</cms:editable>

</cms:template>

<?php COUCH::invoke(); ?>


