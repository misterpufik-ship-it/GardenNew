<?php

function gl_faq_masterpage($branch)
{
    return $branch === 'udelnaya' ? 'udelnaya/faq.php' : 'faq.php';
}

function gl_faq_template_registered($masterpage)
{
    global $DB;

    if (!isset($DB) || !is_object($DB) || !defined('K_TBL_TEMPLATES')) {
        return false;
    }

    $name = $DB->sanitize((string) $masterpage);
    $rs = $DB->select(K_TBL_TEMPLATES, array('id'), "name = '" . $name . "'");

    return is_array($rs) && count($rs) > 0;
}

function gl_faq_render_fallback($branch)
{
    $items = gl_faq_default_items($branch);
    gl_render_faq_styles();
    gl_render_faq_section_items(
        $items,
        array(
            'title' => 'Частые вопросы',
            'subtitle' => 'FAQ Garden Lounge',
        )
    );
    gl_render_faq_script();
}

function gl_faq_schema_text($plain, $html)
{
    $plain = trim((string) $plain);
    if ($plain !== '') {
        return $plain;
    }

    $html = trim((string) $html);
    if ($html === '') {
        return '';
    }

    return trim(preg_replace('/\s+/u', ' ', strip_tags($html)));
}

function gl_faq_default_items($branch)
{
    if ($branch === 'udelnaya') {
        return array(
            array(
                'q' => 'Где кальянная Garden Lounge в Приморском районе?',
                'a' => 'СПб, ул. Аккуратова 13, Приморский район, рядом с метро Удельная. Маршрут — в разделе «Контакты» или на карте.',
                'a_html' => 'СПб, ул. Аккуратова 13, Приморский район, рядом с метро Удельная. Маршрут — в разделе <a href="#contact">«Контакты»</a> или <a href="https://yandex.ru/maps/-/CPtpbQPg" target="_blank" rel="noopener noreferrer">на карте</a>.',
            ),
            array(
                'q' => 'До скольки работает Garden Lounge на Удельной?',
                'a' => 'Пн–Чт и Вс: 13:00–01:00. Пт–Сб: 13:00–03:00.',
            ),
            array(
                'q' => 'Сколько стоит кальян?',
                'a' => 'Актуальные цены — в текстовом меню на сайте. Меню общее для обоих филиалов.',
                'a_html' => 'Актуальные цены — в <a href="/udelnaya/menu/text">текстовом меню</a> на сайте. Меню общее для обоих филиалов.',
            ),
            array(
                'q' => 'Как забронировать стол?',
                'a' => 'Через форму «Забронировать», по телефону +7 950 047-33-65 или в Telegram @Garden_lounge_spb.',
                'a_html' => 'Через <a href="#reservation">форму «Забронировать»</a>, по телефону <a href="tel:+79500473365">+7 950 047-33-65</a> или в Telegram <a href="https://t.me/Garden_lounge_spb" target="_blank" rel="noopener noreferrer">@Garden_lounge_spb</a>.',
            ),
            array(
                'q' => 'Подойдёт ли для свидания или корпоратива?',
                'a' => 'Да. Камерная атмосфера и VIP-комнаты — забронируйте стол заранее.',
                'a_html' => 'Да. Камерная атмосфера и VIP-комнаты — <a href="#reservation">забронируйте стол</a> заранее.',
            ),
            array(
                'q' => 'Можно ли отметить день рождения?',
                'a' => 'Конечно! Забронируйте стол в зале или VIP-комнату — с кальянами, кухней и баром. Детали уточняйте при бронировании.',
                'a_html' => 'Конечно! Можно <a href="#reservation">забронировать стол</a> в зале или VIP-комнату — с кальянами, кухней и баром. Детали уточняйте <a href="#reservation">при бронировании</a>.',
            ),
            array(
                'q' => 'Можно ли принести свой алкоголь или еду?',
                'a' => 'Нет. Работают собственные бар и кухня — заказы только из меню заведения.',
                'a_html' => 'Нет. Работают собственные бар и кухня — заказы только из <a href="/udelnaya/menu/text">меню заведения</a>.',
            ),
            array(
                'q' => 'С какого возраста можно посещать?',
                'a' => '18+. Детей и подростков не пускаем.',
            ),
            array(
                'q' => 'Есть ли VIP-комнаты и PlayStation 5?',
                'a' => 'Да. Бронь — по телефону +7 950 047-33-65 или в Telegram @Garden_lounge_spb.',
                'a_html' => 'Да. Бронь — по телефону <a href="tel:+79500473365">+7 950 047-33-65</a> или в Telegram <a href="https://t.me/Garden_lounge_spb" target="_blank" rel="noopener noreferrer">@Garden_lounge_spb</a>.',
            ),
            array(
                'q' => 'Есть ли кухня и бар?',
                'a' => 'Да. Кальянная с кухней и баром: закуски, горячее, коктейли, чай и кофе — полное меню на сайте.',
                'a_html' => 'Да. Кальянная с кухней и баром: закуски, горячее, коктейли, чай и кофе — <a href="/udelnaya/menu/text">полное меню на сайте</a>.',
            ),
        );
    }

    return array(
        array(
            'q' => 'Где кальянная Garden Lounge на Адмиралтейской?',
            'a' => 'Центр СПб, наб. реки Мойки 67–69, м. Адмиралтейская. Маршрут — в разделе «Контакты» или на карте.',
            'a_html' => 'Центр СПб, наб. реки Мойки 67–69, м. Адмиралтейская. Маршрут — в разделе <a href="#contact">«Контакты»</a> или <a href="https://yandex.ru/maps/org/garden_lounge/92097430496/" target="_blank" rel="noopener noreferrer">на карте</a>.',
        ),
        array(
            'q' => 'До скольки работает Garden Lounge?',
            'a' => 'Пн–Чт и Вс: 12:00–01:00. Пт–Сб: 12:00–03:00.',
        ),
        array(
            'q' => 'Сколько стоит кальян?',
            'a' => 'Актуальные цены — в текстовом меню на сайте.',
            'a_html' => 'Актуальные цены — в <a href="/admiralteyskaya/menu/text">текстовом меню</a> на сайте.',
        ),
        array(
            'q' => 'Как забронировать стол?',
            'a' => 'Через форму «Забронировать», по телефону +7 995 624-68-08 или в Telegram @gardenlounge_admiral.',
            'a_html' => 'Через <a href="#reservation">форму «Забронировать»</a>, по телефону <a href="tel:+79956246808">+7 995 624-68-08</a> или в Telegram <a href="https://t.me/gardenlounge_admiral" target="_blank" rel="noopener noreferrer">@gardenlounge_admiral</a>.',
        ),
        array(
            'q' => 'Подойдёт ли для свидания или корпоратива?',
            'a' => 'Да. Эстетичный интерьер и VIP-комнаты — забронируйте стол заранее.',
            'a_html' => 'Да. Эстетичный интерьер и VIP-комнаты — <a href="#reservation">забронируйте стол</a> заранее.',
        ),
        array(
            'q' => 'Можно ли отметить день рождения?',
            'a' => 'Конечно! Забронируйте стол в зале или VIP-комнату — с кальянами, кухней и баром. Детали уточняйте при бронировании.',
            'a_html' => 'Конечно! Можно <a href="#reservation">забронировать стол</a> в зале или VIP-комнату — с кальянами, кухней и баром. Детали уточняйте <a href="#reservation">при бронировании</a>.',
        ),
        array(
            'q' => 'Можно ли принести свой алкоголь или еду?',
            'a' => 'Нет. Свой алкоголь и еду приносить нельзя — только меню заведения.',
            'a_html' => 'Нет. Свой алкоголь и еду приносить нельзя — только <a href="/admiralteyskaya/menu/text">меню заведения</a>.',
        ),
        array(
            'q' => 'С какого возраста можно в Garden Lounge?',
            'a' => '18+. Возможна проверка документа.',
        ),
        array(
            'q' => 'Есть ли VIP-комнаты и PlayStation 5?',
            'a' => 'Да. Наличие и бронь — по телефону +7 995 624-68-08 или в Telegram @gardenlounge_admiral.',
            'a_html' => 'Да. Наличие и бронь — по телефону <a href="tel:+79956246808">+7 995 624-68-08</a> или в Telegram <a href="https://t.me/gardenlounge_admiral" target="_blank" rel="noopener noreferrer">@gardenlounge_admiral</a>.',
        ),
        array(
            'q' => 'Есть ли кухня и бар?',
            'a' => 'Да. Кальянная с кухней и баром — полное меню на сайте.',
            'a_html' => 'Да. Кальянная с кухней и баром — <a href="/admiralteyskaya/menu/text">полное меню на сайте</a>.',
        ),
    );
}

