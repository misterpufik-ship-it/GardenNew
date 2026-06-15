<?php
define('K_TEMPLATE_NAME', 'udelnaya/about.php');
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
<cms:template title='Концепция' name='philosophy_section' executable='0' order='110'>
    
    <cms:editable name='phil_group' label='Контент блока' type='group' order='1' />
        <cms:editable name='phil_title' label='Заголовок (H1/H2)' group='phil_group' type='text'>Philosophy</cms:editable>
        <cms:editable name='phil_concept' label='Текст надписи Концепция' group='phil_group' type='text'>Концепция</cms:editable>
        
        <cms:editable name='phil_content' label='Основной текст описания' group='phil_group' type='richtext'>
            Магический вечнозеленый сад, скрытый от городской суеты в самом сердце Петербурга. 
            <br><br>
            Здесь время замедляет свой ход. Роскошный интерьер, утопающий в живых тропиках, мелодичный шум фонтана и уютное тепло камина создают атмосферу абсолютной гармонии и уединения.
        </cms:editable>
        
        <cms:editable name='phil_slogan' label='Слоган (внизу)' group='phil_group' type='textarea'>Garden Lounge — место, где рождаются ритуалы, достойные ваших воспоминаний</cms:editable>

    <cms:editable name='phil_seo_group' label='SEO и Заточка под ИИ' type='group' order='2' />
        <cms:editable name='phil_h1_logic' label='Тип заголовка' group='phil_seo_group' type='dropdown' opt_values='Сделать главным (H1)=h1 | Сделать обычным (H2)=h2' default='h2' desc='H1 должен быть только один на странице!' />
        
        <cms:editable name='phil_img_alt' label='Alt-текст для разделителя' group='phil_seo_group' type='text'>Эстетичный лаундж бар Санкт-Петербург</cms:editable>
        
        <cms:editable name='phil_lsi' label='LSI Ключи для ИИ (скрытые)' group='phil_seo_group' type='textarea' desc='Пропишите здесь через запятую важные ключи, которые ИИ должен связать с этим текстом (например: дизайнерская кальянная, центр спб, живые растения)'>дизайнерская кальянная СПб, премиум лаундж в центре, место с живыми растениями и фонтаном</cms:editable>

    <cms:editable name='phil_img_group' label='Изображения' type='group' order='3' />
        <cms:editable name='phil_sep' label='Картинка разделителя (узор)' group='phil_img_group' type='image'>:div.webp</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>


