<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Адмиралтейская' order='2'>
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
        .scroll-top-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 45px;
            height: 45px;
            background-color: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(197, 160, 89, 0.5);
            color: #C5A059;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s ease;
            backdrop-filter: blur(5px);
            border-radius: 2px; /* Легкое скругление в стиле Old Money */
        }

        .scroll-top-btn.show {
            opacity: 1;
            visibility: visible;
        }

        .scroll-top-btn:hover {
            background-color: rgba(197, 160, 89, 0.2);
            border-color: #C5A059;
            transform: translateY(-5px);
        }

        @media (max-width: 767px) {
            .scroll-top-btn {
                bottom: 20px;
                right: 20px;
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>
<body>
    <cms:pages masterpage='header.php' limit='1'>
        <cms:embed 'header.html' />
    </cms:pages>
    
    <cms:pages masterpage='about.php' limit='1'>
        <cms:embed 'about.html' />
    </cms:pages>
    
    <cms:pages masterpage='gallery.php' limit='1'>
        <cms:embed 'gallery.html' />
    </cms:pages>
    
    <cms:pages masterpage='menu.php' limit='1'>
        <cms:embed 'menu.html' />
    </cms:pages>
    
    <cms:pages masterpage='akzii.php' limit='1'>
        <cms:embed 'akzii.html' />
    </cms:pages>
    
    <cms:pages masterpage='reservation.php' limit='1'>
        <cms:embed 'reservation.html' />
    </cms:pages>
    
    <cms:pages masterpage='contacts.php' limit='1'>
        <cms:embed 'contacts.html' />
    </cms:pages>
    
    <cms:pages masterpage='filial.php' limit='1'>
        <cms:embed 'filial.html' />
    </cms:pages>

    <cms:embed 'footer.html' />

    <div id="scrollTopBtn" class="scroll-top-btn">
        <i class="fas fa-chevron-up"></i>
    </div> 

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scrollTopBtn = document.getElementById('scrollTopBtn');

            window.addEventListener('scroll', function() {
                if (window.scrollY > 400) {
                    scrollTopBtn.classList.add('show');
                } else {
                    scrollTopBtn.classList.remove('show');
                }
            });

            scrollTopBtn.onclick = function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            };
        });
    </script>

    <?php COUCH::invoke(); ?>
</body>
</html>