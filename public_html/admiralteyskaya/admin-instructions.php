<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Инструкции' name='admin_instructions' executable='0' order='-10' icon='info'>

    <cms:editable name='guide_notice' label='Справка' type='message' order='1'>
        Эта страница открывается при входе в админку. Текст ниже можно редактировать — изменения сохраняются кнопкой «Сохранить» внизу.
    </cms:editable>

    <cms:editable name='group_basics' label='Основы работы с CouchCMS' type='group' collapsed='1' order='10' />
    <cms:editable name='guide_basics' label='Содержание' group='group_basics' type='textarea' order='11'><![CDATA[
Сайт Garden Lounge работает на CouchCMS — контент хранится в базе, а разделы админки соответствуют страницам сайта.

Как устроена админка:
• Слева — разделы сайта (Главная, Адмиралтейская, Удельная, Общие).
• Справа — поля выбранного раздела.
• Внизу — «Расширенные настройки», «Сохранить», «Предпросмотр» и стрелка «наверх».

Как редактировать:
1. Выберите раздел в левом меню.
2. Раскройте нужный блок (+) и измените поля.
3. Нажмите «Сохранить».
4. Нажмите «Предпросмотр» или «Просмотр сайта», чтобы проверить результат.

Повторяемые списки (блюда, акции, фото галереи):
• «+ Добавить» — новая строка.
• Перетаскивание — порядок элементов.
• Корзина — удаление строки.
]]></cms:editable>

    <cms:editable name='group_home' label='Раздел «Главная»' type='group' collapsed='1' order='20' />
    <cms:editable name='guide_home' label='Содержание' group='group_home' type='textarea' order='21'><![CDATA[
Что внутри: SEO главной страницы garden-lounge.pro, логотип, карточки филиалов (Адмиралтейская / Удельная), соцсети, оформление кнопок.

Как редактировать: откройте «Главная» → «Главная» в левом меню, измените поля в группах SEO / Логотип / Филиалы.

Как проверить: сохраните и откройте https://garden-lounge.pro/

Кэш: после изменений на главной очистите кэш (см. блок «Кэш и обновление сайта»).
]]></cms:editable>

    <cms:editable name='group_admiral' label='Раздел «Адмиралтейская»' type='group' collapsed='1' order='30' />
    <cms:editable name='guide_admiral' label='Содержание' group='group_admiral' type='textarea' order='31'><![CDATA[
Страницы филиала: шапка, концепция, акции, меню, галерея, бронирование, контакты, филиал, футер и SEO.

Типовой порядок работы:
1. Откройте нужный подраздел (например «Меню RU» или «Галерея»).
2. Измените тексты, цены, фото.
3. Сохраните.
4. Проверьте страницу на https://garden-lounge.pro/admiralteyskaya/

Меню филиала:
• «Меню (общие настройки)» — обложки и ссылки на визуальное/текстовое меню.
• «Меню RU» / «Меню EN» — текстовое меню с вкладками.
• «Меню визуальное» — карточки блюд с фото (категории: Кальяны, Кухня, Бар).
]]></cms:editable>

    <cms:editable name='group_udelnaya' label='Раздел «Удельная»' type='group' collapsed='1' order='40' />
    <cms:editable name='guide_udelnaya' label='Содержание' group='group_udelnaya' type='textarea' order='41'><![CDATA[
Структура такая же, как у Адмиралтейской, но для филиала на ул. Аккуратова.

Проверка: https://garden-lounge.pro/udelnaya/

При копировании позиций меню между филиалами используйте блок «Копирование между филиалами» в «Меню RU».
]]></cms:editable>

    <cms:editable name='group_menu_visual' label='Визуальное меню' type='group' collapsed='1' order='50' />
    <cms:editable name='guide_menu_visual' label='Содержание' group='group_menu_visual' type='textarea' order='51'><![CDATA[
Раздел «Меню визуальное» — карточки с фото для сайта.

Поля блюда: название, тег (New / Hit / острота), цена, вес, фото, описание.
Категории редактируются отдельными списками: Кальяны, Кухня, Бар.
Логотип и разделитель — вверху формы.

Как проверить: «Предпросмотр» или страница /admiralteyskaya/menu/visual/ (или /udelnaya/menu/visual/).
]]></cms:editable>

    <cms:editable name='group_common' label='Раздел «Общие»' type='group' collapsed='1' order='60' />
    <cms:editable name='guide_common' label='Содержание' group='group_common' type='textarea' order='61'><![CDATA[
Общие настройки для всего сайта:
• Футер и SEO — отступы между блоками, соцсети, контакты филиалов, SEO по умолчанию.
• Бронирование Telegram — уведомления и тексты формы.
• Прелоадер — экран загрузки.
• Заглушка 18+ — возрастное окно.
• Названия разделов — подписи пунктов левого меню админки (необязательно менять).
]]></cms:editable>

    <cms:editable name='group_save' label='Сохранение и проверка' type='group' collapsed='1' order='70' />
    <cms:editable name='guide_save' label='Содержание' group='group_save' type='textarea' order='71'><![CDATA[
Сохранение:
• Кнопка «Сохранить» внизу формы — всегда сохраняйте после правок.
• Дождитесь сообщения об успешном сохранении.

Проверка:
• «Предпросмотр» — черновик текущей страницы.
• «Просмотр сайта» (внизу слева) — открыть живой сайт в новой вкладке.
• Если изменения не видны — очистите кэш браузера (Ctrl+F5) и кэш сайта.
]]></cms:editable>

    <cms:editable name='group_cache' label='Кэш и обновление сайта' type='group' collapsed='1' order='80' />
    <cms:editable name='guide_cache' label='Содержание' group='group_cache' type='textarea' order='81'><![CDATA[
CouchCMS кэширует HTML-страницы для скорости. После правок иногда нужна очистка кэша.

Способы:
1. При сохранении страницы кэш этой страницы сбрасывается автоматически.
2. Полная очистка на сервере: скрипт _maintenance/clear-cache-web.php (только для администратора, по запросу разработчика).
3. В браузере: жёсткое обновление Ctrl+F5 или режим инкогнито.

Если после сохранения на сайте старый текст — сначала Ctrl+F5, затем сообщите разработчику о полной очистке кэша.
]]></cms:editable>

    <cms:repeatable name='guide_extra' label='Дополнительные инструкции' order='100'>
        <cms:editable name='extra_title' label='Заголовок' type='text' />
        <cms:editable name='extra_body' label='Текст' type='textarea' />
    </cms:repeatable>

</cms:template>
<?php COUCH::invoke(); ?>
