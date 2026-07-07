<?php
if (!defined('K_TEMPLATE_NAME')) {
    define('K_TEMPLATE_NAME', 'udelnaya/sticky-sticker.php');
}
require_once dirname(__DIR__) . '/couch/cms.php';
?>
<cms:template title='Липкий стикер — Удельная' name='sticky_sticker_udel' executable='0' order='165'>

    <cms:editable name='grp_sticker' label='Стикер (текстовое меню)' type='group' order='1' />
    <cms:editable name='sticker_enabled' label='Показывать стикер' group='grp_sticker' type='dropdown' opt_values='Да=1 | Нет=0'>1</cms:editable>
    <cms:editable name='sticker_image' label='Изображение стикера' group='grp_sticker' type='image' show_preview='1' preview_width='200'>https://garden-lounge.pro/img/garden-second-sticker.webp</cms:editable>
    <cms:editable name='sticker_alt' label='Alt-текст стикера' group='grp_sticker' type='text'>А ты был в Garden на Адмиралтейской?</cms:editable>
    <cms:editable name='sticker_width' label='Макс. ширина (px)' group='grp_sticker' type='text'>364</cms:editable>
    <cms:editable name='appear_delay' label='Задержка появления (сек)' group='grp_sticker' type='text'>10</cms:editable>

    <cms:editable name='grp_modal' label='Всплывающее окно' type='group' order='2' />
    <cms:editable name='modal_logo' label='Монограмма GL (сверху)' group='grp_modal' type='image' show_preview='1' preview_width='120'>https://garden-lounge.pro/img/logo-gl.webp</cms:editable>
    <cms:editable name='modal_title_branch' label='Название филиала в заголовке' group='grp_modal' type='text'>Адмиралтейская</cms:editable>
    <cms:editable name='modal_lead' label='Текст' group='grp_modal' type='textarea'>Флагманский лаунж в центре Петербурга на набережной Мойки. Кальяны, авторская кухня, бар, VIP-комнаты и атмосфера Garden в самом сердце города.</cms:editable>
    <cms:editable name='modal_site_url' label='Ссылка на сайт филиала' group='grp_modal' type='text'>https://garden-lounge.pro/admiralteyskaya/</cms:editable>
    <cms:editable name='modal_site_label' label='Текст кнопки' group='grp_modal' type='text'>Войти в оазис</cms:editable>

    <cms:editable name='grp_gallery' label='Слайдер (из Галереи другого филиала)' type='group' order='3' />
    <cms:editable name='sticker_gallery_note' label='Источник фото' group='grp_gallery' type='textarea' hidden='1'>Слайдер подтягивается автоматически из раздела «Галерея» филиала Адмиралтейская.</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>
