<?php

function gl_menu_en_or_translate($en, $ru)
{
    $en = trim((string) $en);
    if ($en !== '') {
        return $en;
    }

    $ru = trim((string) $ru);
    if ($ru === '') {
        return '';
    }

    // Fallback to RU text — live translation API removed (blocked page render).
    return $ru;
}

function gl_menu_en_field($en, $ru, $defaultEn = '')
{
    $en = trim((string) $en);
    if ($en !== '') {
        return $en;
    }
    if ($defaultEn !== '') {
        return $defaultEn;
    }
    return gl_menu_en_or_translate('', $ru);
}

function gl_menu_en_echo($en, $ru, $defaultEn = '')
{
    echo htmlspecialchars(gl_menu_en_field($en, $ru, $defaultEn), ENT_QUOTES, 'UTF-8');
}
