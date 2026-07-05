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
<cms:template title='Уделка Философия' name='philosophy_section' executable='0' order='20'>
    
    <cms:editable name='phil_group' label='Контент блока' type='group' order='1' />
        <cms:editable name='phil_title' label='Заголовок (H1/H2)' group='phil_group' type='text'>Philosophy</cms:editable>
        <cms:editable name='phil_concept' label='Текст надписи Концепция' group='phil_group' type='text'>Концепция</cms:editable>

        <cms:editable name='phil_seo_h1' label='SEO-заголовок (H1)' group='phil_group' type='text'>Garden Lounge — кальянная и лаунж-бар в Приморском районе Санкт-Петербурга</cms:editable>

        <cms:editable name='phil_content' label='Основной текст описания' group='phil_group' type='richtext'>
            <p>На ул. Аккуратова, у метро Удельная, спрятан вечнозелёный сад — эстетичная кальянная, где городская суета остаётся за дверью. Живые тропики, фонтан и камин создают пространство, в котором время замедляется само.</p>
            <p>Премиальная кальянная с изысканной кухней в СПб: авторские кальяны, бар, VIP-комнаты и вечера с PS5. Уютная атмосфера третьего места.</p>
        </cms:editable>

        <cms:editable name='phil_slogan' label='Слоган (внизу)' group='phil_group' type='textarea'>Garden Lounge — место, где рождаются ритуалы, достойные ваших воспоминаний</cms:editable>

    <cms:editable name='phil_seo_group' label='SEO и Заточка под ИИ' type='group' order='2' />
        <cms:editable name='phil_h1_logic' label='Тип заголовка' group='phil_seo_group' type='dropdown' opt_values='Сделать главным (H1)=h1 | Сделать обычным (H2)=h2' default='h2' desc='H1 должен быть только один на странице!' />

        <cms:editable name='phil_img_alt' label='Alt-текст для разделителя' group='phil_seo_group' type='text'>Эстетичный лаунж-бар в Приморском районе, Санкт-Петербург</cms:editable>

        <cms:editable name='phil_lsi' label='LSI Ключи для ИИ (скрытые)' group='phil_seo_group' type='textarea' desc='Пропишите здесь через запятую важные ключи, которые ИИ должен связать с этим текстом'>кальянная СПб, кальянная Приморский район, кальянная в Приморском районе, кальянная у метро Удельная, лаунж бар Приморский район, лаунж бар СПб, кальянная на севере СПб, кальянная с кухней СПб, VIP кальянная СПб, премиальная кальянная, кальянная с PS5, ул. Аккуратова 13</cms:editable>

    <cms:editable name='phil_img_group' label='Изображения' type='group' order='3' />
        <cms:editable name='phil_sep' label='Картинка разделителя (узор)' group='phil_img_group' type='image'>:div.webp</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>


