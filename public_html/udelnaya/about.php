<?php
define('K_TEMPLATE_NAME', 'udelnaya/about.php');
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
<cms:template title='РЈРїСЂР°РІР»РµРЅРёРµ: Р¤РёР»РѕСЃРѕС„РёСЏ' name='philosophy_section' executable='0' order='20'>
    
    <cms:editable name='phil_group' label='РљРѕРЅС‚РµРЅС‚ Р±Р»РѕРєР°' type='group' order='1' />
        <cms:editable name='phil_title' label='Р—Р°РіРѕР»РѕРІРѕРє (H1/H2)' group='phil_group' type='text'>Philosophy</cms:editable>
        <cms:editable name='phil_concept' label='РўРµРєСЃС‚ РЅР°РґРїРёСЃРё РљРѕРЅС†РµРїС†РёСЏ' group='phil_group' type='text'>РљРѕРЅС†РµРїС†РёСЏ</cms:editable>
        
        <cms:editable name='phil_content' label='РћСЃРЅРѕРІРЅРѕР№ С‚РµРєСЃС‚ РѕРїРёСЃР°РЅРёСЏ' group='phil_group' type='richtext'>
            РњР°РіРёС‡РµСЃРєРёР№ РІРµС‡РЅРѕР·РµР»РµРЅС‹Р№ СЃР°Рґ, СЃРєСЂС‹С‚С‹Р№ РѕС‚ РіРѕСЂРѕРґСЃРєРѕР№ СЃСѓРµС‚С‹ РІ СЃР°РјРѕРј СЃРµСЂРґС†Рµ РџРµС‚РµСЂР±СѓСЂРіР°. 
            <br><br>
            Р—РґРµСЃСЊ РІСЂРµРјСЏ Р·Р°РјРµРґР»СЏРµС‚ СЃРІРѕР№ С…РѕРґ. Р РѕСЃРєРѕС€РЅС‹Р№ РёРЅС‚РµСЂСЊРµСЂ, СѓС‚РѕРїР°СЋС‰РёР№ РІ Р¶РёРІС‹С… С‚СЂРѕРїРёРєР°С…, РјРµР»РѕРґРёС‡РЅС‹Р№ С€СѓРј С„РѕРЅС‚Р°РЅР° Рё СѓСЋС‚РЅРѕРµ С‚РµРїР»Рѕ РєР°РјРёРЅР° СЃРѕР·РґР°СЋС‚ Р°С‚РјРѕСЃС„РµСЂСѓ Р°Р±СЃРѕР»СЋС‚РЅРѕР№ РіР°СЂРјРѕРЅРёРё Рё СѓРµРґРёРЅРµРЅРёСЏ.
        </cms:editable>
        
        <cms:editable name='phil_slogan' label='РЎР»РѕРіР°РЅ (РІРЅРёР·Сѓ)' group='phil_group' type='textarea'>Garden Lounge вЂ” РјРµСЃС‚Рѕ, РіРґРµ СЂРѕР¶РґР°СЋС‚СЃСЏ СЂРёС‚СѓР°Р»С‹, РґРѕСЃС‚РѕР№РЅС‹Рµ РІР°С€РёС… РІРѕСЃРїРѕРјРёРЅР°РЅРёР№</cms:editable>

    <cms:editable name='phil_seo_group' label='SEO Рё Р—Р°С‚РѕС‡РєР° РїРѕРґ РР' type='group' order='2' />
        <cms:editable name='phil_h1_logic' label='РўРёРї Р·Р°РіРѕР»РѕРІРєР°' group='phil_seo_group' type='dropdown' opt_values='РЎРґРµР»Р°С‚СЊ РіР»Р°РІРЅС‹Рј (H1)=h1 | РЎРґРµР»Р°С‚СЊ РѕР±С‹С‡РЅС‹Рј (H2)=h2' default='h2' desc='H1 РґРѕР»Р¶РµРЅ Р±С‹С‚СЊ С‚РѕР»СЊРєРѕ РѕРґРёРЅ РЅР° СЃС‚СЂР°РЅРёС†Рµ!' />
        
        <cms:editable name='phil_img_alt' label='Alt-С‚РµРєСЃС‚ РґР»СЏ СЂР°Р·РґРµР»РёС‚РµР»СЏ' group='phil_seo_group' type='text'>Р­СЃС‚РµС‚РёС‡РЅС‹Р№ Р»Р°СѓРЅРґР¶ Р±Р°СЂ РЎР°РЅРєС‚-РџРµС‚РµСЂР±СѓСЂРі</cms:editable>
        
        <cms:editable name='phil_lsi' label='LSI РљР»СЋС‡Рё РґР»СЏ РР (СЃРєСЂС‹С‚С‹Рµ)' group='phil_seo_group' type='textarea' desc='РџСЂРѕРїРёС€РёС‚Рµ Р·РґРµСЃСЊ С‡РµСЂРµР· Р·Р°РїСЏС‚СѓСЋ РІР°Р¶РЅС‹Рµ РєР»СЋС‡Рё, РєРѕС‚РѕСЂС‹Рµ РР РґРѕР»Р¶РµРЅ СЃРІСЏР·Р°С‚СЊ СЃ СЌС‚РёРј С‚РµРєСЃС‚РѕРј (РЅР°РїСЂРёРјРµСЂ: РґРёР·Р°Р№РЅРµСЂСЃРєР°СЏ РєР°Р»СЊСЏРЅРЅР°СЏ, С†РµРЅС‚СЂ СЃРїР±, Р¶РёРІС‹Рµ СЂР°СЃС‚РµРЅРёСЏ)'>РґРёР·Р°Р№РЅРµСЂСЃРєР°СЏ РєР°Р»СЊСЏРЅРЅР°СЏ РЎРџР±, РїСЂРµРјРёСѓРј Р»Р°СѓРЅРґР¶ РІ С†РµРЅС‚СЂРµ, РјРµСЃС‚Рѕ СЃ Р¶РёРІС‹РјРё СЂР°СЃС‚РµРЅРёСЏРјРё Рё С„РѕРЅС‚Р°РЅРѕРј</cms:editable>

    <cms:editable name='phil_img_group' label='РР·РѕР±СЂР°Р¶РµРЅРёСЏ' type='group' order='3' />
        <cms:editable name='phil_sep' label='РљР°СЂС‚РёРЅРєР° СЂР°Р·РґРµР»РёС‚РµР»СЏ (СѓР·РѕСЂ)' group='phil_img_group' type='image'>:div.webp</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>


