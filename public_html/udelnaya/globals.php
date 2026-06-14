<?php
define('K_TEMPLATE_NAME', 'udelnaya/globals.php');
$garden_cms = null;
foreach ([
    __DIR__ . '/admiralteyskaya/couch/cms.php',
    __DIR__ . '/../admiralteyskaya/couch/cms.php',
    __DIR__ . '/../../admiralteyskaya/couch/cms.php',
    __DIR__ . '/../../../admiralteyskaya/couch/cms.php',
    __DIR__ . '/../../../../admiralteyskaya/couch/cms.php',
    __DIR__ . '/../couch/cms.php',
    __DIR__ . '/../../couch/cms.php',
    __DIR__ . '/../../../couch/cms.php',
    __DIR__ . '/../../../../couch/cms.php',
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
<cms:template title='РќР°СЃС‚СЂРѕР№РєРё СЃР°Р№С‚Р° (Р¤СѓС‚РµСЂ Рё SEO)' executable='0'>

    <!-- Р“Р РЈРџРџРђ: РћР‘Р©РђРЇ РРќР¤РћР РњРђР¦РРЇ -->
    <cms:editable name='group_general' label='РћР±С‰Р°СЏ РёРЅС„РѕСЂРјР°С†РёСЏ' type='group' />
        <cms:editable name='site_description_text' label='РћРїРёСЃР°РЅРёРµ Р±СЂРµРЅРґР° (РІ С„СѓС‚РµСЂРµ)' group='group_general' type='textarea' height='100'>РњР°РіРёС‡РµСЃРєРёР№ РІРµС‡РЅРѕР·РµР»РµРЅС‹Р№ СЃР°Рґ, СЃРєСЂС‹С‚С‹Р№ РѕС‚ РіРѕСЂРѕРґСЃРєРѕР№ СЃСѓРµС‚С‹ РІ СЃР°РјРѕРј СЃРµСЂРґС†Рµ РџРµС‚РµСЂР±СѓСЂРіР°.</cms:editable>

    <!-- Р“Р РЈРџРџРђ: РЎРћР¦ РЎР•РўР -->
    <cms:editable name='group_socials' label='РЎРѕС†РёР°Р»СЊРЅС‹Рµ СЃРµС‚Рё' type='group' />
        <cms:editable name='link_instagram' label='Instagram (СЃСЃС‹Р»РєР°)' group='group_socials' type='text'>https://instagram.com/garden_lounge_spb/</cms:editable>
        <cms:editable name='link_telegram' label='Telegram РєР°РЅР°Р» (СЃСЃС‹Р»РєР°)' group='group_socials' type='text'>https://t.me/Garden_lounge_spb</cms:editable>
        <cms:editable name='link_vk' label='VK (СЃСЃС‹Р»РєР°)' group='group_socials' type='text'>https://vk.com/loungegarden</cms:editable>
        <cms:editable name='link_youtube' label='YouTube (СЃСЃС‹Р»РєР°)' group='group_socials' type='text'>https://youtube.com/@garden.lounge</cms:editable>

    <!-- Р“Р РЈРџРџРђ: Р¤РР›РРђР› РђР”РњРР РђР›РўР•Р™РЎРљРђРЇ -->
    <cms:editable name='group_admiral' label='Р¤РёР»РёР°Р»: РђРґРјРёСЂР°Р»С‚РµР№СЃРєР°СЏ' type='group' />
        <cms:editable name='admiral_address' label='РђРґСЂРµСЃ' group='group_admiral' type='text'>РќР°Р±. СЂРµРєРё РњРѕР№РєРё 67-69</cms:editable>
        <cms:editable name='admiral_map_link' label='РЎСЃС‹Р»РєР° РЅР° РЇРЅРґРµРєСЃ.РљР°СЂС‚С‹' group='group_admiral' type='text'>https://yandex.ru/maps/-/CLBURN0n</cms:editable>
        <cms:editable name='admiral_phone' label='РўРµР»РµС„РѕРЅ (РєР°Рє РѕС‚РѕР±СЂР°Р¶Р°РµС‚СЃСЏ)' group='group_admiral' type='text'>+7 995 624-68-08</cms:editable>
        <cms:editable name='admiral_phone_clean' label='РўРµР»РµС„РѕРЅ РґР»СЏ СЃСЃС‹Р»РєРё (Р±РµР· РїСЂРѕР±РµР»РѕРІ, +7...)' group='group_admiral' type='text'>+79956246808</cms:editable>
        <cms:editable name='admiral_tg_chat' label='РЎСЃС‹Р»РєР° РЅР° С‡Р°С‚ Telegram' group='group_admiral' type='text'>https://t.me/Garden_lounge_spb</cms:editable>
        <cms:editable name='admiral_hours_week' label='Р§Р°СЃС‹ СЂР°Р±РѕС‚С‹ (РџРЅ-Р§С‚, Р’СЃ)' group='group_admiral' type='text'>РџРЅвЂ“Р§С‚; Р’СЃ: 12:00 вЂ“ 01:00</cms:editable>
        <cms:editable name='admiral_hours_weekend' label='Р§Р°СЃС‹ СЂР°Р±РѕС‚С‹ (РџС‚-РЎР±)' group='group_admiral' type='text'>РџС‚вЂ“РЎР±: 12:00 вЂ“ 03:00</cms:editable>

    <!-- Р“Р РЈРџРџРђ: Р¤РР›РРђР› РЈР”Р•Р›Р¬РќРђРЇ -->
    <cms:editable name='group_udelnaya' label='Р¤РёР»РёР°Р»: РЈРґРµР»СЊРЅР°СЏ' type='group' />
        <cms:editable name='udel_address' label='РђРґСЂРµСЃ' group='group_udelnaya' type='text'>РЈР». РђРєРєСѓСЂР°С‚РѕРІР° 13</cms:editable>
        <cms:editable name='udel_map_link' label='РЎСЃС‹Р»РєР° РЅР° РЇРЅРґРµРєСЃ.РљР°СЂС‚С‹' group='group_udelnaya' type='text'>https://yandex.ru/maps/-/CPE-mNm0</cms:editable>
        <cms:editable name='udel_phone' label='РўРµР»РµС„РѕРЅ (РєР°Рє РѕС‚РѕР±СЂР°Р¶Р°РµС‚СЃСЏ)' group='group_udelnaya' type='text'>+7 995 624-68-08</cms:editable>
        <cms:editable name='udel_phone_clean' label='РўРµР»РµС„РѕРЅ РґР»СЏ СЃСЃС‹Р»РєРё (Р±РµР· РїСЂРѕР±РµР»РѕРІ, +7...)' group='group_udelnaya' type='text'>+79956246808</cms:editable>
        <cms:editable name='udel_tg_chat' label='РЎСЃС‹Р»РєР° РЅР° С‡Р°С‚ Telegram' group='group_udelnaya' type='text'>https://t.me/Garden_lounge_spb</cms:editable>
        <cms:editable name='udel_hours_week' label='Р§Р°СЃС‹ СЂР°Р±РѕС‚С‹ (РџРЅ-Р§С‚, Р’СЃ)' group='group_udelnaya' type='text'>РџРЅвЂ“Р§С‚; Р’СЃ: 12:00 вЂ“ 01:00</cms:editable>
        <cms:editable name='udel_hours_weekend' label='Р§Р°СЃС‹ СЂР°Р±РѕС‚С‹ (РџС‚-РЎР±)' group='group_udelnaya' type='text'>РџС‚вЂ“РЎР±: 12:00 вЂ“ 03:00</cms:editable>

    <!-- Р“Р РЈРџРџРђ: SEO РџРћ РЈРњРћР›Р§РђРќРР® -->
    <cms:editable name='group_seo' label='SEO РїРѕ СѓРјРѕР»С‡Р°РЅРёСЋ' type='group' />
        <cms:editable name='seo_title_default' label='Р—Р°РіРѕР»РѕРІРѕРє (Title)' group='group_seo' type='text'>Garden Lounge РЅР° РђРґРјРёСЂР°Р»С‚РµР№СЃРєРѕР№ вЂ” РєР°Р»СЊСЏРЅРЅР°СЏ Рё Р»Р°СѓРЅР¶-Р±Р°СЂ РІ С†РµРЅС‚СЂРµ РЎРџР±</cms:editable>
        <cms:editable name='seo_desc_default' label='РћРїРёСЃР°РЅРёРµ (Description)' group='group_seo' type='textarea'>Garden Lounge РЅР° РЅР°Р±. СЂРµРєРё РњРѕР№РєРё 67-69: РїСЂРµРјРёР°Р»СЊРЅС‹Рµ РєР°Р»СЊСЏРЅС‹, РєСѓС…РЅСЏ, VIP-РєРѕРјРЅР°С‚С‹ Рё Р±СЂРѕРЅСЊ СЃС‚РѕР»РёРєР° СЂСЏРґРѕРј СЃ РјРµС‚СЂРѕ РђРґРјРёСЂР°Р»С‚РµР№СЃРєР°СЏ. РўРµР». +7 995 624-68-08.</cms:editable>
        <cms:editable name='seo_keywords_default' label='РљР»СЋС‡РµРІС‹Рµ СЃР»РѕРІР°' group='group_seo' type='textarea'>Garden Lounge, РєР°Р»СЊСЏРЅРЅР°СЏ РЎРџР±, РєР°Р»СЊСЏРЅРЅР°СЏ Сѓ РђРґРјРёСЂР°Р»С‚РµР№СЃРєРѕР№, Р»Р°СѓРЅР¶ Р±Р°СЂ РЎРџР±, РєР°Р»СЊСЏРЅРЅР°СЏ РІ С†РµРЅС‚СЂРµ РЎРџР±, VIP РєР°Р»СЊСЏРЅРЅР°СЏ РЎРџР±, РєР°Р»СЊСЏРЅРЅР°СЏ СЃ РєСѓС…РЅРµР№, lounge bar, hookah bar, РєР°Р»СЊСЏРЅРЅР°СЏ РІ С†РµРЅС‚СЂРµ, РєР°Р»СЊСЏРЅРЅР°СЏ СЃ РЅРµРѕР±С‹С‡РЅС‹Рј РёРЅС‚РµСЂСЊРµСЂРѕРј, РЅР°Р±РµСЂРµР¶РЅР°СЏ СЂРµРєРё РњРѕР№РєРё 67-69, РђРґРјРёСЂР°Р»С‚РµР№СЃРєР°СЏ, VIP-РєРѕРјРЅР°С‚С‹, PS5, РєСѓС…РЅСЏ</cms:editable>
        <cms:editable name='seo_image_default' label='РљР°СЂС‚РёРЅРєР° РґР»СЏ СЃРѕС†СЃРµС‚РµР№ (OG Image)' group='group_seo' type='image'>https://garden-lounge.pro/udelnaya/couch/uploads/image/garden-main.jpg</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>


