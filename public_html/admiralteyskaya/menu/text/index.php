<?php require_once( '../../couch/cms.php' ); ?>

<cms:template title='РњРµРЅСЋ : С‚РµРєСЃС‚ РђРґРј' icon='restaurant'>

    <cms:editable name='translation_script' type='message' order='0'>
        <div style="background: #f0f0f0; padding: 15px; border-radius: 5px; border: 1px solid #ccc; margin-bottom: 20px;">
            <button type="button" id="auto-translate-btn" style="padding:10px 20px; background:#C5A059; color:white; border:none; border-radius:4px; cursor:pointer; font-weight:bold; font-size: 14px;">
                вњЁ РџРµСЂРµРІРµСЃС‚Рё РІСЃС‘ РЅР° Р°РЅРіР»РёР№СЃРєРёР№ Р°РІС‚РѕРјР°С‚РёС‡РµСЃРєРё
            </button>
            <p style="margin: 5px 0 0; font-size: 12px; color: #666;">РќР°Р¶РјРёС‚Рµ, С‡С‚РѕР±С‹ Р°РІС‚РѕРјР°С‚РёС‡РµСЃРєРё Р·Р°РїРѕР»РЅРёС‚СЊ РїСѓСЃС‚С‹Рµ Р°РЅРіР»РёР№СЃРєРёРµ РїРѕР»СЏ РЅР° РѕСЃРЅРѕРІРµ СЂСѓСЃСЃРєРёС….</p>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const translateBtn = document.getElementById('auto-translate-btn');
            if(translateBtn) {
                translateBtn.onclick = async function() {
                    const btn = this;
                    const originalText = btn.innerText;
                    btn.innerText = 'вЏі РџРµСЂРµРІРѕР¶Сѓ...';
                    btn.style.opacity = '0.7';
                    btn.disabled = true;

                    async function translateText(text) {
                        if(!text || text.trim().length < 2) return '';
                        try {
                            const res = await fetch(`https://api.mymemory.translated.net/get?q=${encodeURIComponent(text)}&langpair=ru|en`);
                            const data = await res.json();
                            return data.responseData.translatedText;
                        } catch (e) { return text; }
                    }

                    const selector = 'input[id$="cat_title"], input[id$="subcat_title"], input[id$="i_name"], textarea[id$="i_desc"], input[id$="i_subheader"], input[id$="kit_name"], textarea[id$="kit_desc"], input[id$="p_title"], textarea[id$="p_desc"], input[id$="p_tag"]';
                    const inputs = document.querySelectorAll(selector);

                    for (let input of inputs) {
                        if(input.id.endsWith('_en')) continue;
                        const enInput = document.getElementById(input.id + '_en');
                        if (enInput && (!enInput.value || enInput.value.trim() === '')) {
                            enInput.value = await translateText(input.value);
                            enInput.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }

                    btn.innerText = `вњ… Р“РѕС‚РѕРІРѕ!`;
                    btn.style.background = '#4CAF50';
                    setTimeout(() => {
                        btn.innerText = originalText;
                        btn.style.background = '#C5A059';
                        btn.style.opacity = '1';
                        btn.disabled = false;
                    }, 3000);
                };
            }
        });
        </script>
    </cms:editable>

    <cms:editable name='seo_group' label='SEO РќР°СЃС‚СЂРѕР№РєРё' type='group' order='1' collapsed='1' />
    <cms:editable name='page_title' label='Title (RU)' type='text' group='seo_group'>Garden Lounge</cms:editable>
    <cms:editable name='page_title_en' label='Title (EN)' type='text' group='seo_group'>Garden Lounge</cms:editable>
    <cms:editable name='meta_desc' label='Description' type='textarea' group='seo_group'></cms:editable>
    <cms:editable name='meta_desc_en' label='Description (EN)' type='textarea' group='seo_group'></cms:editable>

    <cms:editable name='settings_group' label='РћСЃРЅРѕРІРЅС‹Рµ РЅР°СЃС‚СЂРѕР№РєРё' type='group' order='2' collapsed='1' />
    <cms:editable name='site_logo' label='Р›РѕРіРѕС‚РёРї' type='image' group='settings_group' show_preview='1' preview_width='150'>/couch/uploads/image/logo3.webp</cms:editable>

    <cms:editable name='lbl_tab_1' label='Р’РєР»Р°РґРєР° 1 (RU)' type='text' group='settings_group'>РљР°Р»СЊСЏРЅС‹</cms:editable>
    <cms:editable name='lbl_tab_1_en' label='Tab 1 (EN)' type='text' group='settings_group'>Hookahs</cms:editable>
    <cms:editable name='lbl_tab_2' label='Р’РєР»Р°РґРєР° 2 (RU)' type='text' group='settings_group'>РљСѓС…РЅСЏ</cms:editable>
    <cms:editable name='lbl_tab_2_en' label='Tab 2 (EN)' type='text' group='settings_group'>Kitchen</cms:editable>
    <cms:editable name='lbl_tab_3' label='Р’РєР»Р°РґРєР° 3 (RU)' type='text' group='settings_group'>Р‘Р°СЂ</cms:editable>
    <cms:editable name='lbl_tab_3_en' label='Tab 3 (EN)' type='text' group='settings_group'>Bar</cms:editable>
    <cms:editable name='lbl_tab_4' label='Р’РєР»Р°РґРєР° 4 (RU)' type='text' group='settings_group'>РќР°РїРёС‚РєРё</cms:editable>
    <cms:editable name='lbl_tab_4_en' label='Tab 4 (EN)' type='text' group='settings_group'>Drinks</cms:editable>
    <cms:editable name='lbl_tab_5' label='Р’РєР»Р°РґРєР° 5 (RU)' type='text' group='settings_group'>РђРєС†РёРё</cms:editable>
    <cms:editable name='lbl_tab_5_en' label='Tab 5 (EN)' type='text' group='settings_group'>Specials</cms:editable>

    <cms:editable name='subtabs_group' label='РџРѕРґРІРєР»Р°РґРєРё (РЅР°Р·РІР°РЅРёСЏ)' type='group' order='3' collapsed='1' />
    <cms:editable name='kt_sub_snacks' label='РљСѓС…РЅСЏ: Р—Р°РєСѓСЃРєРё' type='text' group='subtabs_group'>Р—Р°РєСѓСЃРєРё</cms:editable>
    <cms:editable name='kt_sub_snacks_en' label='Kitchen: Snacks (EN)' type='text' group='subtabs_group'>Snacks</cms:editable>
    <cms:editable name='kt_sub_salads' label='РљСѓС…РЅСЏ: РЎР°Р»Р°С‚С‹' type='text' group='subtabs_group'>РЎР°Р»Р°С‚С‹</cms:editable>
    <cms:editable name='kt_sub_salads_en' label='Kitchen: Salads (EN)' type='text' group='subtabs_group'>Salads</cms:editable>
    <cms:editable name='kt_sub_rolls' label='РљСѓС…РЅСЏ: Р РѕР»Р»С‹' type='text' group='subtabs_group'>Р РѕР»Р»С‹</cms:editable>
    <cms:editable name='kt_sub_rolls_en' label='Kitchen: Rolls (EN)' type='text' group='subtabs_group'>Rolls</cms:editable>
    <cms:editable name='kt_sub_soups' label='РљСѓС…РЅСЏ: РЎСѓРїС‹' type='text' group='subtabs_group'>РЎСѓРїС‹</cms:editable>
    <cms:editable name='kt_sub_soups_en' label='Kitchen: Soups (EN)' type='text' group='subtabs_group'>Soups</cms:editable>
    <cms:editable name='kt_sub_poke' label='РљСѓС…РЅСЏ: РџРѕРєРµ' type='text' group='subtabs_group'>РџРѕРєРµ | Р‘РѕСѓР» | Wok</cms:editable>
    <cms:editable name='kt_sub_poke_en' label='Kitchen: Poke (EN)' type='text' group='subtabs_group'>Poke | Bowl | Wok</cms:editable>
    <cms:editable name='kt_sub_hot' label='РљСѓС…РЅСЏ: Р“РѕСЂСЏС‡РµРµ' type='text' group='subtabs_group'>Р“РѕСЂСЏС‡РµРµ</cms:editable>
    <cms:editable name='kt_sub_hot_en' label='Kitchen: Hot (EN)' type='text' group='subtabs_group'>Hot</cms:editable>
    <cms:editable name='kt_sub_desserts' label='РљСѓС…РЅСЏ: Р”РµСЃРµСЂС‚С‹' type='text' group='subtabs_group'>Р”РµСЃРµСЂС‚С‹</cms:editable>
    <cms:editable name='kt_sub_desserts_en' label='Kitchen: Desserts (EN)' type='text' group='subtabs_group'>Desserts</cms:editable>
    <cms:editable name='dt_sub_tea' label='РќР°РїРёС‚РєРё: Р§Р°Р№' type='text' group='subtabs_group'>Р§Р°Р№ | РљРѕС„Рµ</cms:editable>
    <cms:editable name='dt_sub_tea_en' label='Drinks: Tea (EN)' type='text' group='subtabs_group'>Tea | Coffee</cms:editable>
    <cms:editable name='dt_sub_lemon' label='РќР°РїРёС‚РєРё: Р›РµРјРѕРЅР°РґС‹' type='text' group='subtabs_group'>Р›РµРјРѕРЅР°РґС‹</cms:editable>
    <cms:editable name='dt_sub_lemon_en' label='Drinks: Lemonades (EN)' type='text' group='subtabs_group'>Lemonades</cms:editable>
    <cms:editable name='bt_sub_beer' label='Р‘Р°СЂ: РџРёРІРѕ' type='text' group='subtabs_group'>РџРёРІРѕ</cms:editable>
    <cms:editable name='bt_sub_beer_en' label='Bar: Beer (EN)' type='text' group='subtabs_group'>Beer</cms:editable>
    <cms:editable name='bt_sub_wine' label='Р‘Р°СЂ: Р’РёРЅРѕ' type='text' group='subtabs_group'>Р’РёРЅРѕ</cms:editable>
    <cms:editable name='bt_sub_wine_en' label='Bar: Wine (EN)' type='text' group='subtabs_group'>Wine</cms:editable>
    <cms:editable name='bt_sub_cocktails' label='Р‘Р°СЂ: РљРѕРєС‚РµР№Р»Рё' type='text' group='subtabs_group'>РљРѕРєС‚РµР№Р»Рё</cms:editable>
    <cms:editable name='bt_sub_cocktails_en' label='Bar: Cocktails (EN)' type='text' group='subtabs_group'>Cocktails</cms:editable>
    <cms:editable name='bt_sub_strong' label='Р‘Р°СЂ: РљСЂРµРїРєРёР№' type='text' group='subtabs_group'>РљСЂРµРїРєРёР№ Р°Р»РєРѕРіРѕР»СЊ</cms:editable>
    <cms:editable name='bt_sub_strong_en' label='Bar: Spirits (EN)' type='text' group='subtabs_group'>Spirits</cms:editable>

    <cms:editable name='grp_rep_hookahs' label='РЎРѕРґРµСЂР¶Р°РЅРёРµ РљР°Р»СЊСЏРЅС‹' type='group' order='4' collapsed='1' />
    <cms:repeatable name='rep_hookahs_v2' label='РЎРїРёСЃРѕРє РљР°Р»СЊСЏРЅС‹' group='grp_rep_hookahs'>
        <cms:editable name='item_tags' label='РўРµРі' type='dropdown' opt_values='РќРµС‚=- | New | Hit | Special | ChefвЂ™s Choice | рџЊ¶пёЏ | рџЊ¶пёЏрџЊ¶пёЏ | рџЊ¶пёЏрџЊ¶пёЏрџЊ¶пёЏ | New + рџЊ¶пёЏ | Hit + рџЊ¶пёЏ' />
        <cms:editable name='row_type' label='РўРёРї' type='dropdown' opt_values='РўРѕРІР°СЂ=item | Р—Р°РіРѕР»РѕРІРѕРє СЂР°Р·РґРµР»Р°=header | РџРѕРґР·Р°РіРѕР»РѕРІРѕРє СЂР°Р·РґРµР»Р°=subheader' />
        <cms:editable name='cat_title' label='Р—Р°РіРѕР»РѕРІРѕРє (RU)' type='text' />
        <cms:editable name='cat_title_en' label='Р—Р°РіРѕР»РѕРІРѕРє (EN)' type='text' />
        <cms:editable name='subcat_title' label='РџРѕРґР·Р°РіРѕР»РѕРІРѕРє (RU)' type='text' />
        <cms:editable name='subcat_title_en' label='РџРѕРґР·Р°РіРѕР»РѕРІРѕРє (EN)' type='text' />
        <cms:editable name='i_name' label='РўРѕРІР°СЂ RU' type='text' />
        <cms:editable name='i_name_en' label='РўРѕРІР°СЂ EN' type='text' />
        <cms:editable name='i_desc' label='РћРїРёСЃР°РЅРёРµ RU' type='textarea' height='40' />
        <cms:editable name='i_desc_en' label='РћРїРёСЃР°РЅРёРµ EN' type='textarea' height='40' />
        <cms:editable name='i_price' label='Р¦РµРЅР°' type='text' />
        <cms:editable name='note_after_ru' label='РџСЂРёРјРµС‡Р°РЅРёРµ RU' type='textarea' height='30' />
        <cms:editable name='note_after_ru_en' label='РџСЂРёРјРµС‡Р°РЅРёРµ EN' type='textarea' height='30' />
    </cms:repeatable>

    <cms:editable name='grp_rep_kitchen' label='РЎРѕРґРµСЂР¶Р°РЅРёРµ РљСѓС…РЅСЏ' type='group' order='5' collapsed='1' />
    <cms:repeatable name='rep_kitchen_v2' label='РЎРїРёСЃРѕРє РљСѓС…РЅСЏ' group='grp_rep_kitchen'>
        <cms:editable name='item_tags' label='РўРµРі' type='dropdown' opt_values='РќРµС‚=- | New | Hit | Special | ChefвЂ™s Choice | рџЊ¶пёЏ | рџЊ¶пёЏрџЊ¶пёЏ | рџЊ¶пёЏрџЊ¶пёЏрџЊ¶пёЏ | New + рџЊ¶пёЏ | Hit + рџЊ¶пёЏ' />
        <cms:editable name='row_type' label='РўРёРї' type='dropdown' opt_values='РўРѕРІР°СЂ=item | Р—Р°РіРѕР»РѕРІРѕРє СЂР°Р·РґРµР»Р°=header | РџРѕРґР·Р°РіРѕР»РѕРІРѕРє СЂР°Р·РґРµР»Р°=subheader' />
        <cms:editable name='cat_title' label='Р—Р°РіРѕР»РѕРІРѕРє (RU)' type='text' />
        <cms:editable name='cat_title_en' label='Р—Р°РіРѕР»РѕРІРѕРє (EN)' type='text' />
        <cms:editable name='subcat_title' label='РџРѕРґР·Р°РіРѕР»РѕРІРѕРє (RU)' type='text' />
        <cms:editable name='subcat_title_en' label='РџРѕРґР·Р°РіРѕР»РѕРІРѕРє (EN)' type='text' />
        <cms:editable name='kitchen_subtab' label='РљР°С‚РµРіРѕСЂРёСЏ' type='dropdown' opt_values='Р—Р°РєСѓСЃРєРё=snacks | РЎР°Р»Р°С‚С‹=salads | Р РѕР»Р»С‹=rolls | РЎСѓРїС‹=soups | РџРѕРєРµ / Р‘РѕСѓР» / Wok=poke_bowl_wok | Р“РѕСЂСЏС‡РµРµ=hot | Р”РµСЃРµСЂС‚С‹=desserts | Р”СЂСѓРіРѕРµ=other' />
        <cms:editable name='kit_name' label='РќР°Р·РІР°РЅРёРµ RU' type='text' />
        <cms:editable name='kit_name_en' label='РќР°Р·РІР°РЅРёРµ EN' type='text' />
        <cms:editable name='kit_desc' label='РћРїРёСЃР°РЅРёРµ RU' type='textarea' height='40' />
        <cms:editable name='kit_desc_en' label='РћРїРёСЃР°РЅРёРµ EN' type='textarea' height='40' />
        <cms:editable name='kit_price' label='Р¦РµРЅР°' type='text' />
        <cms:editable name='note_after_ru' label='РџСЂРёРјРµС‡Р°РЅРёРµ RU' type='textarea' height='30' />
        <cms:editable name='note_after_ru_en' label='РџСЂРёРјРµС‡Р°РЅРёРµ EN' type='textarea' height='30' />
    </cms:repeatable>

    <cms:editable name='grp_rep_bar' label='РЎРѕРґРµСЂР¶Р°РЅРёРµ Р‘Р°СЂ' type='group' order='6' collapsed='1' />
    <cms:repeatable name='rep_bar_alc_v2' label='РЎРїРёСЃРѕРє Р‘Р°СЂ' group='grp_rep_bar'>
        <cms:editable name='item_tags' label='РўРµРі' type='dropdown' opt_values='РќРµС‚=- | New | Hit | Special | ChefвЂ™s Choice | рџЊ¶пёЏ | рџЊ¶пёЏрџЊ¶пёЏ | рџЊ¶пёЏрџЊ¶пёЏрџЊ¶пёЏ' />
        <cms:editable name='row_type' label='РўРёРї' type='dropdown' opt_values='РўРѕРІР°СЂ=item | Р—Р°РіРѕР»РѕРІРѕРє СЂР°Р·РґРµР»Р°=header | РџРѕРґР·Р°РіРѕР»РѕРІРѕРє СЂР°Р·РґРµР»Р°=subheader' />
        <cms:editable name='cat_title' label='Р—Р°РіРѕР»РѕРІРѕРє (RU)' type='text' />
        <cms:editable name='cat_title_en' label='Р—Р°РіРѕР»РѕРІРѕРє (EN)' type='text' />
        <cms:editable name='subcat_title' label='РџРѕРґР·Р°РіРѕР»РѕРІРѕРє (RU)' type='text' />
        <cms:editable name='subcat_title_en' label='РџРѕРґР·Р°РіРѕР»РѕРІРѕРє (EN)' type='text' />
        <cms:editable name='bar_alc_subtab' label='РљР°С‚РµРіРѕСЂРёСЏ' type='dropdown' opt_values='РџРёРІРѕ=beer | Р’РёРЅРѕ=wine | РљРѕРєС‚РµР№Р»Рё=cocktails | РљСЂРµРїРєРёР№ Р°Р»РєРѕРіРѕР»СЊ=spirits | Р”СЂСѓРіРѕРµ=other' />
        <cms:editable name='i_name' label='РќР°Р·РІР°РЅРёРµ RU' type='text' />
        <cms:editable name='i_name_en' label='РќР°Р·РІР°РЅРёРµ EN' type='text' />
        <cms:editable name='i_subheader' label='РџРѕРґР·Р°РіРѕР»РѕРІРѕРє С‚РѕРІР°СЂР° RU' type='text' />
        <cms:editable name='i_subheader_en' label='РџРѕРґР·Р°РіРѕР»РѕРІРѕРє С‚РѕРІР°СЂР° EN' type='text' />
        <cms:editable name='i_price' label='Р¦РµРЅР°' type='text' />
        <cms:editable name='note_after_ru' label='РџСЂРёРјРµС‡Р°РЅРёРµ RU' type='textarea' height='30' />
        <cms:editable name='note_after_ru_en' label='РџСЂРёРјРµС‡Р°РЅРёРµ EN' type='textarea' height='30' />
    </cms:repeatable>

    <cms:editable name='grp_rep_drinks' label='РЎРѕРґРµСЂР¶Р°РЅРёРµ РќР°РїРёС‚РєРё' type='group' order='7' collapsed='1' />
    <cms:repeatable name='rep_bar_non_v2' label='РЎРїРёСЃРѕРє РќР°РїРёС‚РєРё' group='grp_rep_drinks'>
        <cms:editable name='item_tags' label='РўРµРі' type='dropdown' opt_values='РќРµС‚=- | New | Hit | Special | ChefвЂ™s Choice' />
        <cms:editable name='row_type' label='РўРёРї' type='dropdown' opt_values='РўРѕРІР°СЂ=item | Р—Р°РіРѕР»РѕРІРѕРє СЂР°Р·РґРµР»Р°=header | РџРѕРґР·Р°РіРѕР»РѕРІРѕРє СЂР°Р·РґРµР»Р°=subheader' />
        <cms:editable name='cat_title' label='Р—Р°РіРѕР»РѕРІРѕРє (RU)' type='text' />
        <cms:editable name='cat_title_en' label='Р—Р°РіРѕР»РѕРІРѕРє (EN)' type='text' />
        <cms:editable name='subcat_title' label='РџРѕРґР·Р°РіРѕР»РѕРІРѕРє (RU)' type='text' />
        <cms:editable name='subcat_title_en' label='РџРѕРґР·Р°РіРѕР»РѕРІРѕРє (EN)' type='text' />
        <cms:editable name='drinks_subtab' label='РљР°С‚РµРіРѕСЂРёСЏ' type='dropdown' opt_values='Р§Р°Р№ / РљРѕС„Рµ=tea_coffee | Р›РµРјРѕРЅР°РґС‹=lemonades | Р”СЂСѓРіРѕРµ=other' />
        <cms:editable name='i_name' label='РќР°Р·РІР°РЅРёРµ RU' type='text' />
        <cms:editable name='i_name_en' label='РќР°Р·РІР°РЅРёРµ EN' type='text' />
        <cms:editable name='i_desc' label='РћРїРёСЃР°РЅРёРµ RU' type='textarea' height='30' />
        <cms:editable name='i_desc_en' label='РћРїРёСЃР°РЅРёРµ EN' type='textarea' height='30' />
        <cms:editable name='i_price' label='Р¦РµРЅР°' type='text' />
        <cms:editable name='note_after_ru' label='РџСЂРёРјРµС‡Р°РЅРёРµ RU' type='textarea' height='30' />
        <cms:editable name='note_after_ru_en' label='РџСЂРёРјРµС‡Р°РЅРёРµ EN' type='textarea' height='30' />
    </cms:repeatable>

    <cms:editable name='grp_promo' label='Р’РєР»Р°РґРєР°: РђРєС†РёРё' type='group' order='8' collapsed='1' />
    <cms:editable name='promo_title' type='text' group='grp_promo'>РџСЂРёРІРёР»РµРіРёРё Garden Lounge</cms:editable>
    <cms:editable name='promo_title_en' type='text' group='grp_promo'>Garden Lounge Privileges</cms:editable>
    <cms:editable name='promo_subtitle' type='text' group='grp_promo'>РЎРїРµС†РёР°Р»СЊРЅС‹Рµ РїСЂРµРґР»РѕР¶РµРЅРёСЏ</cms:editable>
    <cms:editable name='promo_subtitle_en' type='text' group='grp_promo'>Special Offers</cms:editable>
    <cms:editable name='promo_footer' type='text' group='grp_promo'>РРґРµР°Р»СЊРЅРѕРµ РјРµСЃС‚Рѕ РґР»СЏ С†РµРЅРёС‚РµР»РµР№ РїСЂРµРєСЂР°СЃРЅРѕРіРѕ.</cms:editable>
    <cms:editable name='promo_footer_en' type='text' group='grp_promo'>Ideal place for connoisseurs.</cms:editable>

    <cms:repeatable name='list_promos_v2' label='РЎРїРёСЃРѕРє Р°РєС†РёР№' group='grp_promo'>
        <cms:editable name='p_title' type='text' /> <cms:editable name='p_title_en' type='text' />
        <cms:editable name='p_desc' type='textarea' /> <cms:editable name='p_desc_en' type='textarea' />
        <cms:editable name='p_tag' type='text' /> <cms:editable name='p_tag_en' type='text' />
    </cms:repeatable>

