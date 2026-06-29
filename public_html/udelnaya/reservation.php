<?php
define('K_TEMPLATE_NAME', 'udelnaya/reservation.php');
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
<cms:template title='Уделка Бронирование' name='reservation_section' executable='0' order='50'>
    
    <cms:editable name='res_group_titles' label='Заголовки секции' type='group' />
    <cms:editable name='res_title' label='Главный заголовок' group='res_group_titles' type='text'>Reservation</cms:editable>
    <cms:editable name='res_subtitle' label='Подзаголовок' group='res_group_titles' type='text'>Забронировать столик</cms:editable>

    <cms:editable name='res_group_modal' label='Тексты после отправки' type='group' />
    <cms:editable name='res_modal_title' label='Заголовок модального окна' group='res_group_modal' type='text'>Спасибо!</cms:editable>
    <cms:editable name='res_modal_text' label='Текст подтверждения' group='res_group_modal' type='textarea'>Ваше бронирование принято. Администратор свяжется с Вами для подтверждения.</cms:editable>
    <cms:editable name='res_modal_notice' label='Дополнительный текст после бронирования' group='res_group_modal' type='textarea' desc='Показывается под основным текстом в окне после нажатия «Забронировать». Можно менять в любой момент.'>На компании от 6 человек взимается сервисный сбор 10%.

Посещение Garden Lounge строго с 18 лет.</cms:editable>

    <cms:editable name='res_group_decor' label='Декор' type='group' />
    <cms:editable name='res_sep' label='Картинка разделителя' group='res_group_decor' type='image'>:div.webp</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>


