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
- Загрузки CouchCMS исключены. Для отдельного добавления версионных изображений после публикации кода используйте `-SyncMenuVisual`: существующие файлы не перезаписываются, удаления отключены.
- После деплоя при необходимости: `clear-cache-web.php?token=<token>`

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

## Защита /ikko/ и проверка перед публикацией

Штатный маршрут: `scripts/deploy-vps.ps1` → VPS `159.194.232.89` →
`sudo -u deploy /srv/deploy/bin/deploy-site garden-lounge.pro` →
rsync из `/srv/deploy/projects/garden-lounge.pro/repo/public_html/` в
`mrpuffch@mrpuffch.beget.tech:/home/m/mrpuffch/garden-lounge.pro/public_html/`.
Сервер берёт `main` из GardenNew; сейчас `DELETE_REMOTE=false`, сборка не настроена.

[Точный diff для VPS](deploy-site-ikko.patch). Исключение `/ikko` привязано к
корню передачи и защищает весь каталог от копирования и штатного `--delete`.
Параметры основного rsync общие; preview добавляет только `-ni` и читает
существующий checkout. До применения патча не передавайте `--dry-run` старому
серверному скрипту: он сначала обновляет Git и делает `reset --hard`.

Проверка кандидата до установки (PowerShell, из корня этого worktree; скрипт
передаётся в stdin, не устанавливается на VPS):

```powershell
python -c "import pathlib,subprocess; k=str(pathlib.Path.home()/'.ssh/codex_beget_vps_ed25519'); subprocess.run(['ssh','-i',k,'-o','BatchMode=yes','root@159.194.232.89','sudo -u deploy bash -s -- garden-lounge.pro --dry-run'],input=pathlib.Path('docs/deploy-site-ikko.review.sh').read_bytes().replace(bytes([13,10]),bytes([10])),check=True)"
```

После согласованной установки серверного патча: `./scripts/deploy-vps.ps1 -DryRun`.
Preview не пушит и не обновляет VPS checkout. Сверьте его HEAD с GitHub main,
если нужен прогноз ближайшего обычного запуска. Для просмотра добавлений фото
можно отдельно указать `-DryRun -SyncMenuVisual`.

Серверный rsync резервного копирования перед публикацией читает Beget
`public_html` и пишет в отдельный каталог `deploy_backups/garden-lounge.pro`;
при пустом `BACKUP_EXCLUDES` используется `cp -a`. Этот шаг по-прежнему может
включать `ikko` в резервную копию и не меняет исходный каталог. Preview его пропускает.

`scripts/deploy.ps1` — старый прямой маршрут для REG.RU (архив и публикация
по SSH, настройки из `scripts/deploy.env.ps1`); штатный маршрут VPS → Beget его
не вызывает. Для Garden Lounge используйте `scripts/deploy-vps.ps1`.
