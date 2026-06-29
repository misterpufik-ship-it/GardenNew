<?php require_once( '../../couch/cms.php' ); ?>
<cms:template title='Мастер Импорта Меню' icon='upload' hidden='1' executable='0' />

<div style="padding:30px; background:#1a1a1a; color:#fff; font-family:sans-serif; min-height: 100vh;">
    <h1 style="color:#C5A059;">⚙️ Финальный импорт (Адмиралтейская)</h1>

    <?php
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file']) ) {
            $file = $_FILES['csv_file']['tmp_name'];
            if ( is_uploaded_file($file) ) {
                $content = file_get_contents($file);
                
                // Исправляем кодировку на лету
                $enc = mb_detect_encoding($content, array('UTF-8', 'Windows-1251', 'CP1251'));
                if ($enc !== 'UTF-8') {
                    $content = mb_convert_encoding($content, 'UTF-8', $enc);
                }
                $content = preg_replace('/^\xEF\xBB\xBF/', '', $content); // Удаляем BOM
                
                $lines = explode("\n", str_replace("\r", "", $content));
                $data_rows = [];
                array_shift($lines); // Пропускаем заголовки

                foreach ($lines as $line) {
                    if (trim($line) == '') continue;
                    
                    // Умный разбор: запятая или точка с запятой
                    $sep = (strpos($line, ';') !== false) ? ';' : ',';
                    $data = str_getcsv($line, $sep);
                    
                    // Исправление склейки Excel
                    if (count($data) == 1 && strpos($data[0], ',') !== false) {
                        $data = str_getcsv($data[0], ',');
                    }

                    if (empty($data[0])) continue;

                    $data_rows[] = array(
                        'row_type'     => trim($data[0]),
                        'cat_title'    => (trim($data[0]) == 'header') ? trim($data[1]) : '',
                        'cat_title_en' => (trim($data[0]) == 'header') ? trim($data[2]) : '',
                        'cat_layout'   => 'list',
                        'i_name'       => (trim($data[0]) == 'item')   ? trim($data[1]) : '',
                        'i_name_en'    => (trim($data[0]) == 'item')   ? trim($data[2]) : '',
                        'i_desc'       => $data[3] ?? '',
                        'i_desc_en'    => $data[4] ?? '',
                        'i_price'      => $data[5] ?? ''
                    );
                }
                
                global $CTX;
                $CTX->set('import_payload', json_encode($data_rows, JSON_UNESCAPED_UNICODE), 'global');
                $CTX->set('execute_db_save', '1', 'global');
            }
        }
    ?>

    <cms:if execute_db_save >
        <cms:db_persist 
            _masterpage='menu/text/index.php'
            _mode='edit'
            _page_id='2c7d006f15925dc5a32a4404ff94ca48' 
            rep_hookahs_v2="<cms:show import_payload />"
            _invalidate_cache='1'
        >
            <cms:if k_error>
                <div style="color:red; border:1px solid red; padding:20px; background:rgba(255,0,0,0.1);">
                    Ошибка: <cms:show k_error />
                </div>
            <cms:else />
                <div style="color:#C5A059; border:1px solid #C5A059; padding:20px; background:rgba(197,160,89,0.1);">
                    ✅ <b>Импорт успешно выполнен!</b><br>
                    Зайдите в админку и нажмите <b>Ctrl + F5</b>.
                </div>
            </cms:if>
        </cms:db_persist>
    </cms:if>

    <form method="post" enctype="multipart/form-data" style="margin-top:20px; background:#222; padding:20px; border-radius:5px;">
        <input type="file" name="csv_file" required style="margin-bottom:10px; display:block;">
        <button type="submit" style="background:#C5A059; padding:10px 20px; border:none; cursor:pointer; font-weight:bold; color:#000;">
            🚀 ЗАГРУЗИТЬ CSV
        </button>
    </form>
</div>

<?php COUCH::invoke(); ?>