<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Бронирование' name='reservation_section' executable='0' order='180'>
    
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

<cms:editable name='adm_reserve_rule' label='Адмиралтейская — ограничение онлайн-брони' type='group' order='50' />
    <cms:editable name='adm_reserve_enabled' label='Включить ограничение' group='adm_reserve_rule' type='dropdown' order='51' opt_values='Нет=0 | Да=1'>1</cms:editable>
    <cms:editable name='adm_reserve_days' label='Дни недели (1 — Пн, 7 — Вс)' group='adm_reserve_rule' type='text' order='52' desc='Номера дней через запятую. Например: 5,6 — пятница и суббота. День определяется по дате визита.'>5,6</cms:editable>
    <cms:editable name='adm_reserve_start' label='Начало интервала (ЧЧ:ММ)' group='adm_reserve_rule' type='text' order='53' >18:00</cms:editable>
    <cms:editable name='adm_reserve_end' label='Конец интервала включительно (ЧЧ:ММ)' group='adm_reserve_rule' type='text' order='54' desc='Время Санкт-Петербурга. Если конец раньше начала, интервал проходит через полночь.'>22:00</cms:editable>
    <cms:editable name='adm_reserve_minutes' label='Минимум минут до визита' group='adm_reserve_rule' type='text' order='55' desc='Целое число минут: 90 — полтора часа, 120 — два часа, 0 — без минимального запаса.'>90</cms:editable>
    <cms:editable name='adm_reserve_message' label='Предупреждение гостю' group='adm_reserve_rule' type='textarea' order='56' desc='Переменная {minutes} заменяется на установленное число минут. Телефон выводится отдельной ссылкой для звонка.'>До времени вашего визита осталось меньше {minutes} минут. Для резерва свяжитесь, пожалуйста, с нами по телефону.</cms:editable>
    <cms:editable name='adm_reserve_phone' label='Телефон для звонка' group='adm_reserve_rule' type='text' order='57' >+7 995 624-68-08</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
