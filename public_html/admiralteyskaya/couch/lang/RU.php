<?php

    if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

    ///////////EDIT BELOW THIS////////////////////////////////////////

    // Header
    $t['greeting'] = 'Здравствуйте';
    $t['view_site'] = 'На сайт';
    $t['logout'] = 'Выйти';
    $t['javascript_msg'] = 'JavaScript is disabled or not supported by your browser.
                            Please upgrade your browser or <a href="https://support.google.com/answer/23852" target="_blank">enable JavaScript</a> to use the Admin Panel.';
    $t['add_new'] = 'Добавить';
    $t['add_new_page'] = 'Добавить страницу';
    $t['add_new_user'] = 'Добавить пользователя';
    $t['view'] = 'Просмотр';
    $t['list'] = 'Список';
    $t['edit'] = 'Редактирование';
    $t['delete'] = 'Удалить';
    $t['delete_selected'] = 'Удалить выбранное';
    $t['advanced_settings'] = 'Расширенные настройки';

    // Sidebar
    $t['comment'] = 'Комментарий';
    $t['comments'] = 'Комментарии';
    $t['manage_comments'] = 'Управление комментариями';
    $t['users'] = 'Пользователи';
    $t['manage_users'] = 'Управление пользователями';

    // List pages
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
    $t['confirm_delete_page'] = 'Удалить страницу'; // No question mark please
    $t['confirm_delete_selected_pages'] = 'Удалить выбранные страницы?';
    $t['remove_template'] = 'Удалить шаблон';
    $t['template_missing'] = 'Шаблон не найден';
    $t['prev'] = 'Назад'; // Pagination button
    $t['next'] = 'Вперёд'; // Pagination button

    // Pages
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
    $t['title_desc'] = 'leave this field empty to use the system generated name from the title';
    $t['required'] = 'обязательно'; // Required field
    $t['required_msg'] = 'Обязательное поле не может быть пустым';
    $t['browse_server'] = 'Обзор сервера';
    $t['view_image'] = 'Просмотр изображения';
    $t['thumb_created_auto'] = 'Создаётся автоматически';
    $t['recreate'] = 'Пересоздать';
    $t['thumb_recreated'] = 'Миниатюра пересоздана';
    $t['crop_from'] = 'cropping from';
    $t['top_left'] = 'Top Left';
    $t['top_center'] = 'Top Center';
    $t['top_right'] = 'Top Right';
    $t['middle_left'] = 'Middle Left';
    $t['middle'] = 'Middle';
    $t['middle_right'] = 'Middle Right';
    $t['bottom_left'] = 'Bottom Left';
    $t['bottom_center'] = 'Bottom Center';
    $t['bottom_right'] = 'Bottom Right';
    $t['view_thumbnail'] = 'Просмотр миниатюры';
    $t['field_not_found'] = 'Поле не найдено!';
    $t['delete_permanently'] = 'Удалить навсегда?';
    $t['view_code'] = 'Просмотр кода';
    $t['confirm_delete_field'] = 'Удалить это поле навсегда?';
    $t['save'] = 'Сохранить';

    // Comments
    $t['all'] = 'Все';
    $t['unapprove'] = 'Снять одобрение';
    $t['unapproved'] = 'Не одобрено';
    $t['approve'] = 'Одобрить';
    $t['approved'] = 'Одобрено';
    $t['select-deselect'] = 'Выбрать / снять всё';
    $t['confirm_delete_comment'] = 'Are you sure you want to delete this comment?';
    $t['confirm_delete_selected_comments'] = 'Are you sure you want to delete the selected comments?';
    $t['bulk_action'] = 'Bulk action with selected';
    $t['apply'] = 'Применить';
    $t['submitted_on'] = 'Submitted on';
    $t['email'] = 'Email';
    $t['website'] = 'Сайт';
    $t['duplicate_content'] = 'Duplicate content';
    $t['insufficient_interval'] = 'Not sufficient interval between comments';

    // Users
    $t['user_name_restrictions'] = 'Only Lowercase characters, numerals, hyphen and underscore permitted';
    $t['display_name'] = 'Отображаемое имя';
    $t['role'] = 'Роль';
    $t['no_users_found'] = 'Пользователи не найдены';
    $t['confirm_delete_user'] = 'Are you sure you want to delete user'; // No question mark please
    $t['confirm_delete_selected_users'] = 'Are you sure you want to delete the selected users?';
    $t['disabled'] = 'Отключён';
    $t['new_password'] = 'Новый пароль';
    $t['new_password_msg'] = 'If you would like to change the password type a new one. Otherwise leave this blank.';
    $t['repeat_password'] = 'Повторите пароль';
    $t['repeat_password_msg'] = 'Type your new password again.';
    $t['user_name_exists'] = 'Username already exists';
    $t['email_exists'] = 'Email address already exists';

    // Login
    $t['user_name'] = 'Логин';
    $t['password'] = 'Пароль';
    $t['login'] = 'Войти';
    $t['forgot_password'] = 'Забыли пароль?';
    $t['prompt_cookies'] = 'Cookies must be enabled to use this CMS';
    $t['prompt_username'] = 'Please enter your username';
    $t['prompt_password'] = 'Please enter your password';
    $t['invalid_credentials'] = 'Invalid username or password';
    $t['account_disabled'] = 'Account disabled';
    $t['access_denied'] = 'Access Denied';
    $t['insufficient_privileges'] = 'You do not have sufficient privileges to view the page requested.
                                    To see this page you must log out and log in with sufficient privileges.';

    // Password recovery
    $t['recovery_prompt'] = 'Please submit your username or email address.<br/>
                            You will receive your password by email.';
    $t['name_or_email'] = 'Your Username or Email Address';
    $t['submit'] = 'Отправить';
    $t['submit_error'] = 'Please enter your username or email address';
    $t['no_such_user'] = 'No such user exists';
    $t['reset_req_email_subject'] = 'Password reset requested';
    $t['reset_req_email_msg_0'] = 'A request was received to reset your password for the following site and username';
    $t['reset_req_email_msg_1'] = 'To confirm that the request was made by you, please visit the following address, otherwise just ignore this email.';
    $t['email_failed'] = 'Email could not be sent';
    $t['reset_req_email_confirm'] = 'A confirmation email has been sent to you.<br/>
                                    Please check your email inbox.';
    $t['invalid_key'] = 'Invalid key';
    $t['reset_email_subject'] = 'Your new password';
    $t['reset_email_msg_0'] = 'Your password has been reset for the following site and username';
    $t['reset_email_msg_1'] = 'You can change your password once logged in.';
    $t['reset_email_confirm'] = 'Your password has been reset.<br/>
                                Please check your email for the new password.';

    // Maintenance Mode
    $t['back_soon'] = '<h2>Maintenance Mode</h2>
                        <p>
                            Sorry for the inconvenience.<br/>
                            Our website is currently undergoing scheduled maintenance.<br/>
                            <b>Please try back after some time.</b>
                        </p>';


    // Addendum to Version 1.1 /////////////////////////////////////
    // Admin Panel
    $t['admin_panel'] = 'Панель управления';
    $t['login_title'] = 'Garden Lounge';

    // Folders
    $t['no_folders'] = 'No folders defined';
    $t['select_folder'] = 'Select Folder';
    $t['folders'] = 'Папки';
    $t['manage_folders'] = 'Управление папками';
    $t['add_new_folder'] = 'Add a new folder';
    $t['parent_folder'] = 'Parent Folder';
    $t['weight'] = 'Weight';
    $t['weight_desc'] = 'Higher the value, lower the folder will appear in list. Can be set to negative.';
    $t['desc'] = 'Описание';
    $t['image'] = 'Изображение';
    $t['cannot_be_own_parent'] = 'Cannot be its own parent';
    $t['name_already_exists'] = 'Name already exists';
    $t['pages'] = 'Страницы';
    $t['none'] = 'Нет';
    $t['confirm_delete_folder'] = 'Are you sure you want to delete folder'; // No question mark please
    $t['confirm_delete_selected_folders'] = 'Are you sure you want to delete the selected folders?';

    // Drafts
    $t['draft_caps'] = 'ЧЕРНОВИК'; // Upper case
    $t['draft'] = 'Черновик';
    $t['drafts'] = 'Черновики';
    $t['create_draft'] = 'Создать черновик';
    $t['create_draft_msg'] = 'Create a copy of this page (after saving changes)';
    $t['manage_drafts'] = 'Управление черновиками'; // Plural
    $t['update_original'] = 'Обновить оригинал';
    $t['update_original_msg'] = 'Copy the contents of this draft to the original page (and delete draft)';
    $t['recreate_original'] = 'Recreate Original';
    $t['no_drafts_found'] = 'No drafts found';
    $t['original_page'] = 'Original Page';
    $t['template'] = 'Шаблон';
    $t['modified'] = 'Изменено'; // Date of last modification
    $t['preview'] = 'Предпросмотр';
    $t['confirm_delete_draft'] = 'Are you sure you want to delete this draft'; // No question mark please
    $t['confirm_delete_selected_drafts'] = 'Are you sure you want to delete the selected drafts?';
    $t['confirm_apply_selected_drafts'] = 'Are you sure you want to apply the selected drafts?';
    $t['view_all_drafts'] = 'View all drafts';
    $t['original_deleted'] = 'ОРИГИНАЛ УДАЛЁН'; // Upper case

    // Addendum to Version 1.2 /////////////////////////////////////
    // Nested Pages
    $t['parent_page'] = 'Parent Page';
    $t['page_weight_desc'] = 'Higher the value, lower the page will appear in list. Can be set to negative.';
    $t['active'] = 'Активна';
    $t['inactive'] = 'Неактивна';
    $t['menu'] = 'Меню';
    $t['menu_text'] = 'Menu Text';
    $t['show_in_menu'] = 'Show in menu';
    $t['not_shown_in_menu'] = 'Not shown in menu';
    $t['leave_empty'] = 'Leave empty to use page title';
    $t['menu_link'] = 'Menu Link';
    $t['link_url'] = 'This page points to the following location';
    $t['link_url_desc'] = 'Can be left empty';
    $t['separate_window'] = 'Open in separate window';
    $t['pointer_page'] = 'Pointer Page';
    $t['points_to_another_page'] = 'Points to another page';
    $t['points_to'] = 'Points to';
    $t['redirects'] = 'Redirects';
    $t['masquerades'] = 'Masquerades';
    $t['strict_matching'] = 'Mark as selected in menu for all pages below this link';
    $t['up'] = 'Move Up';
    $t['down'] = 'Move Down';
    $t['remove_template_completely'] = 'Delete all pages and drafts of this template to remove it completely';
    $t['remove_uncloned_template_completely'] = 'Delete all drafts of this template to remove it completely';

    // Addendum to Version 1.2.5 /////////////////////////////////////
    // Gallery
    $t['bulk_upload'] = 'Загрузить';
    $t['folder_empty'] = 'This folder is empty. Please use the upload button above to add images.';
    $t['root'] = 'Корень';
    $t['item'] = 'image'; // Single
    $t['items'] = 'images'; // Multiple
    $t['container'] = 'folder'; // Single
    $t['containers'] = 'folders'; // Multiple

    //
    $t['columns_missing'] = 'Some columns missing!';
    $t['confirm_delete_columns'] = 'Are you sure you want to permanently delete the missing columns?';
    $t['add_row'] = 'Add a Row';

    // 2.0
    $t['left'] = 'Move Left';
    $t['right'] = 'Move Right';
    $t['crop'] = 'Crop';
    $t['menu_templates'] = 'Шаблоны';
    $t['menu_modules'] = 'Администрирование';
    $t['cancel'] = 'Отмена';
    $t['selected'] = 'Выбрано';
    $t['add'] = 'Добавить';
    $t['remove'] = 'Убрать';
    // 2.1
    $t['tiles_missing'] = 'Some tiles missing!';
    $t['confirm_delete_tiles'] = 'Are you sure you want to permanently delete the missing tiles?';
    $t['add_above'] = 'Add Above';
    $t['confirm_delete_row'] = 'Delete this row?';
    $t['no_data_message'] = '- No Data -';
    $t['ok'] = 'ОК';
    $t['globals'] = 'Глобальные';
    $t['manage_globals'] = 'Управление глобальными';
    $t['bulk_action_with_selected'] = 'Bulk action with selected';
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
