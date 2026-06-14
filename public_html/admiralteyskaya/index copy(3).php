<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='АА. Общая страница' order='2'>
    <cms:editable name='page_title' label='Заголовок страницы' type='text' />
</cms:template>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lounge Garden</title>
    <link rel="icon" type="image/png" href="favicon.png">
    
    <cms:embed 'styles.html' /> 
    <cms:embed 'seo_tags.html' />
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=Montserrat:wght@200;300;400;500;600&display=swap">

    <style>
       /* Стили кнопки Вверх */
        #scrollTopBtn {
            position: fixed !important;
            bottom: 30px !important;
            right: 30px !important;
            /* Сохраняем квадратный размер, чтобы получился идеальный круг */
            width: 45px !important;
            height: 45px !important;
            background-color: rgba(0, 0, 0, 0.8) !important;
            border: 1px solid rgba(197, 160, 89, 0.6) !important;
            color: #C5A059 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            z-index: 999999 !important;
            transition: all 0.4s ease-in-out !important;
            opacity: 0;
            visibility: hidden;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            
            /* --- ДОБАВЬ ЭТУ СТРОКУ --- */
            border-radius: 50% !important;
            /* ------------------------ */
        }

        #scrollTopBtn.show {
            opacity: 1 !important;
            visibility: visible !important;
        }

        #scrollTopBtn:hover {
            background-color: rgba(197, 160, 89, 0.2) !important;
            transform: translateY(-5px) !important;
        }

        @media (max-width: 767px) {
            #scrollTopBtn {
                bottom: 20px !important;
                right: 20px !important;
                width: 40px !important;
                height: 40px !important;
            }
        }
    </style>
</head>
<body>

    <cms:pages masterpage='header.php' limit='1'><cms:embed 'header.html' /></cms:pages>
    <cms:pages masterpage='about.php' limit='1'><cms:embed 'about.html' /></cms:pages>
    <cms:pages masterpage='gallery.php' limit='1'><cms:embed 'gallery.html' /></cms:pages>
    <cms:pages masterpage='menu.php' limit='1'><cms:embed 'menu.html' /></cms:pages>
    <cms:pages masterpage='akzii.php' limit='1'><cms:embed 'akzii.html' /></cms:pages>
    <cms:pages masterpage='reservation.php' limit='1'><cms:embed 'reservation.html' /></cms:pages>
    <cms:pages masterpage='contacts.php' limit='1'><cms:embed 'contacts.html' /></cms:pages>
    <cms:pages masterpage='filial.php' limit='1'><cms:embed 'filial.html' /></cms:pages>

    <cms:embed 'footer.html' />

    <div id="scrollTopBtn">
        <i class="fas fa-chevron-up"></i>
    </div>

    <script>
        (function() {
            const btn = document.getElementById('scrollTopBtn');
            
            function toggleBtn() {
                if (window.pageYOffset > 400 || document.documentElement.scrollTop > 400) {
                    btn.classList.add('show');
                } else {
                    btn.classList.remove('show');
                }
            }

            // Проверка при скролле
            window.addEventListener('scroll', toggleBtn);
            
            // Проверка при загрузке (если страница уже прокручена)
            window.addEventListener('load', toggleBtn);

            btn.onclick = function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            };
        })();
    </script>

</body>
</html>
<?php COUCH::invoke(); ?>