function gl_faq_default_cms_rows($branch)
{
    $rows = array();
    foreach (gl_faq_default_items($branch) as $item) {
        $html = isset($item['a_html']) ? $item['a_html'] : htmlspecialchars($item['a'], ENT_QUOTES, 'UTF-8');
        $rows[] = array(
            'faq_question' => $item['q'],
            'faq_answer_schema' => $item['a'],
            'faq_answer_html' => $html,
        );
    }
    return $rows;
}

function gl_faq_answer_html($item)
{
    if (!empty($item['a_html'])) {
        return $item['a_html'];
    }

    return htmlspecialchars($item['a'], ENT_QUOTES, 'UTF-8');
}

function gl_render_faq_schema($items)
{
    if (!$items) {
        return;
    }

    $entities = array();
    foreach ($items as $item) {
        if (empty($item['q']) || empty($item['a'])) {
            continue;
        }
        $entities[] = array(
            '@type' => 'Question',
            'name' => $item['q'],
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text' => $item['a'],
            ),
        );
    }

    if (!$entities) {
        return;
    }

    $json = json_encode(
        array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $entities,
        ),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG
    );

    if ($json === false) {
        return;
    }

    echo '<script type="application/ld+json">' . $json . '</script>' . "\n";
}

function gl_render_faq_section_items($items, $options = array())
{
    if (!$items) {
        return;
    }

    $title = !empty($options['title']) ? $options['title'] : 'Частые вопросы';
    $subtitle = !empty($options['subtitle']) ? $options['subtitle'] : 'FAQ Garden Lounge';

    gl_render_faq_schema($items);

    echo '<section id="faq" class="gl-faq-section">';
    echo '<div class="gl-faq-grain"></div>';
    echo '<div class="gl-faq-inner widget-limiter">';
    echo '<div class="gl-section-head text-center mb-8 fade-up">';
    echo '<h2 class="gl-faq-title font-serif-lux">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h2>';
    echo '<div class="golden-line"></div>';
    echo '<p class="gl-faq-subtitle subtitle-gold">' . htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') . '</p>';
    echo '</div>';
    echo '<div class="gl-faq-list fade-up" style="animation-delay:0.15s">';

    $index = 0;
    foreach ($items as $item) {
        if (empty($item['q'])) {
            continue;
        }
        $index++;
        $id = 'gl-faq-item-' . $index;
        echo '<article class="gl-faq-item">';
        echo '<button type="button" class="gl-faq-question" aria-expanded="false" aria-controls="' . $id . '">';
        echo '<span class="gl-faq-question-text">' . htmlspecialchars($item['q'], ENT_QUOTES, 'UTF-8') . '</span>';
        echo '<i class="fas fa-chevron-down gl-faq-icon" aria-hidden="true"></i>';
        echo '</button>';
        echo '<div id="' . $id . '" class="gl-faq-answer" hidden>';
        echo '<p>' . gl_faq_answer_html($item) . '</p>';
        echo '</div>';
        echo '</article>';
    }

    echo '</div></div></section>';
}

