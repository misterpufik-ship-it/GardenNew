<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Контакты' name='contacts_section' executable='0' order='190'>
    
    <cms:editable name='cont_group_main' label='Основная информация' type='group' />
    <cms:editable name='cont_address' label='Адрес' group='cont_group_main' type='text'>СПб. наб. реки Мойки, д.67-69</cms:editable>
    <cms:editable name='cont_map_link' label='Ссылка на карты (маршрут)' group='cont_group_main' type='text'>https://yandex.ru/maps/org/garden_lounge/92097430496/</cms:editable>
    <cms:editable name='cont_phone' label='Телефон' group='cont_group_main' type='text'>+7 995 624-68-08</cms:editable>
    <cms:editable name='cont_phone_link' label='Телефон (для ссылки без пробелов)' group='cont_group_main' type='text'>+79956246808</cms:editable>

    <cms:editable name='cont_group_hours' label='Часы работы' type='group' />
    <cms:editable name='cont_hours_1' label='Пн–Чт; Вс' group='cont_group_hours' type='text'>12:00 – 01:00</cms:editable>
    <cms:editable name='cont_hours_2' label='Пт–Сб' group='cont_group_hours' type='text'>12:00 – 03:00</cms:editable>

    <cms:editable name='cont_group_social' label='Социальные сети' type='group' />
    <cms:editable name='cont_whatsapp' label='WhatsApp (номер или ссылка)' group='cont_group_social' type='text'>https://wa.me/79956246808</cms:editable>
    <cms:editable name='cont_telegram' label='Telegram (ссылка)' group='cont_group_social' type='text'>https://t.me/gardenlounge_admiral</cms:editable>

    <cms:editable name='cont_group_ratings' label='Рейтинги (цифры и ссылки)' type='group' />
    <cms:editable name='rate_yandex_val' label='Яндекс (балл)' group='cont_group_ratings' type='text' desc='Отображается на сайте и в JSON-LD (AggregateRating). Обновите при изменении рейтинга на Яндекс.Картах.'>5.0</cms:editable>
    <cms:editable name='rate_yandex_count' label='Яндекс (кол-во отзывов)' group='cont_group_ratings' type='text' desc='Формат: «480+ отзывов». Число автоматически попадает в schema.org (reviewCount). Обновляйте вместе с Яндекс.Картами.'>480+ отзывов</cms:editable>
    <cms:editable name='rate_yandex_link' label='Яндекс (ссылка на отзывы)' group='cont_group_ratings' type='text'>https://yandex.ru/maps/org/garden_lounge/92097430496/reviews/</cms:editable>
   
    <cms:editable name='rate_2gis_val' label='2GIS (балл)' group='cont_group_ratings' type='text'>4.9</cms:editable>
    <cms:editable name='rate_2gis_count' label='2GIS (кол-во отзывов)' group='cont_group_ratings' type='text'>131+ отзыв</cms:editable>
    <cms:editable name='rate_2gis_link' label='Google (ссылка)' group='cont_group_ratings' type='text'>https://2gis.ru/spb/firm/70000001089303834/tab/reviews</cms:editable>
    
<cms:editable name='rate_google_val' label='Google (балл)' group='cont_group_ratings' type='text'>4.7</cms:editable>
    <cms:editable name='rate_google_count' label='Google (отзывы)' group='cont_group_ratings' type='text'>13 отзывов</cms:editable>
    <cms:editable name='rate_google_link' label='Google (ссылка)' group='cont_group_ratings' type='text'>https://maps.app.goo.gl/rcwMbaXdfrbSTowd7</cms:editable>
  
    <cms:editable name='cont_map_iframe' label='Код виджета карты (только URL из src)' group='cont_group_main' type='text'>https://yandex.ru/map-widget/v1/?um=constructor%3A86b78088cdeb728323715abfc506cc9bb4ed50969d2f614eda2941d37b52b3c5&source=constructor</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
