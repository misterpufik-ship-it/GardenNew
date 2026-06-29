<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Бронирование — уведомления' executable='0' order='5'>

    <cms:editable name='booking_intro' label='Справка' type='message' order='1'>
        Настройки отправки заявок с формы «Бронирование» в Telegram и VK.
        Telegram — у каждого филиала свой бот и Chat ID.
        VK — одно сообщество Garden Lounge; заявки уходят в личные сообщения на рабочий VK-аккаунт администратора филиала (у каждого филиала свой ID пользователя).
        Переменные в тексте: {branch}, {branch_label}, {name}, {phone}, {date}, {time}, {guests}.
    </cms:editable>

    <cms:editable name='group_vk_shared' label='VK — общие настройки' type='group' order='5' />
    <cms:editable name='vk_link' label='VK — ссылка на сообщество' group='group_vk_shared' type='text' order='6'>https://vk.com/loungegarden</cms:editable>
    <cms:editable name='vk_access_token' label='VK — ключ доступа сообщества' group='group_vk_shared' type='text' order='7' desc='Один ключ для всех филиалов. Сообщество → Управление → Сообщения → Работа с API, право «Сообщения сообщества».' />

    <cms:editable name='group_admiral_booking' label='Адмиралтейская — отправка' type='group' order='10' />
    <cms:editable name='adm_booking_enabled' label='Включить отправку' group='group_admiral_booking' type='dropdown' opt_values='Нет=0 | Да=1' order='11'>1</cms:editable>
    <cms:editable name='adm_branch_label' label='Название филиала в сообщении' group='group_admiral_booking' type='text' order='12'>Адмиралтейская</cms:editable>
    <cms:editable name='adm_telegram_link' label='Telegram (для справки)' group='group_admiral_booking' type='text' order='13'>https://t.me/gardenlounge_admiral</cms:editable>
    <cms:editable name='adm_bot_token' label='Bot Token' group='group_admiral_booking' type='text' order='14' />
    <cms:editable name='adm_chat_id' label='Chat ID' group='group_admiral_booking' type='text' order='15' desc='Числовой ID группы/чата, где добавлен бот @Reservnmoykibot (не ссылка t.me). После добавления бота в группу: getUpdates в API Telegram.'>@gardenlounge_admiral</cms:editable>
    <cms:editable name='adm_message_template' label='Текст сообщения в Telegram' group='group_admiral_booking' type='textarea' height='180' order='16' desc='Переменные: {branch}, {branch_label}, {name}, {phone}, {date}, {time}, {guests}'>🆕 Новая бронь — {branch_label}

Имя: {name}
Тел: {phone}
Дата: {date}
Время: {time}
Гостей: {guests}</cms:editable>

    <cms:editable name='adm_vk_enabled' label='VK — включить отправку' group='group_admiral_booking' type='dropdown' opt_values='Нет=0 | Да=1' order='17'>1</cms:editable>
    <cms:editable name='adm_vk_user_id' label='VK — ID пользователя (личка)' group='group_admiral_booking' type='text' order='18' desc='Числовой ID рабочего VK-аккаунта администратора Адмиралтейской. Узнать: vk.com/id123 или через vk.com/dev. Аккаунт должен один раз написать сообществу, чтобы ему можно было писать.'>868619211</cms:editable>
    <cms:editable name='adm_vk_message_template' label='VK — текст сообщения' group='group_admiral_booking' type='textarea' height='180' order='19' desc='Пусто — используется тот же текст, что и для Telegram.' />

    <cms:editable name='group_udelnaya_booking' label='Удельная — отправка' type='group' order='30' />
    <cms:editable name='udel_booking_enabled' label='Включить отправку' group='group_udelnaya_booking' type='dropdown' opt_values='Нет=0 | Да=1' order='31'>1</cms:editable>
    <cms:editable name='udel_branch_label' label='Название филиала в сообщении' group='group_udelnaya_booking' type='text' order='32'>Удельная</cms:editable>
    <cms:editable name='udel_telegram_link' label='Telegram (для справки)' group='group_udelnaya_booking' type='text' order='33'>https://t.me/Garden_lounge_spb</cms:editable>
    <cms:editable name='udel_bot_token' label='Bot Token' group='group_udelnaya_booking' type='text' order='34' />
    <cms:editable name='udel_chat_id' label='Chat ID' group='group_udelnaya_booking' type='text' order='35' desc='Числовой ID группы/чата, где добавлен бот @Gardenmoylibot (не ссылка t.me). После добавления бота в группу: getUpdates в API Telegram.'>@Garden_lounge_spb</cms:editable>
    <cms:editable name='udel_message_template' label='Текст сообщения в Telegram' group='group_udelnaya_booking' type='textarea' height='180' order='36' desc='Переменные: {branch}, {branch_label}, {name}, {phone}, {date}, {time}, {guests}'>🆕 Новая бронь — {branch_label}

Имя: {name}
Тел: {phone}
Дата: {date}
Время: {time}
Гостей: {guests}</cms:editable>

    <cms:editable name='udel_vk_enabled' label='VK — включить отправку' group='group_udelnaya_booking' type='dropdown' opt_values='Нет=0 | Да=1' order='37'>0</cms:editable>
    <cms:editable name='udel_vk_user_id' label='VK — ID пользователя (личка)' group='group_udelnaya_booking' type='text' order='38' desc='Числовой ID рабочего VK-аккаунта администратора Удельной. Узнать: vk.com/id123 или через vk.com/dev. Аккаунт должен один раз написать сообществу, чтобы ему можно было писать.' />
    <cms:editable name='udel_vk_message_template' label='VK — текст сообщения' group='group_udelnaya_booking' type='textarea' height='180' order='39' desc='Пусто — используется тот же текст, что и для Telegram.' />

</cms:template>
<?php COUCH::invoke(); ?>
