<?php

function gl_menu_en_known_translations()
{
    static $map = null;
    if ($map !== null) {
        return $map;
    }

    $map = array(
        'Дымный ланч' => 'Smoky Lunch',
        'Магический момент спокойствия в самом сердце мегаполиса. Время замедлить ход событий.' =>
            'A magical moment of calm in the heart of the city. Time to slow down.',
        '30% СКИДКА • БУДНИ ДО 17:00 • НА КЛАССИЧЕСКУЮ ЧАШКУ' =>
            '30% OFF • WEEKDAYS BEFORE 5 PM • ON CLASSIC BOWL',

        'Женская среда' => "Ladies' Wednesday",
        'Там, где не нужно спешить и никому ничего доказывать. Только вы, подруги и мягкий дым любимых вкусов. Для ваших спокойных вечеров.' =>
            'A place where you do not have to rush or prove anything. Just you, your friends, and the soft smoke of your favourite flavours.',
        '50% СКИДКА • ЖЕНСКИМ КОМПАНИЯМ ОТ ДВУХ ЧЕЛОВЕК • НА КЛАССИЧЕСКУЮ ЧАШКУ' =>
            '50% OFF • FOR LADIES GROUPS OF 2+ • ON CLASSIC BOWL',

        'День рождение' => 'Birthday',
        'День рождения' => 'Birthday',
        'День, когда хочется прожить не спеша, в кругу самых близких, где каждая минута - про радость и тепло.' =>
            'A day to take slowly, surrounded by your closest people, where every minute is about joy and warmth.',
        '10% СКИДКА • 3 ДНЯ ДО • 7 ДНЕЙ ПОСЛЕ' =>
            '10% OFF • 3 DAYS BEFORE • 7 DAYS AFTER',

        'Спасибо, что делитесь моментом' => 'Thanks for sharing the moment',
        'Иногда самое ценное - это не просто впечатления, а возможность ими поделиться. Спасибо, что выбираете нас и рассказываете об этом всему миру.' =>
            'Sometimes the most valuable thing is not just the experience, but the chance to share it. Thank you for choosing us and telling the world about it.',
        '10% СКИДКА НА СЛЕДУЮЩЕЕ ПОСЕЩЕНИЕ  • ЗА ОТЗЫВ  ИЛИ ОТМЕТКУ' =>
            '10% OFF YOUR NEXT VISIT • FOR A REVIEW OR SOCIAL MENTION',
        '10% СКИДКА НА СЛЕДУЮЩЕЕ ПОСЕЩЕНИЕ • ЗА ОТЗЫВ ИЛИ ОТМЕТКУ' =>
            '10% OFF YOUR NEXT VISIT • FOR A REVIEW OR SOCIAL MENTION',

        'Специальные предложения' => 'Special Offers',
        'Идеальное место для ценителей прекрасного.' => 'The perfect place for connoisseurs.',
        'Чтобы не запутаться в деталях, спросите у администратора, он с удовольствием подскажет.' =>
            'If you need details, ask the host — they will be happy to help.',
    );

    return $map;
}

function gl_menu_en_translate_known($text)
{
    $text = trim((string) $text);
    if ($text === '') {
        return '';
    }

    $map = gl_menu_en_known_translations();
    if (isset($map[$text])) {
        return $map[$text];
    }

    return $text;
}

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

    return gl_menu_en_translate_known($ru);
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
