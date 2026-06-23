<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Заглушка 18+' executable='0' order='3'>

    <cms:editable name='ag_intro' label='Справка' type='message' order='1'>
        Заглушка 18+ показывается посетителям до подтверждения возраста. После согласия выбор запоминается в браузере на указанный срок.
        Если заглушка отключена, скрипт и стили на сайт не подключаются.
    </cms:editable>

    <cms:editable name='group_ag_main' label='Включение' type='group' order='10' />
    <cms:editable name='ag_enabled' label='Показывать заглушку 18+' group='group_ag_main' type='dropdown' opt_values='Нет=0 | Да=1' order='11'>1</cms:editable>

    <cms:editable name='group_ag_scope' label='Где показывать' type='group' order='20' />
    <cms:editable name='ag_scope_mode' label='Режим показа' group='group_ag_scope' type='dropdown' opt_values='На всём сайте=all | Только на выбранных разделах=include | Везде, кроме выбранных=exclude' order='21' desc='Раздел определяется по URL страницы.'>all</cms:editable>
    <cms:editable name='ag_sections' label='Разделы сайта' group='group_ag_scope' type='text' order='22' desc='Через запятую: home, admiral, udelnaya, menu. home — главная garden-lounge.pro; admiral — /admiralteyskaya/; udelnaya — /udelnaya/; menu — все страницы меню.'>home, admiral, udelnaya, menu</cms:editable>

    <cms:editable name='group_ag_storage' label='Запоминание выбора' type='group' order='30' />
    <cms:editable name='ag_storage_mode' label='Способ хранения' group='group_ag_storage' type='dropdown' opt_values='localStorage и cookie=both | Только cookie=cookie | Только на время сессии (без cookie)=session' order='31'>both</cms:editable>
    <cms:editable name='ag_remember_days' label='Срок запоминания (дней)' group='group_ag_storage' type='text' order='32' desc='Для режимов с cookie/localStorage. 0 — до закрытия вкладки.'>365</cms:editable>

    <cms:editable name='group_ag_appearance' label='Оформление' type='group' order='40' />
    <cms:editable name='ag_logo' label='Логотип' group='group_ag_appearance' type='image' order='41'>:logo3.webp</cms:editable>
    <cms:editable name='ag_badge' label='Бейдж (над заголовком)' group='group_ag_appearance' type='text' order='42'>18+</cms:editable>
    <cms:editable name='ag_color_gold' label='Золотой акцент (CSS-цвет)' group='group_ag_appearance' type='text' order='43'>#C5A059</cms:editable>
    <cms:editable name='ag_color_gold_dark' label='Тёмное золото (кнопка «Да»)' group='group_ag_appearance' type='text' order='44'>#8e7037</cms:editable>
    <cms:editable name='ag_color_gold_light' label='Светлое золото (кнопка «Да»)' group='group_ag_appearance' type='text' order='45'>#FFEebb</cms:editable>
    <cms:editable name='ag_overlay_opacity' label='Затемнение фона (0–100)' group='group_ag_appearance' type='text' order='46'>92</cms:editable>

    <cms:editable name='group_ag_texts' label='Тексты' type='group' order='50' />
    <cms:editable name='ag_title' label='Заголовок' group='group_ag_texts' type='text' order='51'>Вам уже исполнилось 18 лет?</cms:editable>
    <cms:editable name='ag_welcome_admiral' label='Приветствие — Адмиралтейская' group='group_ag_texts' type='text' order='52'>Добро пожаловать в Garden Lounge.</cms:editable>
    <cms:editable name='ag_welcome_udelnaya' label='Приветствие — Удельная' group='group_ag_texts' type='text' order='53'>Добро пожаловать в Garden Lounge на Удельной.</cms:editable>
    <cms:editable name='ag_description' label='Описание под приветствием' group='group_ag_texts' type='textarea' height='100' order='54'>Перед входом подтвердите возраст, чтобы продолжить знакомство с нашим садом. Сайт содержит информацию о заведении, где представлены кальяны и табачная продукция.</cms:editable>
    <cms:editable name='ag_btn_yes' label='Кнопка «Да»' group='group_ag_texts' type='text' order='55'>Да, войти</cms:editable>
    <cms:editable name='ag_btn_no' label='Кнопка «Нет»' group='group_ag_texts' type='text' order='56'>Нет, покинуть сайт</cms:editable>
    <cms:editable name='ag_denied_text' label='Текст при отказе' group='group_ag_texts' type='text' order='57'>Дальнейшее отображение материалов сайта невозможно</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
