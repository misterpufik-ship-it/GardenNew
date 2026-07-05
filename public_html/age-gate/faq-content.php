<?php

function gl_faq_branch_items($branch)
{
    if ($branch === 'udelnaya') {
        return array(
            array(
                'q' => 'Где кальянная Garden Lounge в Приморском районе?',
                'a' => 'ул. Аккуратова 13, Приморский район, рядом с метро Удельная. Маршрут — в разделе «Контакты».',
            ),
            array(
                'q' => 'До скольки работает Garden Lounge на Удельной?',
                'a' => 'Пн–Чт и Вс: 13:00–01:00. Пт–Сб: 13:00–03:00.',
            ),
            array(
                'q' => 'Сколько стоит кальян?',
                'a' => 'Цены — в текстовом меню на сайте. Меню общее для обоих филиалов.',
            ),
            array(
                'q' => 'Как забронировать стол?',
                'a' => 'Форма «Забронировать», телефон +7 950 047-33-65 или Telegram @Garden_lounge_spb.',
            ),
            array(
                'q' => 'Подойдёт ли для свидания или корпоратива?',
                'a' => 'Да. Камерная атмосфера и VIP-комнаты — уточните при бронировании.',
            ),
            array(
                'q' => 'Можно ли отметить день рождения?',
                'a' => 'Да: зал или VIP-комната, кальян, кухня и бар. Условия — у администратора.',
            ),
            array(
                'q' => 'Можно ли принести свой алкоголь или еду?',
                'a' => 'Нет. Работают собственные бар и кухня — заказы только из меню.',
            ),
            array(
                'q' => 'С какого возраста можно посещать?',
                'a' => '18+. Детей и подростков не пускаем.',
            ),
            array(
                'q' => 'Есть ли VIP-комнаты и PlayStation 5?',
                'a' => 'Да. Бронь — по телефону или в Telegram.',
            ),
            array(
                'q' => 'Есть ли кухня и бар?',
                'a' => 'Да. Кальянная с кухней и баром: закуски, горячее, коктейли, чай и кофе.',
            ),
        );
    }

    return array(
        array(
            'q' => 'Где кальянная Garden Lounge на Адмиралтейской?',
            'a' => 'Центр СПб, наб. реки Мойки 67–69, м. Адмиралтейская. Маршрут — в «Контактах».',
        ),
        array(
            'q' => 'До скольки работает Garden Lounge?',
            'a' => 'Пн–Чт и Вс: 12:00–01:00. Пт–Сб: 12:00–03:00.',
        ),
        array(
            'q' => 'Сколько стоит кальян?',
            'a' => 'Актуальные цены — в текстовом меню на сайте.',
        ),
        array(
            'q' => 'Как забронировать стол?',
            'a' => 'Форма «Забронировать», +7 995 624-68-08 или Telegram @gardenlounge_admiral.',
        ),
        array(
            'q' => 'Подойдёт ли для свидания или корпоратива?',
            'a' => 'Да. Эстетичный интерьер и VIP-комнаты — по брони.',
        ),
        array(
            'q' => 'Можно ли отметить день рождения?',
            'a' => 'Да: стол или VIP, кальян, кухня и бар. Детали — при бронировании.',
        ),
        array(
            'q' => 'Можно ли принести свой алкоголь или еду?',
            'a' => 'Нет. Свой алкоголь и еду приносить нельзя — только меню заведения.',
        ),
        array(
            'q' => 'С какого возраста можно в Garden Lounge?',
            'a' => '18+. Возможна проверка документа.',
        ),
        array(
            'q' => 'Есть ли VIP-комнаты и PlayStation 5?',
            'a' => 'Да. Наличие и бронь — по телефону или в Telegram.',
        ),
        array(
            'q' => 'Есть ли кухня и бар?',
            'a' => 'Да. Кальянная с кухней и баром — полное меню на сайте.',
        ),
    );
}

function gl_render_faq_schema($branch)
{
    $items = gl_faq_branch_items($branch);
    if (!$items) {
        return;
    }

    $entities = array();
    foreach ($items as $item) {
        $entities[] = array(
            '@type' => 'Question',
            'name' => $item['q'],
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text' => $item['a'],
            ),
        );
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

function gl_render_faq_section($branch)
{
    $items = gl_faq_branch_items($branch);
    if (!$items) {
        return;
    }

    gl_render_faq_schema($branch);

    echo '<section id="faq" class="gl-faq-section">';
    echo '<div class="gl-faq-grain"></div>';
    echo '<div class="gl-faq-inner widget-limiter">';
    echo '<div class="gl-section-head text-center mb-8 fade-up">';
    echo '<h2 class="gl-faq-title font-serif-lux">Частые вопросы</h2>';
    echo '<div class="golden-line"></div>';
    echo '<p class="gl-faq-subtitle subtitle-gold">FAQ Garden Lounge</p>';
    echo '</div>';
    echo '<div class="gl-faq-list fade-up" style="animation-delay:0.15s">';

    foreach ($items as $index => $item) {
        $id = 'gl-faq-item-' . ($index + 1);
        echo '<article class="gl-faq-item">';
        echo '<button type="button" class="gl-faq-question" aria-expanded="false" aria-controls="' . $id . '">';
        echo '<span class="gl-faq-question-text">' . htmlspecialchars($item['q'], ENT_QUOTES, 'UTF-8') . '</span>';
        echo '<i class="fas fa-chevron-down gl-faq-icon" aria-hidden="true"></i>';
        echo '</button>';
        echo '<div id="' . $id . '" class="gl-faq-answer" hidden>';
        echo '<p>' . htmlspecialchars($item['a'], ENT_QUOTES, 'UTF-8') . '</p>';
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
.gl-faq-item {
    border: 1px solid rgba(197, 160, 89, 0.2);
    background: rgba(255, 255, 255, 0.02);
}
.gl-faq-question {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 11px 14px;
    background: transparent;
    border: 0;
    color: #fff;
    text-align: left;
    font-family: 'Montserrat', sans-serif;
    cursor: pointer;
}
.gl-faq-question-text {
    flex: 1 1 auto;
    font-size: 11px;
    line-height: 1.45;
    letter-spacing: 0.03em;
    font-weight: 500;
}
.gl-faq-question:hover,
.gl-faq-item.is-open .gl-faq-question {
    color: #C5A059;
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
    font-size: 11px;
    line-height: 1.65;
    color: rgba(234, 234, 234, 0.82);
}
@media (max-width: 767px) {
    .gl-faq-question { padding: 10px 12px; }
    .gl-faq-question-text { font-size: 10px; }
    .gl-faq-answer p { font-size: 10px; }
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
