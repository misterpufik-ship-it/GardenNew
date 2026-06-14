<?php
define('K_TEMPLATE_NAME', 'udelnaya/gallery.php');
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
<cms:template title='РЈРїСЂР°РІР»РµРЅРёРµ: Р“Р°Р»РµСЂРµСЏ' name='gallery_section' executable='0' order='40'>
    
    <cms:editable name='gallery_main_title' label='Р“Р»Р°РІРЅС‹Р№ Р·Р°РіРѕР»РѕРІРѕРє' type='text'>Experience</cms:editable>
    <cms:editable name='gallery_sub_title' label='РџРѕРґР·Р°РіРѕР»РѕРІРѕРє' type='text'>Р’РёР·СѓР°Р»СЊРЅР°СЏ СЌСЃС‚РµС‚РёРєР°</cms:editable>

    <cms:repeatable name='gallery_items' label='Р¤РѕС‚РѕРіСЂР°С„РёРё РіР°Р»РµСЂРµРё'>
        <cms:editable name='gallery_img' label='Р¤РѕС‚Рѕ' type='image' />
        <cms:editable name='gallery_img_title' label='РџРѕРґРїРёСЃСЊ Рє С„РѕС‚Рѕ' type='text' />
        <cms:editable name='gallery_category' label='РљР°С‚РµРіРѕСЂРёСЏ (Р’РєР»Р°РґРєР°)' 
            opt_values='Interior=interior | Menu=menu | Vibe=vibe' 
            type='dropdown' 
        />
    </cms:repeatable>

    <cms:editable name='gallery_footer_text' label='РўРµРєСЃС‚ РІ СЃР°РјРѕРј РЅРёР·Сѓ' type='text'>РћС‚РєСЂРѕР№С‚Рµ РјРёСЂ СѓРЅРёРєР°Р»СЊРЅС‹С… Р»РѕРєР°С†РёР№ Рё РіР°СЃС‚СЂРѕРЅРѕРјРёС‡РµСЃРєРѕРіРѕ СѓРґРѕРІРѕР»СЊСЃС‚РІРёСЏ</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>

