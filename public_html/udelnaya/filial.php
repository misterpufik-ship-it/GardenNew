<?php
define('K_TEMPLATE_NAME', 'udelnaya/filial.php');
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
<cms:template title='РЈРїСЂР°РІР»РµРЅРёРµ: Р¤РёР»РёР°Р»' name='final_section' executable='0' order='70'>

    <cms:editable name='final_group_info' label='РРЅС„РѕСЂРјР°С†РёСЏ Рѕ С„РёР»РёР°Р»Рµ' type='group' />
    <cms:editable name='final_title' label='РќР°Р·РІР°РЅРёРµ С„РёР»РёР°Р»Р°' group='final_group_info' type='text'>Garden Lounge Udelnaya</cms:editable>
    <cms:editable name='final_subtitle' label='РџРѕРґР·Р°РіРѕР»РѕРІРѕРє' group='final_group_info' type='text'>Р’С‚РѕСЂРѕР№ С„РёР»РёР°Р» С‚Р°Р№РЅРѕРіРѕ СЃР°РґР°</cms:editable>
    <cms:editable name='final_img' label='РћР±Р»РѕР¶РєР° С„РёР»РёР°Р»Р°' group='final_group_info' type='image'>https://garden-lounge.pro/img/akkuratova.webp</cms:editable>

    <cms:repeatable name='final_gallery' label='Р“Р°Р»РµСЂРµСЏ С„РёР»РёР°Р»Р°' group='final_group_info'>
        <cms:editable name='final_gallery_img' label='Р¤РѕС‚Рѕ' type='image' />
        <cms:editable name='final_gallery_alt' label='РџРѕРґРїРёСЃСЊ / alt' type='text' />
    </cms:repeatable>

    <cms:editable name='final_address' label='РђРґСЂРµСЃ' group='final_group_info' type='text'>РЎРџР±., СѓР». РђРєРєСѓСЂР°С‚РѕРІР°, Рґ. 13</cms:editable>
    <cms:editable name='final_metro' label='РњРµС‚СЂРѕ' group='final_group_info' type='text'>Рј. РЈРґРµР»СЊРЅР°СЏ</cms:editable>

    <cms:editable name='final_group_btn' label='РљРЅРѕРїРєР° РґРµР№СЃС‚РІРёСЏ' type='group' />
    <cms:editable name='final_btn_text' label='РўРµРєСЃС‚ РєРЅРѕРїРєРё' group='final_group_btn' type='text'>РџРµСЂРµР№С‚Рё РЅР° СЃР°Р№С‚</cms:editable>
    <cms:editable name='final_btn_link' label='РЎСЃС‹Р»РєР° РєРЅРѕРїРєРё' group='final_group_btn' type='text'>https://garden-lounge.pro</cms:editable>

    <cms:editable name='final_sep' label='РљР°СЂС‚РёРЅРєР° СЂР°Р·РґРµР»РёС‚РµР»СЏ' type='image'>:div.webp</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>


