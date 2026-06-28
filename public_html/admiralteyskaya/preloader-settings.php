<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Прелоадер' executable='0' order='3'>

    <cms:editable name='preloader_intro' label='Справка' type='message' order='1'>
        Видео-прелоадер показывается при открытии страницы до полной загрузки сайта. Если отключён — видео и скрипт не подключаются.
        Разделы: home — главная garden-lounge.pro; admiral — /admiralteyskaya/; udelnaya — /udelnaya/; admiral_udelnaya — /admiralteyskaya/udelnaya/.
    </cms:editable>

    <cms:editable name='group_preloader_main' label='Включение и показ' type='group' order='10' />
    <cms:editable name='preloader_enabled' label='Включить прелоадер' group='group_preloader_main' type='dropdown' opt_values='Нет=0 | Да=1' order='11'>1</cms:editable>
    <cms:editable name='preloader_scope_mode' label='Режим показа' group='group_preloader_main' type='dropdown' opt_values='На всех выбранных разделах=all | Только на выбранных=include | Везде, кроме выбранных=exclude' order='12'>all</cms:editable>
    <cms:editable name='preloader_sections' label='Разделы сайта' group='group_preloader_main' type='text' order='13' desc='Через запятую: home, admiral, udelnaya, admiral_udelnaya'>home, admiral, udelnaya, admiral_udelnaya</cms:editable>
    <cms:editable name='preloader_video' label='Видео (путь или URL)' group='group_preloader_main' type='text' order='14' desc='По умолчанию /video/preloader.mp4. Можно указать полный URL или путь от корня сайта.'>/video/preloader.mp4</cms:editable>

    <cms:editable name='group_preloader_timing' label='Скорость и тайминги' type='group' order='20' />
    <cms:editable name='preloader_min_time' label='Минимальное время показа (мс)' group='group_preloader_timing' type='text' order='21' desc='Чтобы прелоадер не мигал при быстрой загрузке.'>1200</cms:editable>
    <cms:editable name='preloader_max_time' label='Максимальное время показа (мс)' group='group_preloader_timing' type='text' order='22' desc='Принудительно скрыть, если видео или страница зависли.'>8000</cms:editable>
    <cms:editable name='preloader_playback_rate' label='Скорость воспроизведения' group='group_preloader_timing' type='text' order='23' desc='1 — обычная скорость. Рекомендуется 1.3. Допустимо от 0.5 до 3.'>1.3</cms:editable>

    <cms:editable name='group_preloader_desktop' label='Десктоп (от 768px)' type='group' order='30' />
    <cms:editable name='preloader_desktop_object_fit' label='Как вписать видео' group='group_preloader_desktop' type='dropdown' opt_values='На весь экран=cover | Вписать без обрезки=contain' order='31'>cover</cms:editable>

    <cms:editable name='group_preloader_mobile' label='Мобильные (до 767px)' type='group' order='40' />
    <cms:editable name='preloader_mobile_object_fit' label='Как вписать видео' group='group_preloader_mobile' type='dropdown' opt_values='Вписать без обрезки=contain | На весь экран=cover' order='41' desc='«Вписать без обрезки» убирает слишком широкий кадр на телефонах.'>contain</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
