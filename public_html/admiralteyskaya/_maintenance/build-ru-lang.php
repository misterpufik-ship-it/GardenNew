<?php
$ru = dirname( __DIR__ ) . '/couch/lang/RU.php';

$content = <<<'PHP'
<?php

    if ( !defined('K_COUCH_DIR') ) die();

    $t['greeting'] = 'Здравствуйте';
    $t['view_site'] = 'На сайт';
    $t['logout'] = 'Выйти';
    $t['javascript_msg'] = 'JavaScript отключён или не поддерживается вашим браузером.
                            Обновите браузер или <a href="https://support.google.com/answer/23852" target="_blank">включите JavaScript</a>, чтобы использовать панель управления.';
    $t['add_new'] = 'Добавить';
    $t['add_new_page'] = 'Добавить страницу';
    $t['add_new_user'] = 'Добавить пользователя';
    $t['view'] = 'Просмотр';
    $t['list'] = 'Список';
    $t['edit'] = 'Редактирование';
    $t['delete'] = 'Удалить';
    $t['delete_selected'] = 'Удалить выбранное';
    $t['advanced_settings'] = 'Расширенные настройки';

    $t['comment'] = 'Комментарий';
    $t['comments'] = 'Комментарии';
    $t['manage_comments'] = 'Управление комментариями';
    $t['users'] = 'Пользователи';
    $t['manage_users'] = 'Управление пользователями';

    $t['view_all_folders'] = 'Все папки';
    $t['filter'] = 'Фильтр';
    $t['showing'] = 'Показано';
    $t['title'] = 'Заголовок';
    $t['folder'] = 'Папка';
    $t['date'] = 'Дата';
    $t['actions'] = 'Действия';
    $t['no_pages_found'] = 'Страницы не найдены';
    $t['published'] = 'Опубликовано';
    $t['unpublished'] = 'Не опубликовано';
    $t['confirm_delete_page'] = 'Удалить страницу';
    $t['confirm_delete_selected_pages'] = 'Удалить выбранные страницы?';
    $t['remove_template'] = 'Удалить шаблон';
    $t['template_missing'] = 'Шаблон не найден';
    $t['prev'] = 'Назад';
    $t['next'] = 'Вперёд';

    $t['welcome'] = 'Добро пожаловать';
    $t['no_regions_defined'] = 'Редактируемые области не заданы';
    $t['no_templates_defined'] = 'Нет шаблонов, управляемых CMS';
    $t['access_level'] = 'Уровень доступа';
    $t['superadmin'] = 'Суперадмин';
    $t['admin'] = 'Администратор';
    $t['authenticated_user_special'] = 'Авторизованный пользователь (особый)';
    $t['authenitcated_user'] = 'Авторизованный пользователь';
    $t['unauthenticated_user'] = 'Все';
    $t['allow_comments'] = 'Разрешить комментарии';
    $t['status'] = 'Статус';
    $t['name'] = 'Имя';
    $t['title_desc'] = 'оставьте пустым — имя будет сгенерировано из заголовка';
    $t['required'] = 'обязательно';
    $t['required_msg'] = 'Обязательное поле не может быть пустым';
    $t['browse_server'] = 'Обзор сервера';
    $t['view_image'] = 'Просмотр изображения';
    $t['thumb_created_auto'] = 'Создаётся автоматически';
    $t['recreate'] = 'Пересоздать';
    $t['thumb_recreated'] = 'Миниатюра пересоздана';
    $t['crop_from'] = 'обрезка от';
    $t['top_left'] = 'Верхний левый';
    $t['top_center'] = 'Верхний центр';
    $t['top_right'] = 'Верхний правый';
    $t['middle_left'] = 'Средний левый';
    $t['middle'] = 'Центр';
    $t['middle_right'] = 'Средний правый';
    $t['bottom_left'] = 'Нижний левый';
    $t['bottom_center'] = 'Нижний центр';
    $t['bottom_right'] = 'Нижний правый';
    $t['view_thumbnail'] = 'Просмотр миниатюры';
    $t['field_not_found'] = 'Поле не найдено!';
    $t['delete_permanently'] = 'Удалить навсегда?';
    $t['view_code'] = 'Просмотр кода';
    $t['confirm_delete_field'] = 'Удалить это поле навсегда?';
    $t['save'] = 'Сохранить';

    $t['all'] = 'Все';
    $t['unapprove'] = 'Снять одобрение';
    $t['unapproved'] = 'Не одобрено';
    $t['approve'] = 'Одобрить';
    $t['approved'] = 'Одобрено';
    $t['select-deselect'] = 'Выбрать / снять всё';
    $t['confirm_delete_comment'] = 'Удалить этот комментарий?';
    $t['confirm_delete_selected_comments'] = 'Удалить выбранные комментарии?';
    $t['bulk_action'] = 'Действие с выбранным';
    $t['apply'] = 'Применить';
    $t['submitted_on'] = 'Отправлено';
    $t['email'] = 'Email';
    $t['website'] = 'Сайт';
    $t['duplicate_content'] = 'Дублирующийся контент';
    $t['insufficient_interval'] = 'Недостаточный интервал между комментариями';

    $t['user_name_restrictions'] = 'Допустимы только строчные латинские буквы, цифры, дефис и подчёркивание';
    $t['display_name'] = 'Отображаемое имя';
    $t['role'] = 'Роль';
    $t['no_users_found'] = 'Пользователи не найдены';
    $t['confirm_delete_user'] = 'Удалить пользователя';
    $t['confirm_delete_selected_users'] = 'Удалить выбранных пользователей?';
    $t['disabled'] = 'Отключён';
    $t['new_password'] = 'Новый пароль';
    $t['new_password_msg'] = 'Введите новый пароль или оставьте поле пустым.';
    $t['repeat_password'] = 'Повторите пароль';
    $t['repeat_password_msg'] = 'Введите новый пароль ещё раз.';
    $t['user_name_exists'] = 'Имя пользователя уже занято';
    $t['email_exists'] = 'Email уже используется';

    $t['user_name'] = 'Логин';
    $t['password'] = 'Пароль';
    $t['user_remember'] = 'Запомнить меня';
    $t['login'] = 'Войти';
    $t['forgot_password'] = 'Забыли пароль?';
    $t['prompt_cookies'] = 'Для работы CMS необходимы cookies';
    $t['prompt_username'] = 'Введите логин';
    $t['prompt_password'] = 'Введите пароль';
    $t['invalid_credentials'] = 'Неверный логин или пароль';
    $t['account_disabled'] = 'Аккаунт отключён';
    $t['access_denied'] = 'Доступ запрещён';
    $t['insufficient_privileges'] = 'Недостаточно прав для просмотра этой страницы.
                                    Выйдите и войдите с нужным уровнем доступа.';

    $t['recovery_prompt'] = 'Введите логин или email.<br/>
                            Новый пароль будет отправлен на почту.';
    $t['name_or_email'] = 'Логин или email';
    $t['submit'] = 'Отправить';
    $t['submit_error'] = 'Введите логин или email';
    $t['no_such_user'] = 'Пользователь не найден';
    $t['reset_req_email_subject'] = 'Запрос сброса пароля';
    $t['reset_req_email_msg_0'] = 'Получен запрос на сброс пароля для сайта и пользователя';
    $t['reset_req_email_msg_1'] = 'Если это были вы, перейдите по ссылке ниже, иначе проигнорируйте письмо.';
    $t['email_failed'] = 'Не удалось отправить email';
    $t['reset_req_email_confirm'] = 'Письмо с подтверждением отправлено.<br/>
                                    Проверьте почту.';
    $t['invalid_key'] = 'Неверный ключ';
    $t['reset_email_subject'] = 'Ваш новый пароль';
    $t['reset_email_msg_0'] = 'Пароль сброшен для сайта и пользователя';
    $t['reset_email_msg_1'] = 'После входа вы можете сменить пароль.';
    $t['reset_email_confirm'] = 'Пароль сброшен.<br/>
                                Проверьте почту для получения нового пароля.';

    $t['back_soon'] = '<h2>Технические работы</h2>
                        <p>
                            Приносим извинения за неудобства.<br/>
                            Сайт временно недоступен из-за обслуживания.<br/>
                            <b>Попробуйте зайти позже.</b>
                        </p>';

    $t['admin_panel'] = 'Панель управления';
    $t['login_title'] = 'Garden Lounge';

    $t['no_folders'] = 'Папки не заданы';
    $t['select_folder'] = 'Выберите папку';
    $t['folders'] = 'Папки';
    $t['manage_folders'] = 'Управление папками';
    $t['add_new_folder'] = 'Добавить папку';
    $t['parent_folder'] = 'Родительская папка';
    $t['weight'] = 'Вес';
    $t['weight_desc'] = 'Чем больше значение, тем ниже папка в списке. Можно задать отрицательное.';
    $t['desc'] = 'Описание';
    $t['image'] = 'Изображение';
    $t['cannot_be_own_parent'] = 'Не может быть родителем самой себя';
    $t['name_already_exists'] = 'Имя уже существует';
    $t['pages'] = 'Страницы';
    $t['none'] = 'Нет';
    $t['confirm_delete_folder'] = 'Удалить папку';
    $t['confirm_delete_selected_folders'] = 'Удалить выбранные папки?';

    $t['draft_caps'] = 'ЧЕРНОВИК';
    $t['draft'] = 'Черновик';
    $t['drafts'] = 'Черновики';
    $t['create_draft'] = 'Создать черновик';
    $t['create_draft_msg'] = 'Создать копию страницы (после сохранения изменений)';
    $t['manage_drafts'] = 'Управление черновиками';
    $t['update_original'] = 'Обновить оригинал';
    $t['update_original_msg'] = 'Скопировать черновик в оригинал (и удалить черновик)';
    $t['recreate_original'] = 'Воссоздать оригинал';
    $t['no_drafts_found'] = 'Черновики не найдены';
    $t['original_page'] = 'Оригинальная страница';
    $t['template'] = 'Шаблон';
    $t['modified'] = 'Изменено';
    $t['preview'] = 'Предпросмотр';
    $t['confirm_delete_draft'] = 'Удалить этот черновик';
    $t['confirm_delete_selected_drafts'] = 'Удалить выбранные черновики?';
    $t['confirm_apply_selected_drafts'] = 'Применить выбранные черновики?';
    $t['view_all_drafts'] = 'Все черновики';
    $t['original_deleted'] = 'ОРИГИНАЛ УДАЛЁН';

    $t['parent_page'] = 'Родительская страница';
    $t['page_weight_desc'] = 'Чем больше значение, тем ниже страница в списке. Можно задать отрицательное.';
    $t['active'] = 'Активна';
    $t['inactive'] = 'Неактивна';
    $t['menu'] = 'Меню';
    $t['menu_text'] = 'Текст в меню';
    $t['show_in_menu'] = 'Показывать в меню';
    $t['not_shown_in_menu'] = 'Не показывать в меню';
    $t['leave_empty'] = 'Оставьте пустым — будет использован заголовок';
    $t['menu_link'] = 'Ссылка меню';
    $t['link_url'] = 'Страница ведёт по адресу';
    $t['link_url_desc'] = 'Можно оставить пустым';
    $t['separate_window'] = 'Открывать в новом окне';
    $t['pointer_page'] = 'Страница-указатель';
    $t['points_to_another_page'] = 'Указывает на другую страницу';
    $t['points_to'] = 'Указывает на';
    $t['redirects'] = 'Перенаправляет';
    $t['masquerades'] = 'Маскируется';
    $t['strict_matching'] = 'Отмечать как выбранное для всех страниц ниже ссылки';
    $t['up'] = 'Вверх';
    $t['down'] = 'Вниз';
    $t['remove_template_completely'] = 'Удалите все страницы и черновики шаблона, чтобы убрать его полностью';
    $t['remove_uncloned_template_completely'] = 'Удалите все черновики шаблона, чтобы убрать его полностью';

    $t['bulk_upload'] = 'Загрузить';
    $t['folder_empty'] = 'Папка пуста. Используйте кнопку загрузки выше.';
    $t['root'] = 'Корень';
    $t['item'] = 'изображение';
    $t['items'] = 'изображения';
    $t['container'] = 'папка';
    $t['containers'] = 'папки';

    $t['columns_missing'] = 'Некоторые колонки отсутствуют!';
    $t['confirm_delete_columns'] = 'Удалить отсутствующие колонки навсегда?';
    $t['add_row'] = 'Добавить строку';

    $t['left'] = 'Влево';
    $t['right'] = 'Вправо';
    $t['crop'] = 'Обрезать';
    $t['menu_templates'] = 'Шаблоны';
    $t['menu_modules'] = 'Администрирование';
    $t['cancel'] = 'Отмена';
    $t['selected'] = 'Выбрано';
    $t['add'] = 'Добавить';
    $t['remove'] = 'Убрать';

    $t['tiles_missing'] = 'Некоторые плитки отсутствуют!';
    $t['confirm_delete_tiles'] = 'Удалить отсутствующие плитки навсегда?';
    $t['add_above'] = 'Добавить выше';
    $t['confirm_delete_row'] = 'Удалить эту строку?';
    $t['no_data_message'] = '— Нет данных —';
    $t['ok'] = 'ОК';
    $t['globals'] = 'Глобальные';
    $t['manage_globals'] = 'Управление глобальными';
    $t['bulk_action_with_selected'] = 'Действие с выбранным';
    $t['month01'] = 'Январь';
    $t['month02'] = 'Февраль';
    $t['month03'] = 'Март';
    $t['month04'] = 'Апрель';
    $t['month05'] = 'Май';
    $t['month06'] = 'Июнь';
    $t['month07'] = 'Июль';
    $t['month08'] = 'Август';
    $t['month09'] = 'Сентябрь';
    $t['month10'] = 'Октябрь';
    $t['month11'] = 'Ноябрь';
    $t['month12'] = 'Декабрь';
    $t['manage'] = 'Управление';

PHP;

file_put_contents( $ru, $content );
echo "RU.php written: $ru (" . strlen( $content ) . " bytes)\n";