</cms:template>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><cms:show page_title /></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=Montserrat:wght@200;300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body { background-color: #000; color: #fff; font-family: 'Montserrat', sans-serif; margin: 0; overflow-x: hidden; }
        .font-serif-lux { font-family: 'Cormorant Garamond', serif; }
        :root { --gold:#C5A059; --gold-dark:#8e7037; }

        @keyframes shineGold { to { background-position: 200% center; } }
        .gold-shimmer {
            background: linear-gradient(to right, var(--gold-dark) 0%, var(--gold) 40%, #FFEebb 50%, var(--gold) 60%, var(--gold-dark) 100%);
            background-size: 200% auto; -webkit-background-clip:text; background-clip:text;
            color: transparent; animation: shineGold 5s linear infinite;
        }

        .nav-sticky { position: sticky; top:0; z-index:50; background-color: rgba(0,0,0,0.95); backdrop-filter: blur(10px); border-bottom: 1px solid #1a1a1a; }
        .gold-divider-nav { width:100%; height:1px; background: linear-gradient(90deg, transparent 0%, var(--gold) 50%, transparent 100%); opacity:0.8; margin-top: 6px; }

        .tab-btn { position: relative; transition: all .3s ease; color:#888; background:none; border:none; cursor:pointer; }
        .tab-btn.active { color: var(--gold); }
        .tab-btn.active::after { content:''; position:absolute; bottom:-4px; left:0; width:100%; height:2px; background: var(--gold); }

        .tabs-wrap { display:flex; flex-wrap:wrap; justify-content:center; gap:14px 18px; padding: 14px 10px 8px; }
        .subtabs-wrap { display:none; flex-wrap:wrap; justify-content:center; gap:10px 12px; padding: 10px 10px 15px; }
        .subtab-btn { border: 1px solid rgba(197,160,89,0.35); border-radius: 999px; padding: 6px 12px; font-size: 11px; text-transform: uppercase; color: #d0d0d0; cursor:pointer; }
        .subtab-btn.active { color: #000; background: var(--gold); }

        .tab-content { display:none; }
        .tab-content.active { display:block; animation: fadeIn .35s ease-out; }
        @keyframes fadeIn { from{ opacity:0; transform: translateY(8px);} to{opacity:1; transform: translateY(0);} }

        .category-title { font-family: 'Cormorant Garamond', serif; font-style: italic; font-size: 1.875rem; margin: 2.2rem 0 1.6rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: .5rem; text-align: center; }
        .subcat-title { font-weight:700; letter-spacing: .28em; text-transform: uppercase; font-size:.75rem; text-align:center; margin: 1.7rem 0 .9rem; }
        .price-tag { font-weight:500; font-size:1.25rem; white-space:nowrap; }

        .badge-container { display: inline-flex; gap: 4px; vertical-align: middle; margin-left: 8px; position: relative; top: -1px; }
        .badge-item {
            height: 18px !important;
            font-size: 12px !important;
            font-weight: 800 !important;
            text-transform: uppercase;
            letter-spacing: 0.01em;
            padding: 0 5px !important;
            border-radius: 2px;
            white-space: nowrap;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(197, 160, 89, 0.6);
            color: var(--gold);
            background: rgba(197, 160, 89, 0.1);
        }
        .badge-chef { background: var(--gold); color: #000; border: 1px solid var(--gold); }
        .badge-spicy { color: #ff4d4d; border: none; background: none; font-size: 14px; padding: 0; margin-left: 2px; }

        .taplink-block-wrapper { width:100vw; position:relative; left:50%; margin-left:-50vw; background-color: #000; padding: 40px 0; overflow: hidden; }
        .content-limiter { max-width:600px; margin:0 auto; padding: 0 20px; position: relative; z-index: 10; }
        .film-grain { position:absolute; top:0; left:0; width:100%; height:100%; background:url('https://grainy-gradients.vercel.app/noise.svg'); opacity:.04; pointer-events:none; z-index:1; }
        .promo-card { border:1px solid rgba(197,160,89,0.2); background-color: rgba(20,20,20,0.4); padding:20px; text-align:center; margin-bottom: 15px; }
        .gold-line-fade { width:160px; height:1px; background: linear-gradient(90deg, transparent, var(--gold), transparent); margin: 16px auto; }
        .shimmer-gold { background: linear-gradient(to right, #8e7037 0%, #C5A059 40%, #FFEebb 50%, #C5A059 60%, #8e7037 100%); background-size:200% auto; color:transparent; -webkit-background-clip:text; background-clip:text; animation: shineGold 5s linear infinite; display:inline-block; }

        #loyalty-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(10px); display: none; justify-content: center; align-items: center; z-index: 3000; padding: 20px; }
        .modal-content { background: #0a0a0a; border: 1px solid var(--gold); padding: 40px 25px; width: 100%; max-width: 400px; text-align: center; position: relative; box-shadow: 0 0 30px rgba(197, 160, 89, 0.2); }
        .modal-title { font-family: 'Cormorant Garamond', serif; font-style: italic; font-size: 24px; margin-bottom: 25px; color: #fff; line-height: 1.2; }
        .modal-btn { display: flex; align-items: center; justify-content: center; gap: 12px; width: 100%; height: 54px; border: 1px solid rgba(197, 160, 89, 0.3); margin-bottom: 12px; color: #fff !important; text-decoration: none; text-transform: uppercase; font-size: 11px; letter-spacing: 0.1em; transition: 0.3s; }
        .modal-btn:hover { background: rgba(197, 160, 89, 0.1); border-color: var(--gold); }
        .modal-btn i { font-size: 18px; color: var(--gold); }
        .close-modal { position: absolute; top: 10px; right: 15px; font-size: 28px; color: rgba(255,255,255,0.3); cursor: pointer; line-height: 1; }
        .close-modal:hover { color: #fff; }

        .action-area { display: flex; flex-direction: column; align-items: center; gap: 10px; margin-top: 40px; }
        @media (min-width: 768px) { .action-area { flex-direction: row; justify-content: center; gap: 15px; } }
        .btn-base { display: flex; align-items: center; justify-content: center; width: 100%; max-width: 280px; height: 52px; border: 1px solid rgba(197,160,89,0.3); text-transform: uppercase; font-size: 10px; letter-spacing: 0.15em; text-decoration: none; transition: 0.3s; }
        .btn-gold-fill { background: var(--gold); color: #000; font-weight: 700; border: none; }
        .note-after { margin-top: 6px; font-size: 12px; color: #a9a9a9; line-height: 1.5; }
    </style>
</head>

<body>

<header class="py-8 text-center bg-black">
    <img src="<cms:show site_logo />" alt="Logo" class="h-28 mx-auto">
</header>

<div class="nav-sticky">
    <nav class="tabs-wrap">
        <button onclick="switchTab('hookahs')" class="tab-btn active uppercase font-bold tracking-widest text-xs"><cms:show lbl_tab_1 /></button>
        <button onclick="switchTab('kitchen')" class="tab-btn uppercase font-bold tracking-widest text-xs"><cms:show lbl_tab_2 /></button>
        <button onclick="switchTab('bar-alc')" class="tab-btn uppercase font-bold tracking-widest text-xs"><cms:show lbl_tab_3 /></button>
        <button onclick="switchTab('bar-non')" class="tab-btn uppercase font-bold tracking-widest text-xs"><cms:show lbl_tab_4 /></button>
        <button onclick="switchTab('promos')" class="tab-btn uppercase font-bold tracking-widest text-xs"><cms:show lbl_tab_5 /></button>
    </nav>

    <div id="subtabs-kitchen" class="subtabs-wrap">
        <button class="subtab-btn active" data-sub="snacks" onclick="switchSubtab('kitchen','snacks')"><cms:show kt_sub_snacks /></button>
        <button class="subtab-btn" data-sub="salads" onclick="switchSubtab('kitchen','salads')"><cms:show kt_sub_salads /></button>
        <button class="subtab-btn" data-sub="rolls" onclick="switchSubtab('kitchen','rolls')"><cms:show kt_sub_rolls /></button>
        <button class="subtab-btn" data-sub="soups" onclick="switchSubtab('kitchen','soups')"><cms:show kt_sub_soups /></button>
        <button class="subtab-btn" data-sub="poke_bowl_wok" onclick="switchSubtab('kitchen','poke_bowl_wok')"><cms:show kt_sub_poke /></button>
        <button class="subtab-btn" data-sub="hot" onclick="switchSubtab('kitchen','hot')"><cms:show kt_sub_hot /></button>
        <button class="subtab-btn" data-sub="desserts" onclick="switchSubtab('kitchen','desserts')"><cms:show kt_sub_desserts /></button>
        <button class="subtab-btn" data-sub="other" onclick="switchSubtab('kitchen','other')">Р›Р°РЅС‡Рё</button>
    </div>

    <div id="subtabs-bar-alc" class="subtabs-wrap">
        <button class="subtab-btn active" data-sub="beer" onclick="switchSubtab('bar-alc','beer')"><cms:show bt_sub_beer /></button>
        <button class="subtab-btn" data-sub="wine" onclick="switchSubtab('bar-alc','wine')"><cms:show bt_sub_wine /></button>
        <button class="subtab-btn" data-sub="cocktails" onclick="switchSubtab('bar-alc','cocktails')"><cms:show bt_sub_cocktails /></button>
        <button class="subtab-btn" data-sub="spirits" onclick="switchSubtab('bar-alc','spirits')"><cms:show bt_sub_strong /></button>
    </div>

    <div id="subtabs-bar-non" class="subtabs-wrap">
        <button class="subtab-btn active" data-sub="tea_coffee" onclick="switchSubtab('bar-non','tea_coffee')"><cms:show dt_sub_tea /></button>
        <button class="subtab-btn" data-sub="lemonades" onclick="switchSubtab('bar-non','lemonades')"><cms:show dt_sub_lemon /></button>
        <button class="subtab-btn" data-sub="other" onclick="switchSubtab('bar-non','other')">РќР°РїРёС‚РєРё</button>
    </div>
    <div class="gold-divider-nav"></div>
</div>

<main class="max-w-2xl mx-auto px-6 py-10 pb-24 min-h-screen">

    <div id="hookahs" class="tab-content active">
        <cms:show_repeatable 'rep_hookahs_v2'>
            <div class="menu-row" data-subtab="all">
                <cms:if row_type='header'><h3 class="category-title gold-shimmer"><cms:show cat_title /></h3>
                <cms:else_if row_type='subheader' /><h4 class="subcat-title gold-shimmer"><cms:show subcat_title /></h4>
                <cms:else />
                    <div class="w-full pb-2 border-b border-white/5 mb-4">
                        <div class="grid grid-cols-[1fr_auto] gap-x-4 items-start">
                            <div class="text-white text-lg flex items-center flex-wrap">
                                <cms:show i_name />
                                <div class="badge-container">
                                    <cms:if item_tags='New' || item_tags='New + рџЊ¶пёЏ'><span class="badge-item">New</span></cms:if>
                                    <cms:if item_tags='Hit' || item_tags='Hit + рџЊ¶пёЏ'><span class="badge-item">Hit</span></cms:if>
                                    <cms:if item_tags='Special'><span class="badge-item">Special</span></cms:if>
                                    <cms:if item_tags='ChefвЂ™s Choice'><span class="badge-item badge-chef">Chef</span></cms:if>
                                    <cms:if item_tags='рџЊ¶пёЏ' || item_tags='New + рџЊ¶пёЏ' || item_tags='Hit + рџЊ¶пёЏ'><span class="badge-spicy">рџЊ¶пёЏ</span></cms:if>
                                    <cms:if item_tags='рџЊ¶пёЏрџЊ¶пёЏ'><span class="badge-spicy">рџЊ¶пёЏрџЊ¶пёЏ</span></cms:if>
                                    <cms:if item_tags='рџЊ¶пёЏрџЊ¶пёЏрџЊ¶пёЏ'><span class="badge-spicy">рџЊ¶пёЏрџЊ¶пёЏрџЊ¶пёЏ</span></cms:if>
                                </div>
                            </div>
                            <span class="price-tag gold-shimmer"><cms:show i_price /> в‚Ѕ</span>
                            <cms:if i_desc><div class="col-span-2 text-[12px] text-gray-400 mt-1"><cms:show i_desc /></div></cms:if>
                        </div>
                        <cms:if note_after_ru><div class="note-after"><cms:show note_after_ru /></div></cms:if>
                    </div>
                </cms:if>
            </div>
        </cms:show_repeatable>
    </div>

    <div id="kitchen" class="tab-content">
        <cms:show_repeatable 'rep_kitchen_v2'>
            <cms:set _st = kitchen_subtab />
            <div class="menu-row" data-subtab="<cms:if _st><cms:show _st /><cms:else />other</cms:if>">
                <cms:if row_type='header'><h3 class="category-title gold-shimmer"><cms:show cat_title /></h3>
                <cms:else_if row_type='subheader' /><h4 class="subcat-title gold-shimmer"><cms:show subcat_title /></h4>
                <cms:else />
                    <div class="w-full pb-2 border-b border-white/5 mb-4">
                        <div class="grid grid-cols-[1fr_auto] gap-x-4 items-start">
                            <div class="text-white text-lg flex items-center flex-wrap">
                                <cms:show kit_name />
                                <div class="badge-container">
                                    <cms:if item_tags='New' || item_tags='New + рџЊ¶пёЏ'><span class="badge-item">New</span></cms:if>
                                    <cms:if item_tags='Hit' || item_tags='Hit + рџЊ¶пёЏ'><span class="badge-item">Hit</span></cms:if>
                                    <cms:if item_tags='Special'><span class="badge-item">Special</span></cms:if>
                                    <cms:if item_tags='ChefвЂ™s Choice'><span class="badge-item badge-chef">Chef</span></cms:if>
                                    <cms:if item_tags='рџЊ¶пёЏ' || item_tags='New + рџЊ¶пёЏ' || item_tags='Hit + рџЊ¶пёЏ'><span class="badge-spicy">рџЊ¶пёЏ</span></cms:if>
                                    <cms:if item_tags='рџЊ¶пёЏрџЊ¶пёЏ'><span class="badge-spicy">рџЊ¶пёЏрџЊ¶пёЏ</span></cms:if>
                                    <cms:if item_tags='рџЊ¶пёЏрџЊ¶пёЏрџЊ¶пёЏ'><span class="badge-spicy">рџЊ¶пёЏрџЊ¶пёЏрџЊ¶пёЏ</span></cms:if>
                                </div>
                            </div>
                            <span class="price-tag gold-shimmer"><cms:show kit_price /> в‚Ѕ</span>
                            <cms:if kit_desc><div class="col-span-2 text-[12px] text-gray-400 mt-1"><cms:show kit_desc /></div></cms:if>
                        </div>
                        <cms:if note_after_ru><div class="note-after"><cms:show note_after_ru /></div></cms:if>
                    </div>
                </cms:if>
            </div>
        </cms:show_repeatable>
    </div>

    <div id="bar-alc" class="tab-content">
        <cms:show_repeatable 'rep_bar_alc_v2'>
            <cms:set _st = bar_alc_subtab />
            <div class="menu-row" data-subtab="<cms:if _st><cms:show _st /><cms:else />other</cms:if>">
                <cms:if row_type='header'><h3 class="category-title gold-shimmer"><cms:show cat_title /></h3>
                <cms:else_if row_type='subheader' /><h4 class="subcat-title gold-shimmer"><cms:show subcat_title /></h4>
                <cms:else />
                    <div class="w-full pb-2 border-b border-white/5 mb-4">
                        <div class="grid grid-cols-[1fr_auto] gap-x-4 items-start">
                            <div class="text-white text-lg flex items-center flex-wrap">
                                <cms:show i_name />
                                <div class="badge-container">
                                    <cms:if item_tags='New'><span class="badge-item">New</span></cms:if>
                                    <cms:if item_tags='Hit'><span class="badge-item">Hit</span></cms:if>
                                    <cms:if item_tags='Special'><span class="badge-item">Special</span></cms:if>
                                    <cms:if item_tags='ChefвЂ™s Choice'><span class="badge-item badge-chef">Chef</span></cms:if>
                                </div>
                            </div>
                            <span class="price-tag gold-shimmer"><cms:show i_price /> в‚Ѕ</span>
                            <cms:if i_subheader><div class="col-span-2 text-[12px] text-gray-400 mt-1"><cms:show i_subheader /></div></cms:if>
                        </div>
                        <cms:if note_after_ru><div class="note-after"><cms:show note_after_ru /></div></cms:if>
                    </div>
                </cms:if>
            </div>
        </cms:show_repeatable>
    </div>

    <div id="bar-non" class="tab-content">
        <cms:show_repeatable 'rep_bar_non_v2'>
            <cms:set _st = drinks_subtab />
            <div class="menu-row" data-subtab="<cms:if _st><cms:show _st /><cms:else />other</cms:if>">
                <cms:if row_type='header'><h3 class="category-title gold-shimmer"><cms:show cat_title /></h3>
                <cms:else_if row_type='subheader' /><h4 class="subcat-title gold-shimmer"><cms:show subcat_title /></h4>
                <cms:else />
                    <div class="w-full pb-2 border-b border-white/5 mb-4">
                        <div class="grid grid-cols-[1fr_auto] gap-x-4 items-start">
                            <div class="text-white text-lg flex items-center flex-wrap">
                                <cms:show i_name />
                                <div class="badge-container">
                                    <cms:if item_tags='New'><span class="badge-item">New</span></cms:if>
                                    <cms:if item_tags='Hit'><span class="badge-item">Hit</span></cms:if>
                                    <cms:if item_tags='Special'><span class="badge-item">Special</span></cms:if>
                                    <cms:if item_tags='ChefвЂ™s Choice'><span class="badge-item badge-chef">Chef</span></cms:if>
                                </div>
                            </div>
                            <span class="price-tag gold-shimmer"><cms:show i_price /> в‚Ѕ</span>
                            <cms:if i_desc><div class="col-span-2 text-[12px] text-gray-400 mt-1"><cms:show i_desc /></div></cms:if>
                        </div>
                        <cms:if note_after_ru><div class="note-after"><cms:show note_after_ru /></div></cms:if>
                    </div>
                </cms:if>
            </div>
        </cms:show_repeatable>
    </div>

    <div id="promos" class="tab-content">
        <div class="taplink-block-wrapper">
            <div class="film-grain"></div>
            <div class="content-limiter">
                <header class="text-center mb-12">
                    <h1 class="font-serif-lux text-3xl text-white font-light italic m-0">
                        <cms:show promo_title />
                    </h1>
                    <div class="gold-line-fade"></div>
                    <p class="text-[12px] uppercase tracking-[0.4em] shimmer-gold font-medium m-0">
                        <cms:show promo_subtitle />
                    </p>
                </header>

                <div class="space-y-3">
                    <cms:show_repeatable 'list_promos_v2'>
                        <div class="promo-card">
                            <h2 class="font-serif-lux text-2xl text-white italic mb-1">
                                <cms:show p_title />
                            </h2>
                            <p class="text-[12px] text-gray-400 font-light leading-relaxed mb-3 tracking-wide">
                                <cms:show p_desc />
                            </p>
                            <div class="w-6 h-px bg-[#C5A059]/30 mx-auto mb-3"></div>
                            <p class="text-[9px] uppercase tracking-[0.2em] shimmer-gold font-medium">
                                <cms:show p_tag />
                            </p>
                        </div>
                    </cms:show_repeatable>
                </div>

                <footer class="mt-8 text-center">
                    <p class="text-[10px] uppercase tracking-[0.3em] font-medium m-0 italic shimmer-gold">
                        <cms:show promo_footer />
                    </p>
                </footer>

                <div style="margin-top: 40px; text-align: center; opacity: 0.7;">
                    <img src="/couch/uploads/image/div.webp" alt="Separator" style="max-width:280px; margin:0 auto;">
                </div>
            </div>
        </div>
    </div>

    <div class="action-area">
        <a href="https://garden-lounge.pro/admiralteyskaya/menu" class="btn-base"><span class="subtitle-gold">Р’РµСЂРЅСѓС‚СЊСЃСЏ РќР°Р·Р°Рґ</span></a>
        <div onclick="openLoyaltyModal()" class="btn-base btn-gold-fill">РџСЂРѕРіСЂР°РјРјР° Р»РѕСЏР»СЊРЅРѕСЃС‚Рё</div>
        <a href="https://garden-lounge.pro/admiralteyskaya/menu/visual/" class="btn-base"><span class="subtitle-gold">Р’РёР·СѓР°Р»СЊРЅРѕРµ РјРµРЅСЋ</span></a>
    </div>

</main>

<div id="loyalty-modal" onclick="closeLoyaltyModal()">
    <div class="modal-content" onclick="event.stopPropagation()">
        <span class="close-modal" onclick="closeLoyaltyModal()">&times;</span>
        <div class="modal-title gold-shimmer">Р’С‹Р±РµСЂРёС‚Рµ СЃРїРѕСЃРѕР± СЂРµРіРёСЃС‚СЂР°С†РёРё</div>

        <a href="https://access.clientomer.ru/feedback/676900-1/" target="_blank" class="modal-btn">
            <i class="fa-solid fa-wallet"></i> Р РµРіРёСЃС‚СЂР°С†РёСЏ С‡РµСЂРµР· Wallet
        </a>

        <a href="https://t.me/GardenLounge_Loyalty_Bot" target="_blank" class="modal-btn">
            <i class="fa-brands fa-telegram"></i> Р РµРіРёСЃС‚СЂР°С†РёСЏ С‡РµСЂРµР· Telegram
        </a>
    </div>
</div>

<script>
    const SUBTABS = { 'kitchen': 'subtabs-kitchen', 'bar-alc': 'subtabs-bar-alc', 'bar-non': 'subtabs-bar-non' };
    const ACTIVE = { 'kitchen': 'snacks', 'bar-alc': 'beer', 'bar-non': 'tea_coffee' };

    function switchTab(id) {
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        const target = document.getElementById(id);
        if(target) target.classList.add('active');

        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        const btn = Array.from(document.querySelectorAll('.tab-btn')).find(b => b.getAttribute('onclick').includes(id));
        if(btn) btn.classList.add('active');

        document.querySelectorAll('.subtabs-wrap').forEach(w => w.style.display = 'none');
        if(SUBTABS[id]) document.getElementById(SUBTABS[id]).style.display = 'flex';
        filter(id);
    }

    function switchSubtab(tab, sub) {
        ACTIVE[tab] = sub;
        const wrap = document.getElementById(SUBTABS[tab]);
        wrap.querySelectorAll('.subtab-btn').forEach(b => b.classList.remove('active'));
        const activeSubBtn = Array.from(wrap.querySelectorAll('.subtab-btn')).find(b => b.getAttribute('data-sub') === sub);
        if(activeSubBtn) activeSubBtn.classList.add('active');
        filter(tab);
    }

    function filter(tab) {
        const sub = ACTIVE[tab];
        const container = document.getElementById(tab);
        if(!container) return;
        const rows = container.querySelectorAll('.menu-row');
        rows.forEach(r => {
            const v = r.getAttribute('data-subtab');
            r.style.display = (tab === 'hookahs' || tab === 'promos' || v === sub) ? 'block' : 'none';
        });
    }

    function openLoyaltyModal() {
        document.getElementById('loyalty-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    function closeLoyaltyModal() {
        document.getElementById('loyalty-modal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    document.addEventListener('DOMContentLoaded', () => { switchTab('hookahs'); });
</script>

</body>
<?php COUCH::invoke(); ?>
</html>