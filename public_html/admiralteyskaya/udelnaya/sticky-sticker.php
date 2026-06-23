<?php
define('K_TEMPLATE_NAME', 'udelnaya/sticky-sticker.php');
$garden_cms = null;
foreach ([
    __DIR__ . '/../couch/cms.php',
    __DIR__ . '/../../couch/cms.php',
    __DIR__ . '/couch/cms.php',
    __DIR__ . '/../admiralteyskaya/couch/cms.php',
    __DIR__ . '/../../admiralteyskaya/couch/cms.php',
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
<cms:template title='Липкий стикер — Удельная' name='sticky_sticker_udel' executable='0' order='165'>

    <cms:editable name='grp_sticker' label='Стикер (текстовое меню)' type='group' order='1' />
    <cms:editable name='sticker_enabled' label='Показывать стикер' group='grp_sticker' type='dropdown' opt_values='Да=1 | Нет=0'>1</cms:editable>
    <cms:editable name='sticker_image' label='Изображение стикера' group='grp_sticker' type='image' show_preview='1' preview_width='200'>https://garden-lounge.pro/img/garden-second-sticker.webp</cms:editable>
    <cms:editable name='sticker_alt' label='Alt-текст стикера' group='grp_sticker' type='text'>А ты был в Garden на Адмиралтейской?</cms:editable>
    <cms:editable name='sticker_width' label='Макс. ширина (px)' group='grp_sticker' type='text'>364</cms:editable>
    <cms:editable name='appear_delay' label='Задержка появления (сек)' group='grp_sticker' type='text'>10</cms:editable>

    <cms:editable name='grp_modal' label='Всплывающее окно' type='group' order='2' />
    <cms:editable name='modal_logo' label='Логотип филиала (сверху)' group='grp_modal' type='image' show_preview='1' preview_width='120'>https://garden-lounge.pro/img/logo3.webp</cms:editable>
    <cms:editable name='modal_title_branch' label='Название филиала в заголовке' group='grp_modal' type='text'>Адмиралтейская</cms:editable>
    <cms:editable name='modal_lead' label='Текст' group='grp_modal' type='textarea'>Флагманский лаунж в центре Петербурга на набережной Мойки. Кальяны, авторская кухня, бар, VIP-комнаты и атмосфера Garden в самом сердце города.</cms:editable>
    <cms:editable name='modal_site_url' label='Ссылка на сайт филиала' group='grp_modal' type='text'>https://garden-lounge.pro/admiralteyskaya/</cms:editable>
    <cms:editable name='modal_site_label' label='Текст кнопки' group='grp_modal' type='text'>Войти в оазис</cms:editable>

    <cms:editable name='grp_gallery' label='Слайдер (Interior / Vibe)' type='group' order='3' />
    <cms:editable name='sticker_grp_interior' label='Interior — интерьер' type='group' collapsed='1' group='grp_gallery' order='10' />
    <cms:repeatable name='modal_interior_items' label='Фото интерьера' group='sticker_grp_interior'>
        <cms:editable name='slide_image' label='Фото' type='image' show_preview='1' preview_width='160' />
        <cms:editable name='slide_caption' label='Подпись (alt)' type='text' />
    </cms:repeatable>

    <cms:editable name='sticker_grp_vibe' label='Vibe — атмосфера' type='group' collapsed='1' group='grp_gallery' order='20' />
    <cms:repeatable name='modal_vibe_items' label='Фото атмосферы' group='sticker_grp_vibe'>
        <cms:editable name='slide_image' label='Фото' type='image' show_preview='1' preview_width='160' />
        <cms:editable name='slide_caption' label='Подпись (alt)' type='text' />
    </cms:repeatable>

</cms:template>
<?php COUCH::invoke(); ?>
