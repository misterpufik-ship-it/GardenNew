<?php
define('K_TEMPLATE_NAME', 'udelnaya/menu.php');
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
<cms:template title='РЈРїСЂР°РІР»РµРЅРёРµ: РњРµРЅСЋ' name='menu_section' executable='0' order='35'>
    
    <cms:editable name='menu_group_titles' label='Р—Р°РіРѕР»РѕРІРєРё' type='group' />
    <cms:editable name='menu_main_title' label='Р“Р»Р°РІРЅС‹Р№ Р·Р°РіРѕР»РѕРІРѕРє' group='menu_group_titles' type='text'>Menu</cms:editable>
    <cms:editable name='menu_sub_title' label='РџРѕРґР·Р°РіРѕР»РѕРІРѕРє' group='menu_group_titles' type='text'>Р­СЃС‚РµС‚РёРєР° РІРєСѓСЃР°</cms:editable>

    <cms:editable name='menu_group_visual' label='Р’РёР·СѓР°Р»СЊРЅРѕРµ РјРµРЅСЋ (Р›РµРІР°СЏ С‡Р°СЃС‚СЊ)' type='group' />
    <cms:editable name='menu_visual_img' label='РћР±Р»РѕР¶РєР° РІРёР·СѓР°Р»СЊРЅРѕРіРѕ РјРµРЅСЋ' group='menu_group_visual' type='image'>:gf11.webp</cms:editable>
    <cms:editable name='menu_visual_link' label='РЎСЃС‹Р»РєР° РЅР° РІРёР·СѓР°Р»СЊРЅРѕРµ РјРµРЅСЋ' group='menu_group_visual' type='text'>https://garden-lounge.pro/udelnaya/menu/visual/</cms:editable>

    <cms:editable name='menu_group_links' label='РљРЅРѕРїРєРё СЃРїСЂР°РІР°' type='group' />
    <cms:editable name='menu_text_link' label='РЎСЃС‹Р»РєР°: РўРµРєСЃС‚РѕРІРѕРµ РјРµРЅСЋ' group='menu_group_links' type='text'>https://garden-lounge.pro/udelnaya/menu/text/</cms:editable>
    <cms:editable name='menu_eng_link' label='РЎСЃС‹Р»РєР°: English Menu' group='menu_group_links' type='text'>https://garden-lounge.pro/udelnaya/menu/english/</cms:editable>

    <cms:editable name='menu_footer_text' label='РўРµРєСЃС‚ РІРЅРёР·Сѓ' type='text'>Р“Р°СЃС‚СЂРѕРЅРѕРјРёС‡РµСЃРєР°СЏ РїРѕСЌР·РёСЏ</cms:editable>
    <cms:editable name='menu_sep' label='РљР°СЂС‚РёРЅРєР° СЂР°Р·РґРµР»РёС‚РµР»СЏ' type='image'>:div.webp</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>


