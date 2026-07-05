<?php

/**
 * Extract first integer from review count labels like "480+ отзывов".
 */
function gl_schema_parse_review_count($text)
{
    if (preg_match('/(\d+)/', (string) $text, $m)) {
        return (int) $m[1];
    }

    return 0;
}

/**
 * Build AggregateRating array for LocalBusiness JSON-LD (Yandex — primary source on page).
 */
function gl_schema_aggregate_rating($ratingValue, $reviewCountText)
{
    $ratingValue = trim((string) $ratingValue);
    $reviewCount = gl_schema_parse_review_count($reviewCountText);

    if ($ratingValue === '' || $reviewCount < 1) {
        return null;
    }

    return array(
        '@type' => 'AggregateRating',
        'ratingValue' => $ratingValue,
        'reviewCount' => $reviewCount,
        'bestRating' => '5',
        'worstRating' => '1',
    );
}

/**
 * Echo LocalBusiness JSON-LD script for a branch page.
 */
function gl_render_localbusiness_schema(array $opts)
{
    $orgId = 'https://garden-lounge.pro/#organization';
    $data = array(
        '@context' => 'https://schema.org',
        '@type' => array('BarOrPub', 'Restaurant'),
        '@id' => $opts['id'],
        'name' => $opts['name'],
        'url' => $opts['url'],
        'image' => $opts['image'],
        'telephone' => $opts['telephone'],
        'priceRange' => '$$',
        'servesCuisine' => array('Hookah lounge', 'Kitchen', 'Bar'),
        'parentOrganization' => array('@id' => $orgId),
        'address' => array(
            '@type' => 'PostalAddress',
            'streetAddress' => $opts['streetAddress'],
            'addressLocality' => 'Санкт-Петербург',
            'addressCountry' => 'RU',
        ),
        'geo' => array(
            '@type' => 'GeoCoordinates',
            'latitude' => $opts['latitude'],
            'longitude' => $opts['longitude'],
        ),
        'openingHoursSpecification' => $opts['openingHours'],
        'sameAs' => $opts['sameAs'],
        'hasMap' => $opts['hasMap'],
    );

    $rating = gl_schema_aggregate_rating(
        isset($opts['ratingValue']) ? $opts['ratingValue'] : '',
        isset($opts['ratingCountText']) ? $opts['ratingCountText'] : ''
    );
    if ($rating !== null) {
        $data['aggregateRating'] = $rating;
    }

    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
    if ($json === false) {
        return;
    }

    echo '<script type="application/ld+json">' . $json . '</script>' . "\n";
}

/**
 * Echo WebSite + Organization @graph for the homepage.
 */
function gl_render_home_schema_graph($logoUrl, array $sameAs)
{
    $sameAs = array_values(array_filter(array_map('trim', $sameAs)));

    $graph = array(
        array(
            '@type' => 'WebSite',
            '@id' => 'https://garden-lounge.pro/#website',
            'url' => 'https://garden-lounge.pro/',
            'name' => 'Garden Lounge',
            'description' => 'Сеть кальянных и лаунж-баров Garden Lounge в Санкт-Петербурге: филиалы Адмиралтейская и Удельная.',
            'inLanguage' => 'ru-RU',
            'publisher' => array('@id' => 'https://garden-lounge.pro/#organization'),
        ),
        array(
            '@type' => 'Organization',
            '@id' => 'https://garden-lounge.pro/#organization',
            'name' => 'Garden Lounge',
            'url' => 'https://garden-lounge.pro/',
            'logo' => $logoUrl,
            'sameAs' => $sameAs,
            'department' => array(
                array('@id' => 'https://garden-lounge.pro/admiralteyskaya/#localbusiness'),
                array('@id' => 'https://garden-lounge.pro/udelnaya/#localbusiness'),
            ),
        ),
    );

    $json = json_encode(
        array('@context' => 'https://schema.org', '@graph' => $graph),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG
    );
    if ($json === false) {
        return;
    }

    echo '<script type="application/ld+json">' . $json . '</script>' . "\n";
}
