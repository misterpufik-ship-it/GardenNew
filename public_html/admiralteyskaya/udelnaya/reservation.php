<?php
define('K_TEMPLATE_NAME', 'udelnaya/reservation.php');
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
<cms:template title='РЈРїСЂР°РІР»РµРЅРёРµ: Р‘СЂРѕРЅРёСЂРѕРІР°РЅРёРµ' name='reservation_section' executable='0' order='50'>
    
    <cms:editable name='res_group_titles' label='Р—Р°РіРѕР»РѕРІРєРё СЃРµРєС†РёРё' type='group' />
    <cms:editable name='res_title' label='Р“Р»Р°РІРЅС‹Р№ Р·Р°РіРѕР»РѕРІРѕРє' group='res_group_titles' type='text'>Reservation</cms:editable>
    <cms:editable name='res_subtitle' label='РџРѕРґР·Р°РіРѕР»РѕРІРѕРє' group='res_group_titles' type='text'>Р—Р°Р±СЂРѕРЅРёСЂРѕРІР°С‚СЊ СЃС‚РѕР»РёРє</cms:editable>

    <cms:editable name='res_group_modal' label='РўРµРєСЃС‚С‹ РїРѕСЃР»Рµ РѕС‚РїСЂР°РІРєРё' type='group' />
    <cms:editable name='res_modal_title' label='Р—Р°РіРѕР»РѕРІРѕРє РјРѕРґР°Р»СЊРЅРѕРіРѕ РѕРєРЅР°' group='res_group_modal' type='text'>РЎРїР°СЃРёР±Рѕ!</cms:editable>
    <cms:editable name='res_modal_text' label='РўРµРєСЃС‚ РїРѕРґС‚РІРµСЂР¶РґРµРЅРёСЏ' group='res_group_modal' type='textarea'>Р’Р°С€Рµ Р±СЂРѕРЅРёСЂРѕРІР°РЅРёРµ РїСЂРёРЅСЏС‚Рѕ. РђРґРјРёРЅРёСЃС‚СЂР°С‚РѕСЂ СЃРІСЏР¶РµС‚СЃСЏ СЃ Р’Р°РјРё РґР»СЏ РїРѕРґС‚РІРµСЂР¶РґРµРЅРёСЏ.</cms:editable>

    <cms:editable name='res_group_decor' label='Р”РµРєРѕСЂ' type='group' />
    <cms:editable name='res_sep' label='РљР°СЂС‚РёРЅРєР° СЂР°Р·РґРµР»РёС‚РµР»СЏ' group='res_group_decor' type='image'>:div.webp</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>


