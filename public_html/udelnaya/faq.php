<?php
define('K_TEMPLATE_NAME', 'udelnaya/faq.php');
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

<cms:template title='FAQ — Уделка' name='faq_section' executable='0' order='35'>

    <cms:editable name='faq_admin_help' type='message' order='0'>
        <div style="font-size:13px;line-height:1.6;">
            <p><strong>Как редактировать FAQ</strong></p>
            <ul style="margin:8px 0;padding-left:18px;">
                <li><strong>Вопрос</strong> — текст в аккордеоне.</li>
                <li><strong>Ответ на сайте</strong> — можно HTML-ссылки: <code>&lt;a href="#contact"&gt;Контакты&lt;/a&gt;</code>, <code>&lt;a href="#reservation"&gt;Забронировать&lt;/a&gt;</code>, <code>&lt;a href="tel:+79500473365"&gt;+7 950 047-33-65&lt;/a&gt;</code>, <code>&lt;a href="https://t.me/Garden_lounge_spb" target="_blank" rel="noopener"&gt;Telegram&lt;/a&gt;</code>, <code>&lt;a href="/udelnaya/menu/text/"&gt;Меню&lt;/a&gt;</code>.</li>
                <li><strong>Текст для поисковиков</strong> — без HTML. Если пусто, подставится автоматически из ответа на сайте.</li>
            </ul>
        </div>
    </cms:editable>

    <cms:editable name='group_titles' label='Заголовки секции' type='group' order='1' />

    <cms:editable name='faq_main_title'
        label='Заголовок'
        group='group_titles'
        type='text'>Частые вопросы</cms:editable>

    <cms:editable name='faq_subtitle'
        label='Подзаголовок'
        group='group_titles'
        type='text'>FAQ Garden Lounge</cms:editable>

    <cms:editable name='group_faq' label='Список вопросов' type='group' order='2' />

    <cms:repeatable name='faq_list' label='Вопросы и ответы' group='group_faq'>
        <cms:editable name='faq_question' label='Вопрос' type='text' />
        <cms:editable name='faq_answer_html' label='Ответ на сайте (HTML)' type='textarea' />
        <cms:editable name='faq_answer_schema' label='Текст для поисковиков (без HTML)' type='textarea' />
    </cms:repeatable>

</cms:template>

<?php COUCH::invoke(); ?>
