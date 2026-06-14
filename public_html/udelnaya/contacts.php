<?php
define('K_TEMPLATE_NAME', 'udelnaya/contacts.php');
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
<cms:template title='РЈРїСЂР°РІР»РµРЅРёРµ: РљРѕРЅС‚Р°РєС‚С‹' name='contacts_section' executable='0' order='60'>
    
    <cms:editable name='cont_group_main' label='РћСЃРЅРѕРІРЅР°СЏ РёРЅС„РѕСЂРјР°С†РёСЏ' type='group' />
    <cms:editable name='cont_address' label='РђРґСЂРµСЃ' group='cont_group_main' type='text'>РЎРџР±. РЅР°Р±. СЂРµРєРё РњРѕР№РєРё, Рґ.67-69</cms:editable>
    <cms:editable name='cont_map_link' label='РЎСЃС‹Р»РєР° РЅР° РєР°СЂС‚С‹ (РјР°СЂС€СЂСѓС‚)' group='cont_group_main' type='text'>https://yandex.ru/maps/org/garden_lounge/92097430496/</cms:editable>
    <cms:editable name='cont_phone' label='РўРµР»РµС„РѕРЅ' group='cont_group_main' type='text'>+7 995 624-68-08</cms:editable>
    <cms:editable name='cont_phone_link' label='РўРµР»РµС„РѕРЅ (РґР»СЏ СЃСЃС‹Р»РєРё Р±РµР· РїСЂРѕР±РµР»РѕРІ)' group='cont_group_main' type='text'>+79956246808</cms:editable>

    <cms:editable name='cont_group_hours' label='Р§Р°СЃС‹ СЂР°Р±РѕС‚С‹' type='group' />
    <cms:editable name='cont_hours_1' label='РџРЅвЂ“Р§С‚; Р’СЃ' group='cont_group_hours' type='text'>12:00 вЂ“ 01:00</cms:editable>
    <cms:editable name='cont_hours_2' label='РџС‚вЂ“РЎР±' group='cont_group_hours' type='text'>12:00 вЂ“ 03:00</cms:editable>

    <cms:editable name='cont_group_social' label='РЎРѕС†РёР°Р»СЊРЅС‹Рµ СЃРµС‚Рё' type='group' />
    <cms:editable name='cont_whatsapp' label='WhatsApp (РЅРѕРјРµСЂ РёР»Рё СЃСЃС‹Р»РєР°)' group='cont_group_social' type='text'>https://wa.me/79956246808</cms:editable>
    <cms:editable name='cont_telegram' label='Telegram (СЃСЃС‹Р»РєР°)' group='cont_group_social' type='text'>https://t.me/Garden_lounge_spb</cms:editable>

    <cms:editable name='cont_group_ratings' label='Р РµР№С‚РёРЅРіРё (С†РёС„СЂС‹ Рё СЃСЃС‹Р»РєРё)' type='group' />
    <cms:editable name='rate_yandex_val' label='РЇРЅРґРµРєСЃ (Р±Р°Р»Р»)' group='cont_group_ratings' type='text'>5.0</cms:editable>
    <cms:editable name='rate_yandex_count' label='РЇРЅРґРµРєСЃ (РєРѕР»-РІРѕ РѕС‚Р·С‹РІРѕРІ)' group='cont_group_ratings' type='text'>480+ РѕС‚Р·С‹РІРѕРІ</cms:editable>
    <cms:editable name='rate_yandex_link' label='Google (СЃСЃС‹Р»РєР°)' group='cont_group_ratings' type='text'>https://yandex.ru/maps/org/garden_lounge/92097430496/reviews/</cms:editable>
   
    <cms:editable name='rate_2gis_val' label='2GIS (Р±Р°Р»Р»)' group='cont_group_ratings' type='text'>4.9</cms:editable>
    <cms:editable name='rate_2gis_count' label='2GIS (РєРѕР»-РІРѕ РѕС‚Р·С‹РІРѕРІ)' group='cont_group_ratings' type='text'>131+ РѕС‚Р·С‹РІ</cms:editable>
    <cms:editable name='rate_2gis_link' label='Google (СЃСЃС‹Р»РєР°)' group='cont_group_ratings' type='text'>https://2gis.ru/spb/firm/70000001089303834/tab/reviews</cms:editable>
    
<cms:editable name='rate_google_val' label='Google (Р±Р°Р»Р»)' group='cont_group_ratings' type='text'>4.7</cms:editable>
    <cms:editable name='rate_google_count' label='Google (РѕС‚Р·С‹РІС‹)' group='cont_group_ratings' type='text'>13 РѕС‚Р·С‹РІРѕРІ</cms:editable>
    <cms:editable name='rate_google_link' label='Google (СЃСЃС‹Р»РєР°)' group='cont_group_ratings' type='text'>https://maps.app.goo.gl/rcwMbaXdfrbSTowd7</cms:editable>
  
    <cms:editable name='cont_map_iframe' label='РљРѕРґ РІРёРґР¶РµС‚Р° РєР°СЂС‚С‹ (С‚РѕР»СЊРєРѕ URL РёР· src)' group='cont_group_main' type='text'>https://yandex.ru/map-widget/v1/?um=constructor%3A86b78088cdeb728323715abfc506cc9bb4ed50969d2f614eda2941d37b52b3c5&source=constructor</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>

