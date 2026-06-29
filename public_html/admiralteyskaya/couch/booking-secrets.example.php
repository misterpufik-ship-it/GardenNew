<?php
/**
 * Опциональный fallback, если Bot Token ещё не внесён в CouchCMS.
 * Скопируйте в booking-secrets.php (файл в .gitignore) только на сервере.
 */
return array(
    'admiral' => array(
        'bot_token' => '',
        'chat_id' => '',
        'branch_label' => 'Адмиралтейская',
        'vk_access_token' => '',
        'vk_peer_id' => '',
    ),
    'udelnaya' => array(
        'bot_token' => '',
        'chat_id' => '',
        'branch_label' => 'Удельная',
        'vk_access_token' => '',
        'vk_peer_id' => '',
    ),
);
