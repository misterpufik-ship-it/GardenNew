<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Бронирование — уведомления' executable='0' order='5'>

    <cms:editable name='booking_intro' label='Справка' type='message' order='1'>
        Настройки отправки заявок с формы «Бронирование» в Telegram и VK. У каждого филиала — свои токены и получатели.
        В тексте сообщения можно использовать переменные: {branch}, {branch_label}, {name}, {phone}, {date}, {time}, {guests}.
        VK: нужен ключ доступа сообщества (Сообщения → Настройки → Работа с API) и peer_id получателя (ID пользователя или беседы).
    </cms:editable>

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

    <cms:editable name='adm_vk_enabled' label='VK — включить отправку' group='group_admiral_booking' type='dropdown' opt_values='Нет=0 | Да=1' order='17'>0</cms:editable>
    <cms:editable name='adm_vk_link' label='VK — ссылка (для справки)' group='group_admiral_booking' type='text' order='18'>https://vk.com/loungegarden</cms:editable>
    <cms:editable name='adm_vk_access_token' label='VK — ключ доступа сообщества' group='group_admiral_booking' type='text' order='19' desc='Ключ с правом messages. Создаётся в настройках сообщества VK → Работа с API.' />
    <cms:editable name='adm_vk_peer_id' label='VK — peer_id получателя' group='group_admiral_booking' type='text' order='20' desc='ID пользователя или беседы, куда приходят заявки. Для беседы — отрицательное число.' />
    <cms:editable name='adm_vk_message_template' label='VK — текст сообщения' group='group_admiral_booking' type='textarea' height='180' order='21' desc='Пусто — используется тот же текст, что и для Telegram.' />

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
    <cms:editable name='udel_vk_link' label='VK — ссылка (для справки)' group='group_udelnaya_booking' type='text' order='38'>https://vk.com/loungegarden</cms:editable>
    <cms:editable name='udel_vk_access_token' label='VK — ключ доступа сообщества' group='group_udelnaya_booking' type='text' order='39' desc='Ключ с правом messages. Создаётся в настройках сообщества VK → Работа с API.' />
    <cms:editable name='udel_vk_peer_id' label='VK — peer_id получателя' group='group_udelnaya_booking' type='text' order='40' desc='ID пользователя или беседы, куда приходят заявки. Для беседы — отрицательное число.' />
    <cms:editable name='udel_vk_message_template' label='VK — текст сообщения' group='group_udelnaya_booking' type='textarea' height='180' order='41' desc='Пусто — используется тот же текст, что и для Telegram.' />

</cms:template>
<?php COUCH::invoke(); ?>
