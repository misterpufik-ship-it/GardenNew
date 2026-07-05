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

    static $cache = array();
    if (isset($cache[$ru])) {
        return $cache[$ru];
    }

    $url = 'https://api.mymemory.translated.net/get?q=' . rawurlencode($ru) . '&langpair=ru|en';
    $response = @file_get_contents($url);
    if ($response !== false) {
        $data = json_decode($response, true);
        if (!empty($data['responseData']['translatedText'])) {
            $translated = trim((string) $data['responseData']['translatedText']);
            if ($translated !== '') {
                $cache[$ru] = $translated;
                return $translated;
            }
        }
    }

    $cache[$ru] = $ru;
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
