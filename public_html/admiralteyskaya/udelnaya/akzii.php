<?php
define('K_TEMPLATE_NAME', 'udelnaya/akzii.php');
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

<cms:template title='РЈРїСЂР°РІР»РµРЅРёРµ: РђРєС†РёРё' name='akzii_section' executable='0' order='30'>
    
    <cms:editable name='group_titles' label='Р—Р°РіРѕР»РѕРІРєРё СЃРµРєС†РёРё' type='group' />
    
    <cms:editable name='akzii_main_title' 
        label='Р“Р»Р°РІРЅС‹Р№ Р·Р°РіРѕР»РѕРІРѕРє' 
        group='group_titles' 
        type='text'>Our Privileges</cms:editable>
    
    <cms:editable name='akzii_sub_title' 
        label='РџРѕРґР·Р°РіРѕР»РѕРІРѕРє' 
        group='group_titles' 
        type='text'>РЎРїРµС†РёР°Р»СЊРЅС‹Рµ РїСЂРµРґР»РѕР¶РµРЅРёСЏ</cms:editable>

    <cms:editable name='group_promo' label='РЎРїРёСЃРѕРє РїСЂРµРґР»РѕР¶РµРЅРёР№' type='group' />
    
    <cms:repeatable name='promo_list' label='РўР°Р±Р»РёС†Р° Р°РєС†РёР№' group='group_promo'>
        <cms:editable name='promo_name' label='РќР°Р·РІР°РЅРёРµ (РЅР°РїСЂРёРјРµСЂ: Smoky Lunch)' type='text' />
        <cms:editable name='promo_desc' label='РћРїРёСЃР°РЅРёРµ (РјРµР»РєРёР№ С‚РµРєСЃС‚)' type='textarea' />
        <cms:editable name='promo_offer' label='РЈСЃР»РѕРІРёРµ (Р—РѕР»РѕС‚РѕР№ С‚РµРєСЃС‚)' type='text' />
    </cms:repeatable>

    <cms:editable name='group_footer' label='РќРёР¶РЅСЏСЏ С‡Р°СЃС‚СЊ Рё РґРµРєРѕСЂ' type='group' />
    
    <cms:editable name='akzii_footer' 
        label='Р¤РёРЅР°Р»СЊРЅР°СЏ С„СЂР°Р·Р°' 
        group='group_footer' 
        type='text'>РРґРµР°Р»СЊРЅРѕРµ РјРµСЃС‚Рѕ РґР»СЏ С†РµРЅРёС‚РµР»РµР№ РїСЂРµРєСЂР°СЃРЅРѕРіРѕ.</cms:editable>
    
    <cms:editable name='akzii_sep' 
        label='РљР°СЂС‚РёРЅРєР° СЂР°Р·РґРµР»РёС‚РµР»СЏ (СѓР·РѕСЂ)' 
        group='group_footer' 
        type='image'>:div.webp</cms:editable>

</cms:template>

<?php COUCH::invoke(); ?>


