<?php require_once( '../../couch/cms.php' ); ?>

<cms:template title='Меню RU' order='140'>

    <cms:editable name='translation_script' type='message' order='0'>
        <cms:embed 'auto-translate-admin.html' />
    </cms:editable>

    <cms:editable name='branch_copy_script' type='message' order='0.1'>
        <cms:set branch_copy_from='admiralteyskaya' />
        <cms:set branch_copy_to='udelnaya' />
        <cms:set branch_copy_to_label='Удельная' />
        <cms:embed 'menu-copy-admin.html' />
    </cms:editable>

    <cms:editable name='seo_group' label='SEO Настройки' type='group' order='1' collapsed='1' />
    <cms:editable name='page_title' label='Title (RU)' type='text' group='seo_group'>Garden Lounge</cms:editable>
    <cms:editable name='page_title_en' label='Title (EN)' type='text' group='seo_group'>Garden Lounge</cms:editable>
    <cms:editable name='meta_desc' label='Description' type='textarea' group='seo_group'></cms:editable>
    <cms:editable name='meta_desc_en' label='Description (EN)' type='textarea' group='seo_group'></cms:editable>

    <cms:editable name='settings_group' label='Основные настройки' type='group' order='2' collapsed='1' />
    <cms:editable name='site_logo' label='Логотип' type='image' group='settings_group' show_preview='1' preview_width='150'>https://garden-lounge.pro/img/logo3.webp</cms:editable>

    <cms:editable name='lbl_tab_1' label='Вкладка 1 (RU)' type='text' group='settings_group'>Кальяны</cms:editable>
    <cms:editable name='lbl_tab_1_en' label='Tab 1 (EN)' type='text' group='settings_group'>Hookahs</cms:editable>
    <cms:editable name='lbl_tab_2' label='Вкладка 2 (RU)' type='text' group='settings_group'>Кухня</cms:editable>
    <cms:editable name='lbl_tab_2_en' label='Tab 2 (EN)' type='text' group='settings_group'>Kitchen</cms:editable>
    <cms:editable name='lbl_tab_3' label='Вкладка 3 (RU)' type='text' group='settings_group'>Бар</cms:editable>
    <cms:editable name='lbl_tab_3_en' label='Tab 3 (EN)' type='text' group='settings_group'>Bar</cms:editable>
    <cms:editable name='lbl_tab_4' label='Вкладка 4 (RU)' type='text' group='settings_group'>Напитки</cms:editable>
    <cms:editable name='lbl_tab_4_en' label='Tab 4 (EN)' type='text' group='settings_group'>Drinks</cms:editable>
    <cms:editable name='lbl_tab_5' label='Вкладка 5 (RU)' type='text' group='settings_group'>Акции</cms:editable>
    <cms:editable name='lbl_tab_5_en' label='Tab 5 (EN)' type='text' group='settings_group'>Specials</cms:editable>

    <cms:editable name='subtabs_group' label='Подвкладки (названия)' type='group' order='3' collapsed='1' />
    <cms:editable name='kt_sub_snacks' label='Кухня: Закуски' type='text' group='subtabs_group'>Закуски</cms:editable>
    <cms:editable name='kt_sub_snacks_en' label='Kitchen: Snacks (EN)' type='text' group='subtabs_group'>Snacks</cms:editable>
    <cms:editable name='kt_sub_salads' label='Кухня: Салаты' type='text' group='subtabs_group'>Салаты</cms:editable>
    <cms:editable name='kt_sub_salads_en' label='Kitchen: Salads (EN)' type='text' group='subtabs_group'>Salads</cms:editable>
    <cms:editable name='kt_sub_rolls' label='Кухня: Роллы' type='text' group='subtabs_group'>Роллы</cms:editable>
    <cms:editable name='kt_sub_rolls_en' label='Kitchen: Rolls (EN)' type='text' group='subtabs_group'>Rolls</cms:editable>
    <cms:editable name='kt_sub_soups' label='Кухня: Супы' type='text' group='subtabs_group'>Супы</cms:editable>
    <cms:editable name='kt_sub_soups_en' label='Kitchen: Soups (EN)' type='text' group='subtabs_group'>Soups</cms:editable>
    <cms:editable name='kt_sub_poke' label='Кухня: Поке' type='text' group='subtabs_group'>Поке | Боул | Wok</cms:editable>
    <cms:editable name='kt_sub_poke_en' label='Kitchen: Poke (EN)' type='text' group='subtabs_group'>Poke | Bowl | Wok</cms:editable>
    <cms:editable name='kt_sub_hot' label='Кухня: Горячее' type='text' group='subtabs_group'>Горячее</cms:editable>
    <cms:editable name='kt_sub_hot_en' label='Kitchen: Hot (EN)' type='text' group='subtabs_group'>Hot</cms:editable>
    <cms:editable name='kt_sub_desserts' label='Кухня: Десерты' type='text' group='subtabs_group'>Десерты</cms:editable>
    <cms:editable name='kt_sub_desserts_en' label='Kitchen: Desserts (EN)' type='text' group='subtabs_group'>Desserts</cms:editable>
    <cms:editable name='dt_sub_tea' label='Напитки: Чай' type='text' group='subtabs_group'>Чай | Кофе</cms:editable>
    <cms:editable name='dt_sub_tea_en' label='Drinks: Tea (EN)' type='text' group='subtabs_group'>Tea | Coffee</cms:editable>
    <cms:editable name='dt_sub_lemon' label='Напитки: Лемонады' type='text' group='subtabs_group'>Лемонады</cms:editable>
    <cms:editable name='dt_sub_lemon_en' label='Drinks: Lemonades (EN)' type='text' group='subtabs_group'>Lemonades</cms:editable>
    <cms:editable name='bt_sub_beer' label='Бар: Пиво' type='text' group='subtabs_group'>Пиво</cms:editable>
    <cms:editable name='bt_sub_beer_en' label='Bar: Beer (EN)' type='text' group='subtabs_group'>Beer</cms:editable>
    <cms:editable name='bt_sub_wine' label='Бар: Вино' type='text' group='subtabs_group'>Вино</cms:editable>
    <cms:editable name='bt_sub_wine_en' label='Bar: Wine (EN)' type='text' group='subtabs_group'>Wine</cms:editable>
    <cms:editable name='bt_sub_cocktails' label='Бар: Коктейли' type='text' group='subtabs_group'>Коктейли</cms:editable>
    <cms:editable name='bt_sub_cocktails_en' label='Bar: Cocktails (EN)' type='text' group='subtabs_group'>Cocktails</cms:editable>
    <cms:editable name='bt_sub_strong' label='Бар: Крепкий' type='text' group='subtabs_group'>Крепкий алкоголь</cms:editable>
    <cms:editable name='bt_sub_strong_en' label='Bar: Spirits (EN)' type='text' group='subtabs_group'>Spirits</cms:editable>

    <cms:editable name='grp_rep_hookahs' label='Содержание Кальяны' type='group' order='4' collapsed='1' />
    <cms:repeatable name='rep_hookahs_v2' label='Список Кальяны' group='grp_rep_hookahs'>
        <cms:editable name='item_tags' label='Тег' type='dropdown' opt_values='Нет=- | New | Hit | Special | Chef’s Choice | 🌶️ | 🌶️🌶️ | 🌶️🌶️🌶️ | New + 🌶️ | Hit + 🌶️' />
        <cms:editable name='row_type' label='Тип' type='dropdown' opt_values='Товар=item | Заголовок раздела=header | Подзаголовок раздела=subheader' />
        <cms:editable name='cat_title' label='Заголовок (RU)' type='text' />
        <cms:editable name='cat_title_en' label='Заголовок (EN)' type='text' />
        <cms:editable name='subcat_title' label='Подзаголовок (RU)' type='text' />
        <cms:editable name='subcat_title_en' label='Подзаголовок (EN)' type='text' />
        <cms:editable name='i_name' label='Товар RU' type='text' />
        <cms:editable name='i_name_en' label='Товар EN' type='text' />
        <cms:editable name='i_desc' label='Описание RU' type='textarea' height='40' />
        <cms:editable name='i_desc_en' label='Описание EN' type='textarea' height='40' />
        <cms:editable name='i_price' label='Цена' type='text' />
        <cms:editable name='note_after_ru' label='Примечание RU' type='textarea' height='30' />
        <cms:editable name='note_after_ru_en' label='Примечание EN' type='textarea' height='30' />
    </cms:repeatable>

    <cms:editable name='grp_rep_kitchen' label='Содержание Кухня' type='group' order='5' collapsed='1' />
    <cms:repeatable name='rep_kitchen_v2' label='Список Кухня' group='grp_rep_kitchen'>
        <cms:editable name='item_tags' label='Тег' type='dropdown' opt_values='Нет=- | New | Hit | Special | Chef’s Choice | 🌶️ | 🌶️🌶️ | 🌶️🌶️🌶️ | New + 🌶️ | Hit + 🌶️' />
        <cms:editable name='row_type' label='Тип' type='dropdown' opt_values='Товар=item | Заголовок раздела=header | Подзаголовок раздела=subheader' />
        <cms:editable name='cat_title' label='Заголовок (RU)' type='text' />
        <cms:editable name='cat_title_en' label='Заголовок (EN)' type='text' />
        <cms:editable name='subcat_title' label='Подзаголовок (RU)' type='text' />
        <cms:editable name='subcat_title_en' label='Подзаголовок (EN)' type='text' />
        <cms:editable name='kitchen_subtab' label='Категория' type='dropdown' opt_values='Закуски=snacks | Салаты=salads | Роллы=rolls | Супы=soups | Поке / Боул / Wok=poke_bowl_wok | Горячее=hot | Десерты=desserts | Другое=other' />
        <cms:editable name='kit_name' label='Название RU' type='text' />
        <cms:editable name='kit_name_en' label='Название EN' type='text' />
        <cms:editable name='kit_desc' label='Описание RU' type='textarea' height='40' />
        <cms:editable name='kit_desc_en' label='Описание EN' type='textarea' height='40' />
        <cms:editable name='kit_price' label='Цена' type='text' />
        <cms:editable name='note_after_ru' label='Примечание RU' type='textarea' height='30' />
        <cms:editable name='note_after_ru_en' label='Примечание EN' type='textarea' height='30' />
    </cms:repeatable>

    <cms:editable name='grp_rep_bar' label='Содержание Бар' type='group' order='6' collapsed='1' />
    <cms:repeatable name='rep_bar_alc_v2' label='Список Бар' group='grp_rep_bar'>
        <cms:editable name='item_tags' label='Тег' type='dropdown' opt_values='Нет=- | New | Hit | Special | Chef’s Choice | 🌶️ | 🌶️🌶️ | 🌶️🌶️🌶️' />
        <cms:editable name='row_type' label='Тип' type='dropdown' opt_values='Товар=item | Заголовок раздела=header | Подзаголовок раздела=subheader' />
        <cms:editable name='cat_title' label='Заголовок (RU)' type='text' />
        <cms:editable name='cat_title_en' label='Заголовок (EN)' type='text' />
        <cms:editable name='subcat_title' label='Подзаголовок (RU)' type='text' />
        <cms:editable name='subcat_title_en' label='Подзаголовок (EN)' type='text' />
        <cms:editable name='bar_alc_subtab' label='Категория' type='dropdown' opt_values='Пиво=beer | Вино=wine | Коктейли=cocktails | Крепкий алкоголь=spirits | Другое=other' />
        <cms:editable name='i_name' label='Название RU' type='text' />
        <cms:editable name='i_name_en' label='Название EN' type='text' />
        <cms:editable name='i_subheader' label='Подзаголовок товара RU' type='text' />
        <cms:editable name='i_subheader_en' label='Подзаголовок товара EN' type='text' />
        <cms:editable name='i_price' label='Цена' type='text' />
        <cms:editable name='note_after_ru' label='Примечание RU' type='textarea' height='30' />
        <cms:editable name='note_after_ru_en' label='Примечание EN' type='textarea' height='30' />
    </cms:repeatable>

    <cms:editable name='grp_rep_drinks' label='Содержание Напитки' type='group' order='7' collapsed='1' />
    <cms:repeatable name='rep_bar_non_v2' label='Список Напитки' group='grp_rep_drinks'>
        <cms:editable name='item_tags' label='Тег' type='dropdown' opt_values='Нет=- | New | Hit | Special | Chef’s Choice' />
        <cms:editable name='row_type' label='Тип' type='dropdown' opt_values='Товар=item | Заголовок раздела=header | Подзаголовок раздела=subheader' />
        <cms:editable name='cat_title' label='Заголовок (RU)' type='text' />
        <cms:editable name='cat_title_en' label='Заголовок (EN)' type='text' />
        <cms:editable name='subcat_title' label='Подзаголовок (RU)' type='text' />
        <cms:editable name='subcat_title_en' label='Подзаголовок (EN)' type='text' />
        <cms:editable name='drinks_subtab' label='Категория' type='dropdown' opt_values='Чай / Кофе=tea_coffee | Лемонады=lemonades | Другое=other' />
        <cms:editable name='i_name' label='Название RU' type='text' />
        <cms:editable name='i_name_en' label='Название EN' type='text' />
        <cms:editable name='i_desc' label='Описание RU' type='textarea' height='30' />
        <cms:editable name='i_desc_en' label='Описание EN' type='textarea' height='30' />
        <cms:editable name='i_price' label='Цена' type='text' />
        <cms:editable name='note_after_ru' label='Примечание RU' type='textarea' height='30' />
        <cms:editable name='note_after_ru_en' label='Примечание EN' type='textarea' height='30' />
    </cms:repeatable>

    <cms:editable name='grp_promo' label='Акции (English menu)' type='group' order='8' collapsed='1' hidden='1' />
    <cms:editable name='promo_title' type='text' group='grp_promo' label='Заголовок RU (fallback EN)' hidden='1'>Привилегии Garden Lounge</cms:editable>
    <cms:editable name='promo_title_en' type='text' group='grp_promo' label='Заголовок EN' hidden='1'>Garden Lounge Privileges</cms:editable>
    <cms:editable name='promo_subtitle' type='text' group='grp_promo' label='Подзаголовок RU (fallback EN)' hidden='1'>Специальные предложения</cms:editable>
    <cms:editable name='promo_subtitle_en' type='text' group='grp_promo' label='Подзаголовок EN' hidden='1'>Special Offers</cms:editable>
    <cms:editable name='promo_footer' type='text' group='grp_promo' label='Подвал RU (fallback EN)' hidden='1'>Идеальное место для ценителей прекрасного.</cms:editable>
    <cms:editable name='promo_footer_en' type='text' group='grp_promo' label='Подвал EN' hidden='1'>Ideal place for connoisseurs.</cms:editable>

    <cms:repeatable name='list_promos_v2' label='Список акций (English menu)' group='grp_promo' hidden='1'>
        <cms:editable name='p_title' type='text' label='Название RU (fallback EN)' hidden='1' /> <cms:editable name='p_title_en' type='text' label='Название EN' hidden='1' />
        <cms:editable name='p_desc' type='textarea' label='Описание RU (fallback EN)' hidden='1' /> <cms:editable name='p_desc_en' type='textarea' label='Описание EN' hidden='1' />
        <cms:editable name='p_tag' type='text' label='Тег RU (fallback EN)' hidden='1' /> <cms:editable name='p_tag_en' type='text' label='Тег EN' hidden='1' />
    </cms:repeatable>

    <cms:editable name='akzii_promo_note' type='message' order='7.5'>
        <p style="margin:0;padding:12px 15px;background:#f9f6ef;border:1px solid #C5A059;border-radius:4px;font-size:13px;color:#444;">
            Акции для RU и EN меню редактируются в разделе <strong>«Акции»</strong>. Английский перевод заполняется автоматически при открытии страницы акций (кнопка «Перевести» — повторить).
        </p>
    </cms:editable>

