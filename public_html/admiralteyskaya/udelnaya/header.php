<?php
define('K_TEMPLATE_NAME', 'udelnaya/header.php');
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
<cms:template title='РЈРїСЂР°РІР»РµРЅРёРµ: РЁР°РїРєР° (Header)' name='header_section' executable='0' order='10'>
    
    <cms:editable name='group_contacts' label='РљРѕРЅС‚Р°РєС‚С‹ Рё РЎРѕС†СЃРµС‚Рё' type='group' />
    <cms:editable name='header_phone' label='РўРµР»РµС„РѕРЅ (С‚РµРєСЃС‚)' group='group_contacts' type='text'>+7 995 624 68 08</cms:editable>
    <cms:editable name='header_phone_link' label='РўРµР»РµС„РѕРЅ (СЃСЃС‹Р»РєР° Р±РµР· РїСЂРѕР±РµР»РѕРІ)' group='group_contacts' type='text'>+79956246808</cms:editable>
    <cms:editable name='link_vk' label='VK СЃСЃС‹Р»РєР°' group='group_contacts' type='text'>https://vk.com/loungegarden</cms:editable>
    <cms:editable name='link_inst' label='Instagram СЃСЃС‹Р»РєР°' group='group_contacts' type='text'>https://instagram.com/garden_lounge_spb/</cms:editable>
    <cms:editable name='link_yt' label='YouTube СЃСЃС‹Р»РєР°' group='group_contacts' type='text'>https://youtube.com/@garden.lounge</cms:editable>
    <cms:editable name='link_tg' label='Telegram СЃСЃС‹Р»РєР°' group='group_contacts' type='text'>https://t.me/Garden_lounge_spb</cms:editable>

    <cms:editable name='group_menu' label='РќР°РІРёРіР°С†РёСЏ' type='group' />
    <cms:repeatable name='nav_menu' label='РџСѓРЅРєС‚С‹ РјРµРЅСЋ' group='group_menu'>
        <cms:editable name='item_name' label='РќР°Р·РІР°РЅРёРµ РїСѓРЅРєС‚Р°' type='text' />
        <cms:editable name='item_link' label='ID Р±Р»РѕРєР° (РЅР°РїСЂРёРјРµСЂ: #about-us)' type='text' />
        <cms:editable name='is_button' label='Р’С‹РґРµР»РёС‚СЊ СЂР°РјРєРѕР№?' opt_values='РќРµС‚=0 | Р”Р°=1' type='dropdown' />
    </cms:repeatable>

    <cms:editable name='group_hero' label='Р“Р»Р°РІРЅС‹Р№ СЌРєСЂР°РЅ (Hero)' type='group' />
    <cms:editable name='hero_tagline' label='РЎР»РѕРіР°РЅ РїРѕРґ Р»РѕРіРѕ' group='group_hero' type='text'>РњР°РіРёС‡РµСЃРєРёР№ РІРµС‡РЅРѕР·РµР»РµРЅС‹Р№ СЃР°Рґ РІ СЃР°РјРѕРј СЃРµСЂРґС†Рµ РЎР°РЅРєС‚-РџРµС‚РµСЂР±СѓСЂРіР°</cms:editable>
    <cms:editable name='hero_bg_desk' label='Р¤РѕРЅ (Р”РµСЃРєС‚РѕРї)' group='group_hero' type='image'>:garden-main.webp</cms:editable>
    <cms:editable name='hero_bg_mob' label='Р¤РѕРЅ (РњРѕР±РёР»РєР°)' group='group_hero' type='image'>:garden-main-mobile.webp</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>


