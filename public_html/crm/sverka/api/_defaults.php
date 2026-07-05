<?php

function sverka_default_config(): array
{
    $path = dirname(__DIR__) . '/config.json';
    if (is_file($path)) {
        $decoded = json_decode(file_get_contents($path), true);
        if (is_array($decoded)) {
            return $decoded;
        }
    }

    return [
        'entity' => 'lounge',
        'ignoreKopecks' => true,
        'paymentFilter' => 'бн',
        'lounge' => [
            'report' => [
                'sheetIndex' => 0,
                'dateCol' => 1,
                'categoryCol' => 6,
                'sumCol' => 7,
                'paymentCol' => 8,
                'commentCol' => 9,
            ],
            'statement' => [
                'sheetIndex' => 0,
                'dateCol' => 1,
                'opNumCol' => 2,
                'counterpartyCol' => 3,
                'purposeCol' => 6,
                'debitCol' => 10,
            ],
        ],
        'vympel' => [
            'reportSheets' => [0, 1],
            'dateCol' => 1,
            'categoryCol' => 6,
            'sumCol' => 7,
            'paymentCol' => 8,
            'commentCol' => 9,
            'statement' => [
                'sheetIndex' => 0,
                'headerRow' => 1,
                'dateCol' => 1,
                'accountCol' => 2,
                'counterpartyCol' => 3,
                'debitCol' => 4,
                'docNumCol' => 5,
                'purposeCol' => 6,
            ],
        ],
    ];
}

function sverka_merge_config(array $stored): array
{
    $defaults = sverka_default_config();
    if (!$stored) {
        return $defaults;
    }
    return array_replace_recursive($defaults, $stored);
}
