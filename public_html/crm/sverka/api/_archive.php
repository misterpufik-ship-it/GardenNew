<?php

require dirname(__DIR__, 2) . '/_lib/storage.php';

function sverka_load_archive(): array
{
    $archive = crm_read_json('sverka', 'archive.json', ['sessions' => [], 'activeKey' => null]);
    if (!is_array($archive['sessions'] ?? null)) {
        $archive['sessions'] = [];
    }

    if (!$archive['sessions']) {
        $legacy = crm_read_json('sverka', 'data.json', []);
        if (is_array($legacy) && !empty($legacy['expenses'])) {
            $month = $legacy['month'] ?? null;
            if (!$month) {
                $date = $legacy['reconciledAt'] ?? ($legacy['expenses'][0]['date'] ?? date('Y-m-d'));
                $month = substr((string) $date, 0, 7);
            }
            $entity = $legacy['entity'] ?? 'lounge';
            $key = $month . '|' . $entity;
            $legacy['month'] = $month;
            $legacy['sessionKey'] = $key;
            $archive['sessions'][$key] = $legacy;
            $archive['activeKey'] = $key;
            crm_write_json('sverka', 'archive.json', $archive);
        }
    }

    return $archive;
}
