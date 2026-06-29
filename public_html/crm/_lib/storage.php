<?php
/**
 * Постоянное хранилище CRM на хостинге (папка storage/ исключена из деплоя).
 */

function crm_root(): string
{
    return dirname(__DIR__);
}

function crm_storage_dir(string $module): string
{
    return crm_root() . '/storage/' . $module;
}

function crm_storage_path(string $module, string $filename): string
{
    return crm_storage_dir($module) . '/' . $filename;
}

function crm_ensure_storage(string $module, string ...$subdirs): string
{
    $dir = crm_storage_dir($module);
    foreach ($subdirs as $sub) {
        $dir .= '/' . trim($sub, '/');
    }
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        throw new RuntimeException('Не удалось создать каталог хранения');
    }
    return $dir;
}

function crm_read_json(string $module, string $filename, array $default = []): array
{
    crm_ensure_storage($module);
    $path = crm_storage_path($module, $filename);

    if (is_file($path)) {
        $decoded = json_decode(file_get_contents($path), true);
        if (is_array($decoded)) {
            return $decoded;
        }
    }

    $legacy = crm_legacy_json_path($module, $filename);
    if ($legacy && is_file($legacy)) {
        $decoded = json_decode(file_get_contents($legacy), true);
        if (is_array($decoded)) {
            crm_write_json($module, $filename, $decoded);
            return $decoded;
        }
    }

    return $default;
}

function crm_write_json(string $module, string $filename, array $data): void
{
    crm_ensure_storage($module);
    $path = crm_storage_path($module, $filename);
    $encoded = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($encoded === false || file_put_contents($path, $encoded . "\n") === false) {
        throw new RuntimeException('Ошибка записи данных');
    }
}

function crm_legacy_json_path(string $module, string $filename): ?string
{
    $map = [
        'odr' => ['reports.json' => 'reports.json'],
        'marketing' => ['reports.json' => 'reports.json'],
        'reglament' => ['reports.json' => 'reports.json'],
        'tasks' => ['tasks.json' => 'tasks.json'],
        'sverka' => ['data.json' => 'data.json', 'config.json' => 'config.json'],
    ];

    if (!isset($map[$module][$filename])) {
        return null;
    }

    return crm_root() . '/' . $module . '/' . $map[$module][$filename];
}

function crm_uploads_dir(string $module, string ...$parts): string
{
    $path = crm_ensure_storage($module, 'uploads', ...$parts);
    return $path;
}

function crm_find_upload(string $module, string $id, string $listKey = 'reports'): ?array
{
    $manifest = crm_read_json($module, 'reports.json', [$listKey => []]);
    $items = $manifest[$listKey] ?? $manifest['items'] ?? [];
    foreach ($items as $item) {
        if (($item['id'] ?? '') === $id) {
            return $item;
        }
    }
    return null;
}
