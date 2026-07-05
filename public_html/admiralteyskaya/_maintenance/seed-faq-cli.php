<?php
/**
 * Seed FAQ sections for both branches (only if faq_list is empty).
 * CLI: php _maintenance/seed-faq-cli.php
 */
if (PHP_SAPI !== 'cli' && !defined('GL_SKIP_CLI_CHECK')) {
    http_response_code(403);
    exit("CLI only\n");
}

$root = realpath(__DIR__ . '/..');
chdir($root);
require_once $root . '/couch/cms.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/age-gate/faq-content.php';

global $AUTH, $FUNCS;

if (!isset($AUTH->user) || !is_object($AUTH->user)) {
    fwrite(STDERR, "Couch auth not initialized\n");
    exit(1);
}

$AUTH->user->access_level = K_ACCESS_LEVEL_SUPER_ADMIN;

function faq_get_page_field($pg, $name)
{
    if (isset($pg->_fields[$name])) {
        return $pg->_fields[$name];
    }
    if (isset($pg->fields[$name])) {
        return $pg->fields[$name];
    }
    return null;
}

function faq_field_value($value)
{
    if (is_object($value) && method_exists($value, 'get_data')) {
        return trim((string) $value->get_data());
    }
    return trim((string) $value);
}

function faq_read_repeatable_rows($field)
{
    $rows = array();
    if (!$field) {
        return $rows;
    }

    if (method_exists($field, 'get_rows')) {
        $sourceRows = $field->get_rows(true);
    } elseif (isset($field->rows) && is_array($field->rows)) {
        $sourceRows = $field->rows;
    } else {
        return $rows;
    }

    foreach ($sourceRows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $question = isset($row['faq_question']) ? faq_field_value($row['faq_question']) : '';
        if ($question === '') {
            continue;
        }
        $rows[] = $row;
    }

    return $rows;
}

function faq_save_repeatable_field($pg, $fieldName, $rows)
{
    $field = faq_get_page_field($pg, $fieldName);
    if (!$field) {
        throw new RuntimeException('Field ' . $fieldName . ' missing');
    }
    $field->store_posted_changes($rows, 'db_persist');
}

function faq_seed_template($templateName, $branch)
{
    global $FUNCS;

    $FUNCS->invalidate_cache();
    $pg = new KWebpage($templateName);
    if (!empty($pg->error)) {
        throw new RuntimeException('Failed to load ' . $templateName . ': ' . $pg->err_msg);
    }

    $existing = faq_read_repeatable_rows(faq_get_page_field($pg, 'faq_list'));
    if ($existing) {
        echo "{$templateName}: already has " . count($existing) . " items, skip\n";
        return;
    }

    faq_save_repeatable_field($pg, 'faq_list', gl_faq_default_cms_rows($branch));

    $titleField = faq_get_page_field($pg, 'faq_main_title');
    if ($titleField && faq_field_value($titleField) === '') {
        $titleField->store_posted_changes('Частые вопросы', 'db_persist');
    }
    $subtitleField = faq_get_page_field($pg, 'faq_subtitle');
    if ($subtitleField && faq_field_value($subtitleField) === '') {
        $subtitleField->store_posted_changes('FAQ Garden Lounge', 'db_persist');
    }

    $errors = $pg->save('db_persist');
    if ($errors) {
        throw new RuntimeException('Save failed for ' . $templateName . ' (' . $errors . ' errors)');
    }

    echo "{$templateName}: seeded " . count(gl_faq_default_cms_rows($branch)) . " items\n";
}

try {
    faq_seed_template('faq.php', 'admiralteyskaya');
    faq_seed_template('udelnaya/faq.php', 'udelnaya');
    $FUNCS->invalidate_cache();
    echo "FAQ seed complete.\n";
} catch (Exception $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
