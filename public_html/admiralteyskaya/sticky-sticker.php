<?php require_once('couch/cms.php'); ?>
<cms:template title='Липкий стикер' name='sticky_sticker' executable='0' order='165'>

    <cms:editable name='grp_sticker' label='Стикер (текстовое меню)' type='group' order='1' />
    <cms:editable name='sticker_enabled' label='Показывать стикер' group='grp_sticker' type='dropdown' opt_values='Да=1 | Нет=0'>1</cms:editable>
    <cms:editable name='sticker_image' label='Изображение стикера' group='grp_sticker' type='image' show_preview='1' preview_width='200'>https://garden-lounge.pro/img/garden-second-sticker.webp</cms:editable>
    <cms:editable name='sticker_alt' label='Alt-текст стикера' group='grp_sticker' type='text'>А ты был во втором Гардене?</cms:editable>
    <cms:editable name='sticker_width' label='Макс. ширина (px)' group='grp_sticker' type='text'>364</cms:editable>
    <cms:editable name='appear_delay' label='Задержка появления (сек)' group='grp_sticker' type='text'>10</cms:editable>

    <cms:editable name='grp_modal' label='Всплывающее окно' type='group' order='2' />
    <cms:editable name='modal_logo' label='Логотип филиала (слева)' group='grp_modal' type='image' show_preview='1' preview_width='120'>https://garden-lounge.pro/img/logo3.webp</cms:editable>
    <cms:editable name='modal_title' label='Заголовок' group='grp_modal' type='text'>Garden Lounge — Удельная</cms:editable>
    <cms:editable name='modal_lead' label='Текст' group='grp_modal' type='textarea'>Второй филиал сети — камерный лаунж у метро Удельная. Тот же Garden по духу: кальяны, авторская кухня, бар и VIP-зоны, но в более уютном формате.</cms:editable>
    <cms:editable name='modal_site_url' label='Ссылка на сайт филиала' group='grp_modal' type='text'>https://garden-lounge.pro/udelnaya/</cms:editable>
    <cms:editable name='modal_site_label' label='Подпись кнопки сайта' group='grp_modal' type='text'>Сайт филиала</cms:editable>

    <cms:editable name='grp_facts' label='Факты (иконка + текст)' type='group' order='3' />
    <cms:repeatable name='modal_facts' label='Пункты' group='grp_facts'>
        <cms:editable name='fact_icon' label='Иконка' type='dropdown' opt_values='Адрес=location-dot | Часы=clock | Телефон=phone | Метро=train-subway | Звезда=star' />
        <cms:editable name='fact_text' label='Текст' type='textarea' />
    </cms:repeatable>

    <cms:editable name='grp_gallery' label='Слайдер фото' type='group' order='4' />
    <cms:repeatable name='modal_gallery' label='Слайды' group='grp_gallery'>
        <cms:editable name='slide_image' label='Фото' type='image' show_preview='1' preview_width='160' />
        <cms:editable name='slide_caption' label='Подпись (alt)' type='text' />
    </cms:repeatable>

</cms:template>
<?php COUCH::invoke(); ?>
