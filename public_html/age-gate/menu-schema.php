<?php

function gl_menu_branch_og_image($branch)
{
    if ($branch === 'udelnaya') {
        return 'https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/kalyannaya-garden-lounge-udelnaya-interer-spb.jpg';
    }

    return 'https://garden-lounge.pro/admiralteyskaya/couch/uploads/image/garden-main.jpg';
}

function gl_menu_og_render(array $opts)
{
    $branch = isset($opts['branch']) ? $opts['branch'] : 'admiralteyskaya';
    $url = isset($opts['url']) ? trim((string) $opts['url']) : '';
    $title = isset($opts['title']) ? trim((string) $opts['title']) : '';
    $description = isset($opts['description']) ? trim((string) $opts['description']) : '';

    if ($url === '' || $title === '') {
        return;
    }

    $image = gl_menu_branch_og_image($branch);
    $titleEsc = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $descEsc = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
    $urlEsc = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    $imageEsc = htmlspecialchars($image, ENT_QUOTES, 'UTF-8');

    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:url" content="' . $urlEsc . '">' . "\n";
    echo '<meta property="og:title" content="' . $titleEsc . '">' . "\n";
    if ($description !== '') {
        echo '<meta property="og:description" content="' . $descEsc . '">' . "\n";
    }
    echo '<meta property="og:image" content="' . $imageEsc . '">' . "\n";
    echo '<meta property="og:site_name" content="Garden Lounge">' . "\n";
    echo '<meta property="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta property="twitter:url" content="' . $urlEsc . '">' . "\n";
    echo '<meta property="twitter:title" content="' . $titleEsc . '">' . "\n";
    if ($description !== '') {
        echo '<meta property="twitter:description" content="' . $descEsc . '">' . "\n";
    }
    echo '<meta property="twitter:image" content="' . $imageEsc . '">' . "\n";
}

function gl_menu_seo_schema_render(array $opts)
{
    $branch = isset($opts['branch']) ? $opts['branch'] : 'admiralteyskaya';
    $pageType = isset($opts['page']) ? $opts['page'] : 'hub';
    $pageUrl = isset($opts['url']) ? $opts['url'] : '';
    $pageName = isset($opts['name']) ? $opts['name'] : '';
    $pageDesc = isset($opts['description']) ? $opts['description'] : '';
    $lang = isset($opts['lang']) ? $opts['lang'] : 'ru-RU';

    if ($pageUrl === '') {
        return;
    }

    $branchName = $branch === 'udelnaya' ? 'Удельная' : 'Адмиралтейская';
    $branchUrl = 'https://garden-lounge.pro' . $branch;
    $branchId = $branchUrl . '#localbusiness';
    $homeUrl = 'https://garden-lounge.pro';
    $menuHubUrl = $branchUrl . '/menu';

    $crumbs = array(
        array(
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Garden Lounge',
            'item' => $homeUrl,
        ),
        array(
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Garden Lounge — ' . $branchName,
            'item' => $branchUrl,
        ),
    );

    if ($pageType === 'hub') {
        $crumbs[] = array(
            '@type' => 'ListItem',
            'position' => 3,
            'name' => 'Меню',
            'item' => $pageUrl,
        );
    } else {
        $crumbNames = array(
            'text' => 'Текстовое меню',
            'visual' => 'Визуальное меню',
            'english' => 'English menu',
        );
        $leafName = isset($crumbNames[$pageType]) ? $crumbNames[$pageType] : $pageName;

        $crumbs[] = array(
            '@type' => 'ListItem',
            'position' => 3,
            'name' => 'Меню',
            'item' => $menuHubUrl,
        );
        $crumbs[] = array(
            '@type' => 'ListItem',
            'position' => 4,
            'name' => $leafName,
            'item' => $pageUrl,
        );
    }

    $graph = array(
        array(
            '@type' => 'BreadcrumbList',
            '@id' => $pageUrl . '#breadcrumb',
            'itemListElement' => $crumbs,
        ),
    );

    if ($pageType !== 'hub') {
        $menu = array(
            '@type' => 'Menu',
            '@id' => $pageUrl . '#menu',
            'name' => $pageName,
            'url' => $pageUrl,
            'inLanguage' => $lang,
            'provider' => array('@id' => $branchId),
        );
        if ($pageDesc !== '') {
            $menu['description'] = $pageDesc;
        }
        $graph[] = $menu;
    }

    $json = json_encode(
        array(
            '@context' => 'https://schema.org',
            '@graph' => $graph,
        ),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG
    );

    if ($json === false) {
        return;
    }

    echo '<script type="application/ld+json">' . $json . '</script>' . "\n";
}

function gl_menu_format_multiline_html($text, $wineMode = false)
{
    $text = trim((string) $text);
    if ($text === '') {
        return '';
    }

    if ($wineMode) {
        $text = preg_replace('/\s+(Сорт винограда:)/u', "\n$1", $text);
    }

    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function gl_menu_format_note_html($note, $wineMode = false)
{
    return gl_menu_format_multiline_html($note, $wineMode);
}

function gl_menu_get_note_text($lang = 'ru')
{
    global $CTX;
    if ($lang === 'en') {
        $en = trim((string) $CTX->get('note_after_ru_en'));
        if ($en !== '') {
            return $en;
        }
    }

    return trim((string) $CTX->get('note_after_ru'));
}

function gl_menu_render_note_after(array $opts = array())
{
    $wineMode = !empty($opts['wine']);
    $inGrid = !empty($opts['in_grid']);
    $lang = !empty($opts['en']) ? 'en' : 'ru';
    $note = gl_menu_get_note_text($lang);
    if ($note === '') {
        return;
    }

    $cls = 'note-after menu-multiline' . ($wineMode ? ' note-after--wine' : '');
    if ($inGrid) {
        $cls .= ' col-span-2';
    }

    echo '<div class="' . htmlspecialchars($cls, ENT_QUOTES, 'UTF-8') . '">';
    echo gl_menu_format_note_html($note, $wineMode);
    echo '</div>';
}
