<?php

function garden_menu_copy_branches()
{
    return array(
        'admiralteyskaya' => 'menu/text/index.php',
        'udelnaya' => 'udelnaya/menu/text/index.php',
    );
}

function garden_menu_copy_repeatables()
{
    return array(
        'rep_hookahs_v2',
        'rep_kitchen_v2',
        'rep_bar_alc_v2',
        'rep_bar_non_v2',
        'list_promos_v2',
    );
}

function garden_menu_copy_field_value($value)
{
    if (is_object($value) && method_exists($value, 'get_data')) {
        return (string) $value->get_data();
    }

    return (string) $value;
}

function garden_menu_copy_read_rows($field)
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

        $normalized = array();
        foreach ($row as $key => $value) {
            if (!is_string($key) || $key === '' || $key[0] === '_') {
                continue;
            }
            $normalized[$key] = garden_menu_copy_field_value($value);
        }

        $rows[] = $normalized;
    }

    return $rows;
}

function garden_menu_copy_get_page_field($pg, $name)
{
    if (isset($pg->_fields[$name])) {
        return $pg->_fields[$name];
    }
    if (isset($pg->fields[$name])) {
        return $pg->fields[$name];
    }

    return null;
}

function garden_menu_copy_row_signature($row)
{
    $parts = array();
    foreach ($row as $key => $value) {
        $parts[] = $key . '=' . trim((string) $value);
    }
    sort($parts);

    return implode('|', $parts);
}

function garden_menu_copy_row_exists($rows, $candidate)
{
    $signature = garden_menu_copy_row_signature($candidate);

    foreach ($rows as $row) {
        if (garden_menu_copy_row_signature($row) === $signature) {
            return true;
        }
    }

    return false;
}

function garden_menu_copy_save_rows($pg, $fieldName, $rows)
{
    $field = garden_menu_copy_get_page_field($pg, $fieldName);
    if (!$field) {
        throw new RuntimeException('Repeatable field not found: ' . $fieldName);
    }

    $field->store_posted_changes($rows, 'db_persist');
}

function garden_menu_copy_append_row($targetBranch, $repeatable, array $row)
{
    global $FUNCS;

    $branches = garden_menu_copy_branches();
    $repeatables = garden_menu_copy_repeatables();

    if (!isset($branches[$targetBranch])) {
        throw new InvalidArgumentException('Unknown target branch.');
    }
    if (!in_array($repeatable, $repeatables, true)) {
        throw new InvalidArgumentException('Unknown repeatable field.');
    }

    $row = array_map('strval', $row);
    if (!count($row)) {
        throw new InvalidArgumentException('Row data is empty.');
    }

    $FUNCS->invalidate_cache();

    $pg = new KWebpage($branches[$targetBranch]);
    if (!empty($pg->error)) {
        throw new RuntimeException('Failed to load target page: ' . $pg->err_msg);
    }

    $existing = garden_menu_copy_read_rows(garden_menu_copy_get_page_field($pg, $repeatable));
    if (garden_menu_copy_row_exists($existing, $row)) {
        return array(
            'status' => 'exists',
            'message' => 'This item is already present in the target branch.',
            'count' => count($existing),
        );
    }

    $existing[] = $row;
    garden_menu_copy_save_rows($pg, $repeatable, $existing);

    $errors = $pg->save('db_persist');
    if ($errors) {
        throw new RuntimeException('Save failed (' . $errors . ' errors).');
    }

    $FUNCS->invalidate_cache();

    return array(
        'status' => 'copied',
        'message' => 'Item copied to the second branch.',
        'count' => count($existing),
    );
}

function garden_menu_copy_handle_request()
{
    global $AUTH, $FUNCS;

    header('Content-Type: application/json; charset=utf-8');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(array('ok' => false, 'error' => 'Method not allowed.'), JSON_UNESCAPED_UNICODE);
        return;
    }

    if (!isset($AUTH->user) || !is_object($AUTH->user) || !$AUTH->user->id) {
        http_response_code(401);
        echo json_encode(array('ok' => false, 'error' => 'Admin login required.'), JSON_UNESCAPED_UNICODE);
        return;
    }

    if ((int) $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN) {
        http_response_code(403);
        echo json_encode(array('ok' => false, 'error' => 'Insufficient permissions.'), JSON_UNESCAPED_UNICODE);
        return;
    }

    $raw = file_get_contents('php://input');
    $payload = json_decode($raw, true);
    if (!is_array($payload)) {
        $payload = $_POST;
    }

    $targetBranch = isset($payload['target_branch']) ? trim((string) $payload['target_branch']) : '';
    $repeatable = isset($payload['repeatable']) ? trim((string) $payload['repeatable']) : '';
    $row = isset($payload['row']) && is_array($payload['row']) ? $payload['row'] : array();

    try {
        $result = garden_menu_copy_append_row($targetBranch, $repeatable, $row);
        echo json_encode(array(
            'ok' => true,
            'status' => $result['status'],
            'message' => $result['message'],
            'count' => $result['count'],
        ), JSON_UNESCAPED_UNICODE);
    } catch (InvalidArgumentException $e) {
        http_response_code(400);
        echo json_encode(array('ok' => false, 'error' => $e->getMessage()), JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array('ok' => false, 'error' => $e->getMessage()), JSON_UNESCAPED_UNICODE);
    }
}
