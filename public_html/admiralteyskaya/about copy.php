<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='РЈРїСЂР°РІР»РµРЅРёРµ: Р¤РёР»РѕСЃРѕС„РёСЏ' name='philosophy_section' executable='0' order='20'>

    <cms:editable name='phil_title' label='Р“Р»Р°РІРЅС‹Р№ Р·Р°РіРѕР»РѕРІРѕРє' type='text'>Philosophy</cms:editable>
    <cms:editable name='phil_concept' label='РўРµРєСЃС‚ РєРѕРЅС†РµРїС†РёРё' type='text'>РљРѕРЅС†РµРїС†РёСЏ</cms:editable>

    <cms:editable name='phil_content' label='РћСЃРЅРѕРІРЅРѕР№ С‚РµРєСЃС‚' type='richtext'>
        РњР°РіРёС‡РµСЃРєРёР№ РІРµС‡РЅРѕР·РµР»РµРЅС‹Р№ СЃР°Рґ, СЃРєСЂС‹С‚С‹Р№ РѕС‚ РіРѕСЂРѕРґСЃРєРѕР№ СЃСѓРµС‚С‹ РІ СЃР°РјРѕРј СЃРµСЂРґС†Рµ РџРµС‚РµСЂР±СѓСЂРіР°.
        <br><br>
        Р—РґРµСЃСЊ РІСЂРµРјСЏ Р·Р°РјРµРґР»СЏРµС‚ СЃРІРѕР№ С…РѕРґ. Р РѕСЃРєРѕС€РЅС‹Р№ РёРЅС‚РµСЂСЊРµСЂ, СѓС‚РѕРїР°СЋС‰РёР№ РІ Р¶РёРІС‹С… С‚СЂРѕРїРёРєР°С…, РјРµР»РѕРґРёС‡РЅС‹Р№ С€СѓРј С„РѕРЅС‚Р°РЅР° Рё СѓСЋС‚РЅРѕРµ С‚РµРїР»Рѕ РєР°РјРёРЅР° СЃРѕР·РґР°СЋС‚ Р°С‚РјРѕСЃС„РµСЂСѓ Р°Р±СЃРѕР»СЋС‚РЅРѕР№ РіР°СЂРјРѕРЅРёРё Рё СѓРµРґРёРЅРµРЅРёСЏ.
    </cms:editable>

    <cms:editable name='phil_slogan' label='РЎР»РѕРіР°РЅ (РІРЅРёР·Сѓ)' type='textarea'>Garden Lounge вЂ” РјРµСЃС‚Рѕ, РіРґРµ СЂРѕР¶РґР°СЋС‚СЃСЏ СЂРёС‚СѓР°Р»С‹, РґРѕСЃС‚РѕР№РЅС‹Рµ РІР°С€РёС… РІРѕСЃРїРѕРјРёРЅР°РЅРёР№</cms:editable>

    <cms:editable name='phil_sep' label='РљР°СЂС‚РёРЅРєР° СЂР°Р·РґРµР»РёС‚РµР»СЏ (СѓР·РѕСЂ)' type='image'>/couch/uploads/image/div.webp</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>