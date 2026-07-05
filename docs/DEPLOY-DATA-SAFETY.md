# Garden Lounge — деплой и сохранность данных CMS

## Где живут данные меню

| Данные | Хранение |
|--------|----------|
| Позиции визуального меню (название, цена, тег, вес, описание) | MySQL `couch_data_text` (сериализованные repeatable) |
| Фото визуального меню | `couch/uploads/image/menu-visual/` на BeGet |
| Текстовое меню | MySQL, шаблоны `menu/text/index.php` |
| Правки в админке | Только БД; **не** в Git |

Git и `deploy-vps.ps1` пушат **код**. Позиции меню из админки в репозиторий не попадают.

## Рутинный деплой (безопасен для меню)

```powershell
.\scripts\deploy-vps.ps1
```

- Не вызывает maintenance-скрипты.
- Синхронизирует `menu-visual` **изображения** с VPS (версионные файлы), не затирая уникальные загрузки из админки без необходимости.
- После деплоя при необходимости: `clear-cache-web.php?token=gl-cache-clear-20260623`

## Скрипты — НЕ запускать при обычном деплое

| Скрипт | Риск |
|--------|------|
| `import-visual-taplink-cli.php` | **Полная замена** списков visual menu |
| `register-layout-mobile-menu-web.php?migrate_legacy=1` | Перезапись отступов гамбургер-меню |
| `fix-mm-logo-gap-web.php?force=1` | Перезапись logo gap |
| `register-*-web.php` без нужды | Может менять метаданные полей |

## Скрипты — безопасные (merge / описания)

| Скрипт | Поведение |
|--------|-----------|
| `restore-visual-menu-missing-web.php?confirm=restore` | Только **добавляет** отсутствующие строки из `taplink-import-data.json` |
| `sync-visual-menu-descriptions-web.php` | Копирует **описания** из текстового меню; `fill_empty=1` — только в пустые поля |
| `report-visual-menu-web.php` | Только чтение |

## Восстановление визуального меню

1. `report-visual-menu-web.php` — сверка количества и пустых описаний.
2. `restore-visual-menu-missing-web.php?confirm=restore&branch=both&sections=kitchen,bar`
3. `sync-visual-menu-descriptions-web.php?token=...`
4. `clear-cache-web.php`

Эталон количества кухни/бара для Адмиралтейской: `menu/visual/taplink-import-data.json` (30 kitchen, 22 bar).

## Правило

**Данные из админки — источник истины.** Код и скрипты не должны удалять или перезаписывать позиции и фото, пока пользователь сам не удалил их в CouchCMS.