function gl_render_faq_styles()
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    echo <<<'HTML'
<style>
.gl-faq-section {
    position: relative;
    background: #000;
    color: #EAEAEA;
    padding: var(--gl-py-faq, var(--gl-section-py, 28px)) 16px 32px;
    overflow: hidden;
}
.gl-faq-grain {
    position: absolute;
    inset: 0;
    background: url('/img/noise.svg');
    opacity: 0.04;
    pointer-events: none;
}
.gl-faq-inner {
    position: relative;
    z-index: 2;
    max-width: 640px;
    margin: 0 auto;
}
.gl-faq-title {
    font-size: clamp(24px, 4vw, 32px);
    font-weight: 300;
    font-style: italic;
    color: #fff;
    margin: 0;
}
.gl-faq-subtitle {
    margin-top: 8px;
    font-size: 10px;
    letter-spacing: 0.32em;
}
.gl-faq-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
@media (min-width: 768px) {
    .gl-faq-inner {
        max-width: 960px;
    }
    .gl-faq-list {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px 12px;
        align-items: start;
    }
}
.gl-faq-item {
    border: 1px solid rgba(197, 160, 89, 0.2);
    background: rgba(255, 255, 255, 0.02);
}
.gl-faq-question {
    width: 100%;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 14px;
    background: transparent;
    border: 0;
    color: inherit;
    text-align: left;
    font-family: 'Montserrat', sans-serif;
    cursor: pointer;
}
.gl-faq-question-text {
    flex: 1 1 auto;
    display: block;
    width: 100%;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 0.875rem;
    font-weight: 300;
    line-height: 1.625;
    letter-spacing: 0.025em;
    text-align: left;
    text-transform: none;
    color: #fff;
}
.gl-faq-icon {
    flex: 0 0 auto;
    font-size: 10px;
    transition: transform 0.25s ease;
    color: #C5A059;
}
.gl-faq-item.is-open .gl-faq-icon {
    transform: rotate(180deg);
}
.gl-faq-item.is-open .gl-faq-answer {
    display: block;
}
.gl-faq-answer {
    display: none;
    padding: 0 14px 12px;
}
.gl-faq-answer[hidden] {
    display: none !important;
}
.gl-faq-item.is-open .gl-faq-answer[hidden] {
    display: block !important;
}
.gl-faq-answer p {
    margin: 0;
    font-family: 'Montserrat', sans-serif !important;
    font-size: 0.875rem;
    font-weight: 300;
    line-height: 1.625;
    letter-spacing: 0.025em;
    background: linear-gradient(to right, var(--gold-dark) 0%, var(--gold-main) 40%, var(--gold-light) 50%, var(--gold-main) 60%, var(--gold-dark) 100%);
    background-size: 200% auto;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    color: transparent;
    animation: shineGold 5s linear infinite;
}
.gl-faq-answer p a {
    background: none;
    -webkit-background-clip: border-box;
    background-clip: border-box;
    -webkit-text-fill-color: #fff;
    color: #fff;
    text-decoration: underline;
    text-underline-offset: 2px;
    text-decoration-color: rgba(255, 255, 255, 0.45);
    transition: opacity 0.2s ease, text-decoration-color 0.2s ease;
}
.gl-faq-answer p a:hover {
    opacity: 0.85;
    text-decoration-color: rgba(255, 255, 255, 0.85);
}
@media (max-width: 767px) {
    .gl-faq-question { padding: 10px 12px; }
    .gl-faq-question-text { font-size: 0.8125rem; }
    .gl-faq-answer p { font-size: 0.8125rem; }
}
</style>
HTML;
}

function gl_render_faq_script()
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    echo <<<'HTML'
<script>
(function () {
    function initGlFaq() {
        document.querySelectorAll('.gl-faq-question').forEach(function (btn) {
            if (btn.dataset.glFaqBound === '1') return;
            btn.dataset.glFaqBound = '1';
            btn.addEventListener('click', function () {
                var item = btn.closest('.gl-faq-item');
                var panel = item.querySelector('.gl-faq-answer');
                var open = item.classList.contains('is-open');
                document.querySelectorAll('.gl-faq-item.is-open').forEach(function (other) {
                    if (other === item) return;
                    other.classList.remove('is-open');
                    var otherBtn = other.querySelector('.gl-faq-question');
                    var otherPanel = other.querySelector('.gl-faq-answer');
                    if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
                    if (otherPanel) otherPanel.hidden = true;
                });
                item.classList.toggle('is-open', !open);
                btn.setAttribute('aria-expanded', open ? 'false' : 'true');
                panel.hidden = open;
            });
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGlFaq);
    } else {
        initGlFaq();
    }
})();
</script>
HTML;
}

function gl_render_faq_assets()
{
    gl_render_faq_styles();
    gl_render_faq_script();
}