</cms:template>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/age-gate/assets.php'; gl_render_head_assets(); ?>
    <title>Меню Garden Lounge на Адмиралтейской — кальяны, кухня, бар</title>
    <meta name="description" content="<cms:if meta_desc><cms:show meta_desc /><cms:else />Меню Garden Lounge на Адмиралтейской: кальяны, кухня, бар, напитки и специальные предложения в лаунж-баре на наб. реки Мойки 67-69.</cms:if>">
    <link rel="canonical" href="https://garden-lounge.pro/admiralteyskaya/menu/text">
    <cms:php>
    global $CTX;
    require_once $_SERVER['DOCUMENT_ROOT'] . '/age-gate/menu-schema.php';
    $desc = trim((string) $CTX->get('meta_desc'));
    if ($desc === '') {
        $desc = 'Меню Garden Lounge на Адмиралтейской: кальяны, кухня, бар, напитки и специальные предложения.';
    }
    gl_menu_og_render(array(
        'branch' => 'admiralteyskaya',
        'url' => 'https://garden-lounge.pro/admiralteyskaya/menu/text',
        'title' => 'Меню Garden Lounge на Адмиралтейской — кальяны, кухня, бар',
        'description' => $desc,
    ));
    </cms:php>
    <?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/age-gate/menu-schema.php';
    gl_menu_seo_schema_render(array(
        'branch' => 'admiralteyskaya',
        'page' => 'text',
        'url' => 'https://garden-lounge.pro/admiralteyskaya/menu/text',
        'name' => 'Меню Garden Lounge на Адмиралтейской — кальяны, кухня, бар',
        'description' => 'Меню Garden Lounge на Адмиралтейской: кальяны, кухня, бар, напитки и специальные предложения.',
    ));
    ?>
    <?php gl_menu_page_head_assets(); ?>

    <style>
        body { background-color: #000; color: #fff; font-family: 'Montserrat', sans-serif; margin: 0; overflow-x: hidden; }
        .font-serif-lux { font-family: 'Cormorant Garamond', serif; }
        :root { --gold:#C5A059; --gold-dark:#8e7037; }

        @keyframes shineGold {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
        @-webkit-keyframes shineGold {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
        .gold-shimmer {
            background: linear-gradient(to right, var(--gold-dark) 0%, var(--gold) 40%, #FFEebb 50%, var(--gold) 60%, var(--gold-dark) 100%);
            background-size: 200% auto; -webkit-background-clip:text; background-clip:text;
            -webkit-text-fill-color: transparent; color: transparent;
            animation: shineGold 5s linear infinite; -webkit-animation: shineGold 5s linear infinite;
        }

        .nav-sticky { position: sticky; top:0; z-index:50; background-color: rgba(0,0,0,0.95); backdrop-filter: blur(10px); border-bottom: 1px solid #1a1a1a; }
        .gold-divider-nav { width:100%; height:1px; background: linear-gradient(90deg, transparent 0%, var(--gold) 50%, transparent 100%); opacity:0.8; margin-top: 6px; }

        .tab-btn { position: relative; transition: all .3s ease; color:#888; background:none; border:none; cursor:pointer; font-family: inherit; padding: 0; }
        .tab-btn.active { color: var(--gold); }
        .tab-btn.active::after { content:''; position:absolute; bottom:-4px; left:0; width:100%; height:2px; background: var(--gold); }
        @media (min-width: 768px) {
            .tabs-wrap .tab-btn { font-size: 11px; letter-spacing: 0.16em; padding-bottom: 7px; }
        }

        .tabs-wrap { display:flex; flex-wrap:wrap; justify-content:center; gap:14px 18px; padding: 14px 10px 8px; }
        .subtabs-wrap { display:none; flex-wrap:wrap; justify-content:center; gap:10px 12px; padding: 10px 10px 15px; }
        .subtab-btn { border: 1px solid rgba(197,160,89,0.35); border-radius: 999px; padding: 6px 12px; font-size: 11px; text-transform: uppercase; color: #d0d0d0; cursor:pointer; background: transparent; font-family: inherit; line-height: 1.2; }
        .subtab-btn.active { color: #000; background: var(--gold); }
        @media (max-width: 767px) {
            .subtabs-wrap { gap: 6px 8px; padding: 8px 10px 12px; }
            .subtab-btn { padding: 5px 10px; font-size: 10px; letter-spacing: 0.04em; line-height: 1.2; white-space: nowrap; }
            main.max-w-2xl { padding-top: 0.85rem !important; }
            .gold-divider-nav { margin-top: 4px; }
            .category-title { margin: 1rem 0 0.65rem; font-size: 1.65rem; padding-bottom: 0.35rem; }
            .subcat-title { margin: 0.65rem 0 0.4rem; letter-spacing: 0.22em; }
            .tab-content .w-full.pb-2.border-b { padding-bottom: 0.25rem !important; margin-bottom: 0.55rem !important; }
        }

        .tab-content { display:none; }
        .tab-content.active { display:block; animation: fadeIn .35s ease-out; }
        @keyframes fadeIn { from{ opacity:0; transform: translateY(8px);} to{opacity:1; transform: translateY(0);} }

        .category-title { font-family: 'Cormorant Garamond', serif; font-style: italic; font-weight: 400; font-size: 1.875rem; margin: 2.2rem 0 1.6rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: .5rem; text-align: center; }
        .subcat-title { font-weight:700; letter-spacing: .28em; text-transform: uppercase; font-size:.75rem; text-align:center; margin: 1.7rem 0 .9rem; }
        .price-tag { font-weight:500; font-size:1.25rem; white-space:nowrap; }

        .badge-container { display: inline-flex; gap: 4px; vertical-align: middle; margin-left: 8px; position: relative; top: -1px; }
        .badge-item {
            height: 18px !important;
            font-size: 12px !important;
            font-weight: 800 !important;
            text-transform: uppercase;
            letter-spacing: 0.01em;
            padding: 0 5px !important;
            border-radius: 2px;
            white-space: nowrap;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(197, 160, 89, 0.6);
            color: var(--gold);
            background: rgba(197, 160, 89, 0.1);
        }
        .badge-chef { background: var(--gold); color: #000; border: 1px solid var(--gold); }
        .badge-spicy { height: 18px; border: 1px solid rgba(197,160,89,0.34); background: linear-gradient(180deg, rgba(197,160,89,0.14), rgba(197,160,89,0.04)); font-size: 10px; padding: 0 5px; margin-left: 2px; border-radius: 999px; gap: 3px; display: inline-flex; align-items: center; line-height: 1; box-shadow: inset 0 0 8px rgba(197,160,89,0.08); }
        .badge-spicy i { color: #C5A059; filter: drop-shadow(0 0 4px rgba(197,160,89,0.28)); font-family: "Font Awesome 6 Free" !important; font-weight: 900; font-style: normal; }
        .badge-spicy .fa-solid::before { font-family: "Font Awesome 6 Free" !important; font-weight: 900; }

        .taplink-block-wrapper { width:100vw; position:relative; left:50%; margin-left:-50vw; background-color: #000; padding: 40px 0; overflow: hidden; }
        .content-limiter { max-width:600px; margin:0 auto; padding: 0 20px; position: relative; z-index: 10; }
        .film-grain { position:absolute; top:0; left:0; width:100%; height:100%; background:url('/img/noise.svg'); opacity:.04; pointer-events:none; z-index:1; }
        .promo-card { border:1px solid rgba(197,160,89,0.2); background-color: rgba(20,20,20,0.4); padding:20px; text-align:center; margin-bottom: 15px; }
        .gold-line-fade { width:160px; height:1px; background: linear-gradient(90deg, transparent, var(--gold), transparent); margin: 16px auto; }
        .shimmer-gold { background: linear-gradient(to right, #8e7037 0%, #C5A059 40%, #FFEebb 50%, #C5A059 60%, #8e7037 100%); background-size:200% auto; -webkit-text-fill-color:transparent; color:transparent; -webkit-background-clip:text; background-clip:text; animation: shineGold 5s linear infinite; -webkit-animation: shineGold 5s linear infinite; display:inline-block; }
        .promo-offer { font-size: 10px; line-height: 1.8; letter-spacing: 0.4em; text-transform: uppercase; font-weight: 500; margin: 0; }
        .akzii-footer-note { font-size: 10px; line-height: 1.8; letter-spacing: 0.3em; font-weight: 500; font-style: normal; margin: 0; }

        #loyalty-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(10px); display: none; justify-content: center; align-items: center; z-index: 3000; padding: 20px; }
        .modal-content { background: #0a0a0a; border: 1px solid var(--gold); padding: 40px 25px; width: 100%; max-width: 400px; text-align: center; position: relative; box-shadow: 0 0 30px rgba(197, 160, 89, 0.2); }
        .modal-title { font-family: 'Cormorant Garamond', serif; font-style: italic; font-size: 24px; margin-bottom: 25px; color: #fff; line-height: 1.2; }
        .modal-btn { display: flex; align-items: center; justify-content: center; gap: 12px; width: 100%; height: 54px; border: 1px solid rgba(197, 160, 89, 0.3); margin-bottom: 12px; color: #fff !important; text-decoration: none; text-transform: uppercase; font-size: 11px; letter-spacing: 0.1em; transition: 0.3s; }
        .modal-btn:hover { background: rgba(197, 160, 89, 0.1); border-color: var(--gold); }
        .modal-btn i { font-size: 18px; color: var(--gold); }
        .close-modal { position: absolute; top: 10px; right: 15px; font-size: 28px; color: rgba(255,255,255,0.3); cursor: pointer; line-height: 1; }
        .close-modal:hover { color: #fff; }

        .action-area { display: flex; flex-direction: column; align-items: center; gap: 10px; margin-top: 40px; }
        @media (min-width: 768px) { .action-area { flex-direction: row; justify-content: center; gap: 15px; } }
        .btn-base { display: flex; align-items: center; justify-content: center; width: 100%; max-width: 280px; height: 52px; border: 1px solid rgba(197,160,89,0.3); text-transform: uppercase; font-size: 10px; letter-spacing: 0.15em; text-decoration: none; transition: 0.3s; }
        .btn-gold-fill { background: var(--gold); color: #000; font-weight: 700; border: none; }
        .note-after { margin-top: 6px; font-size: 12px; color: #a9a9a9; line-height: 1.5; }
    </style>
</head>

<body>

<header class="py-8 text-center bg-black">
    <a href="https://garden-lounge.pro/admiralteyskaya/menu" class="inline-block">
        <img src="<cms:show site_logo />" alt="Logo" class="h-28 mx-auto">
    </a>
</header>

<div class="nav-sticky">
    <nav class="tabs-wrap">
        <button onclick="switchTab('hookahs')" class="tab-btn active uppercase font-bold tracking-widest text-xs"><cms:show lbl_tab_1 /></button>
        <button onclick="switchTab('kitchen')" class="tab-btn uppercase font-bold tracking-widest text-xs"><cms:show lbl_tab_2 /></button>
        <button onclick="switchTab('bar-alc')" class="tab-btn uppercase font-bold tracking-widest text-xs"><cms:show lbl_tab_3 /></button>
        <button onclick="switchTab('bar-non')" class="tab-btn uppercase font-bold tracking-widest text-xs"><cms:show lbl_tab_4 /></button>
        <button onclick="switchTab('promos')" class="tab-btn uppercase font-bold tracking-widest text-xs"><cms:show lbl_tab_5 /></button>
    </nav>

    <div id="subtabs-kitchen" class="subtabs-wrap">
        <button class="subtab-btn active" data-sub="snacks" onclick="switchSubtab('kitchen','snacks')"><cms:show kt_sub_snacks /></button>
        <button class="subtab-btn" data-sub="salads" onclick="switchSubtab('kitchen','salads')"><cms:show kt_sub_salads /></button>
        <button class="subtab-btn" data-sub="rolls" onclick="switchSubtab('kitchen','rolls')"><cms:show kt_sub_rolls /></button>
        <button class="subtab-btn" data-sub="soups" onclick="switchSubtab('kitchen','soups')"><cms:show kt_sub_soups /></button>
        <button class="subtab-btn" data-sub="poke_bowl_wok" onclick="switchSubtab('kitchen','poke_bowl_wok')"><cms:show kt_sub_poke /></button>
        <button class="subtab-btn" data-sub="hot" onclick="switchSubtab('kitchen','hot')"><cms:show kt_sub_hot /></button>
        <button class="subtab-btn" data-sub="desserts" onclick="switchSubtab('kitchen','desserts')"><cms:show kt_sub_desserts /></button>
        <button class="subtab-btn" data-sub="other" onclick="switchSubtab('kitchen','other')">Ланчи</button>
    </div>

    <div id="subtabs-bar-alc" class="subtabs-wrap">
        <button class="subtab-btn active" data-sub="beer" onclick="switchSubtab('bar-alc','beer')"><cms:show bt_sub_beer /></button>
        <button class="subtab-btn" data-sub="wine" onclick="switchSubtab('bar-alc','wine')"><cms:show bt_sub_wine /></button>
        <button class="subtab-btn" data-sub="cocktails" onclick="switchSubtab('bar-alc','cocktails')"><cms:show bt_sub_cocktails /></button>
        <button class="subtab-btn" data-sub="spirits" onclick="switchSubtab('bar-alc','spirits')"><cms:show bt_sub_strong /></button>
    </div>

    <div id="subtabs-bar-non" class="subtabs-wrap">
        <button class="subtab-btn active" data-sub="tea_coffee" onclick="switchSubtab('bar-non','tea_coffee')"><cms:show dt_sub_tea /></button>
        <button class="subtab-btn" data-sub="lemonades" onclick="switchSubtab('bar-non','lemonades')"><cms:show dt_sub_lemon /></button>
        <button class="subtab-btn" data-sub="other" onclick="switchSubtab('bar-non','other')">Напитки</button>
    </div>
    <div class="gold-divider-nav"></div>
</div>

<main class="max-w-2xl mx-auto px-6 py-10 pb-24 min-h-screen">

    <div id="hookahs" class="tab-content active">
        <cms:show_repeatable 'rep_hookahs_v2'>
            <div class="menu-row" data-subtab="all">
                <cms:if row_type='header'><h3 class="category-title gold-shimmer"><cms:show cat_title /></h3>
                <cms:else_if row_type='subheader' /><h4 class="subcat-title gold-shimmer"><cms:show subcat_title /></h4>
                <cms:else />
                    <div class="w-full pb-2 border-b border-white/5 mb-4">
                        <div class="grid grid-cols-[1fr_auto] gap-x-4 items-start">
                            <div class="text-white text-lg flex items-center flex-wrap">
                                <cms:show i_name />
                                <div class="badge-container">
                                    <cms:if item_tags='New' || item_tags='New + 🌶️'><span class="badge-item">New</span></cms:if>
                                    <cms:if item_tags='Hit' || item_tags='Hit + 🌶️'><span class="badge-item">Hit</span></cms:if>
                                    <cms:if item_tags='Special'><span class="badge-item">Special</span></cms:if>
                                    <cms:if item_tags='Chef’s Choice'><span class="badge-item badge-chef">Chef</span></cms:if>
                                    <cms:if item_tags='🌶️' || item_tags='New + 🌶️' || item_tags='Hit + 🌶️'><span class="badge-spicy"><i class="fa-solid fa-pepper-hot"></i></span></cms:if>
                                    <cms:if item_tags='🌶️🌶️'><span class="badge-spicy"><i class="fa-solid fa-pepper-hot"></i><i class="fa-solid fa-pepper-hot"></i></span></cms:if>
                                    <cms:if item_tags='🌶️🌶️🌶️'><span class="badge-spicy"><i class="fa-solid fa-pepper-hot"></i><i class="fa-solid fa-pepper-hot"></i><i class="fa-solid fa-pepper-hot"></i></span></cms:if>
                                </div>
                            </div>
                            <span class="price-tag gold-shimmer"><cms:show i_price /> ₽</span>
                            <cms:if i_desc><div class="col-span-2 text-[12px] text-gray-400 mt-1"><cms:show i_desc /></div></cms:if>
                        </div>
                        <cms:if note_after_ru><div class="note-after"><cms:show note_after_ru /></div></cms:if>
                    </div>
                </cms:if>
            </div>
        </cms:show_repeatable>
    </div>

    <div id="kitchen" class="tab-content">
        <cms:show_repeatable 'rep_kitchen_v2'>
            <cms:set _st = kitchen_subtab />
            <div class="menu-row" data-subtab="<cms:if _st><cms:show _st /><cms:else />other</cms:if>">
                <cms:if row_type='header'><h3 class="category-title gold-shimmer"><cms:show cat_title /></h3>
                <cms:else_if row_type='subheader' /><h4 class="subcat-title gold-shimmer"><cms:show subcat_title /></h4>
                <cms:else />
                    <div class="w-full pb-2 border-b border-white/5 mb-4">
                        <div class="grid grid-cols-[1fr_auto] gap-x-4 items-start">
                            <div class="text-white text-lg flex items-center flex-wrap">
                                <cms:show kit_name />
                                <div class="badge-container">
                                    <cms:if item_tags='New' || item_tags='New + 🌶️'><span class="badge-item">New</span></cms:if>
                                    <cms:if item_tags='Hit' || item_tags='Hit + 🌶️'><span class="badge-item">Hit</span></cms:if>
                                    <cms:if item_tags='Special'><span class="badge-item">Special</span></cms:if>
                                    <cms:if item_tags='Chef’s Choice'><span class="badge-item badge-chef">Chef</span></cms:if>
                                    <cms:if item_tags='🌶️' || item_tags='New + 🌶️' || item_tags='Hit + 🌶️'><span class="badge-spicy"><i class="fa-solid fa-pepper-hot"></i></span></cms:if>
                                    <cms:if item_tags='🌶️🌶️'><span class="badge-spicy"><i class="fa-solid fa-pepper-hot"></i><i class="fa-solid fa-pepper-hot"></i></span></cms:if>
                                    <cms:if item_tags='🌶️🌶️🌶️'><span class="badge-spicy"><i class="fa-solid fa-pepper-hot"></i><i class="fa-solid fa-pepper-hot"></i><i class="fa-solid fa-pepper-hot"></i></span></cms:if>
                                </div>
                            </div>
                            <span class="price-tag gold-shimmer"><cms:show kit_price /> ₽</span>
                            <cms:if kit_desc><div class="col-span-2 text-[12px] text-gray-400 mt-1"><cms:show kit_desc /></div></cms:if>
                        </div>
                        <cms:if note_after_ru><div class="note-after"><cms:show note_after_ru /></div></cms:if>
                    </div>
                </cms:if>
            </div>
        </cms:show_repeatable>
    </div>

    <div id="bar-alc" class="tab-content">
        <cms:show_repeatable 'rep_bar_alc_v2'>
            <cms:set _st = bar_alc_subtab />
            <div class="menu-row" data-subtab="<cms:if _st><cms:show _st /><cms:else />other</cms:if>">
                <cms:if row_type='header'><h3 class="category-title gold-shimmer"><cms:show cat_title /></h3>
                <cms:else_if row_type='subheader' /><h4 class="subcat-title gold-shimmer"><cms:show subcat_title /></h4>
                <cms:else />
                    <div class="w-full pb-2 border-b border-white/5 mb-4">
                        <div class="grid grid-cols-[1fr_auto] gap-x-4 items-start">
                            <div class="text-white text-lg flex items-center flex-wrap">
                                <cms:show i_name />
                                <div class="badge-container">
                                    <cms:if item_tags='New'><span class="badge-item">New</span></cms:if>
                                    <cms:if item_tags='Hit'><span class="badge-item">Hit</span></cms:if>
                                    <cms:if item_tags='Special'><span class="badge-item">Special</span></cms:if>
                                    <cms:if item_tags='Chef’s Choice'><span class="badge-item badge-chef">Chef</span></cms:if>
                                </div>
                            </div>
                            <span class="price-tag gold-shimmer"><cms:show i_price /> ₽</span>
                            <cms:if i_subheader><div class="col-span-2 text-[12px] text-gray-400 mt-1"><cms:show i_subheader /></div></cms:if>
                        </div>
                        <cms:if note_after_ru><div class="note-after"><cms:show note_after_ru /></div></cms:if>
                    </div>
                </cms:if>
            </div>
        </cms:show_repeatable>
    </div>

    <div id="bar-non" class="tab-content">
        <cms:show_repeatable 'rep_bar_non_v2'>
            <cms:set _st = drinks_subtab />
            <div class="menu-row" data-subtab="<cms:if _st><cms:show _st /><cms:else />other</cms:if>">
                <cms:if row_type='header'><h3 class="category-title gold-shimmer"><cms:show cat_title /></h3>
                <cms:else_if row_type='subheader' /><h4 class="subcat-title gold-shimmer"><cms:show subcat_title /></h4>
                <cms:else />
                    <div class="w-full pb-2 border-b border-white/5 mb-4">
                        <div class="grid grid-cols-[1fr_auto] gap-x-4 items-start">
                            <div class="text-white text-lg flex items-center flex-wrap">
                                <cms:show i_name />
                                <div class="badge-container">
                                    <cms:if item_tags='New'><span class="badge-item">New</span></cms:if>
                                    <cms:if item_tags='Hit'><span class="badge-item">Hit</span></cms:if>
                                    <cms:if item_tags='Special'><span class="badge-item">Special</span></cms:if>
                                    <cms:if item_tags='Chef’s Choice'><span class="badge-item badge-chef">Chef</span></cms:if>
                                </div>
                            </div>
                            <span class="price-tag gold-shimmer"><cms:show i_price /> ₽</span>
                            <cms:if i_desc><div class="col-span-2 text-[12px] text-gray-400 mt-1"><cms:show i_desc /></div></cms:if>
                        </div>
                        <cms:if note_after_ru><div class="note-after"><cms:show note_after_ru /></div></cms:if>
                    </div>
                </cms:if>
            </div>
        </cms:show_repeatable>
    </div>

    <div id="promos" class="tab-content">
        <div class="taplink-block-wrapper">
            <div class="film-grain"></div>
            <div class="content-limiter">
                <cms:pages masterpage='akzii.php' limit='1'>
                    <cms:embed 'promos-menu-block.html' />
                </cms:pages>
            </div>
        </div>
    </div>

    <div class="action-area">
        <a href="https://garden-lounge.pro/admiralteyskaya/menu" class="btn-base"><span class="subtitle-gold">Вернуться Назад</span></a>
        <div onclick="openLoyaltyModal()" class="btn-base btn-gold-fill">Программа лояльности</div>
        <a href="https://garden-lounge.pro/admiralteyskaya/menu/visual" class="btn-base"><span class="subtitle-gold">Визуальное меню</span></a>
    </div>

</main>

<cms:set sticky_masterpage='sticky-sticker.php' scope='global' />
<cms:embed 'sticky-sticker.html' />

<div id="loyalty-modal" onclick="closeLoyaltyModal()">
    <div class="modal-content" onclick="event.stopPropagation()">
        <span class="close-modal" onclick="closeLoyaltyModal()">&times;</span>
        <div class="modal-title gold-shimmer">Выберите способ регистрации</div>

        <a href="https://access.clientomer.ru/feedback/676900-1/" target="_blank" class="modal-btn">
            <i class="fa-solid fa-wallet"></i> Регистрация через Wallet
        </a>

        <a href="https://t.me/GardenLounge_Loyalty_Bot" target="_blank" class="modal-btn">
            <i class="fa-brands fa-telegram"></i> Регистрация через Telegram
        </a>
    </div>
</div>

<script>
    const SUBTABS = { 'kitchen': 'subtabs-kitchen', 'bar-alc': 'subtabs-bar-alc', 'bar-non': 'subtabs-bar-non' };
    const ACTIVE = { 'kitchen': 'snacks', 'bar-alc': 'beer', 'bar-non': 'tea_coffee' };

    function scrollMenuToTop() {
        const anchor = document.querySelector('.nav-sticky') || document.querySelector('main.max-w-2xl');
        if (!anchor) {
            window.scrollTo(0, 0);
            return;
        }
        const run = () => {
            const top = Math.max(0, anchor.getBoundingClientRect().top + window.pageYOffset);
            const scroller = document.scrollingElement || document.documentElement;
            scroller.scrollTop = top;
            window.scrollTo(0, top);
        };
        run();
        requestAnimationFrame(run);
    }

    function switchTab(id, scrollTop = true) {
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        const target = document.getElementById(id);
        if(target) target.classList.add('active');

        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        const btn = Array.from(document.querySelectorAll('.tab-btn')).find(b => b.getAttribute('onclick').includes(id));
        if(btn) btn.classList.add('active');

        document.querySelectorAll('.subtabs-wrap').forEach(w => w.style.display = 'none');
        if(SUBTABS[id]) document.getElementById(SUBTABS[id]).style.display = 'flex';
        filter(id);
        if (scrollTop) scrollMenuToTop();
    }

    function switchSubtab(tab, sub) {
        ACTIVE[tab] = sub;
        const wrap = document.getElementById(SUBTABS[tab]);
        wrap.querySelectorAll('.subtab-btn').forEach(b => b.classList.remove('active'));
        const activeSubBtn = Array.from(wrap.querySelectorAll('.subtab-btn')).find(b => b.getAttribute('data-sub') === sub);
        if(activeSubBtn) activeSubBtn.classList.add('active');
        filter(tab);
        scrollMenuToTop();
    }

    function filter(tab) {
        const sub = ACTIVE[tab];
        const container = document.getElementById(tab);
        if(!container) return;
        const rows = container.querySelectorAll('.menu-row');
        rows.forEach(r => {
            const v = r.getAttribute('data-subtab');
            r.style.display = (tab === 'hookahs' || tab === 'promos' || v === sub) ? 'block' : 'none';
        });
    }

    function openLoyaltyModal() {
        document.getElementById('loyalty-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeLoyaltyModal() {
        document.getElementById('loyalty-modal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    document.addEventListener('DOMContentLoaded', () => { switchTab('hookahs', false); });
</script>

</body>
<?php COUCH::invoke(); ?>
</html>
