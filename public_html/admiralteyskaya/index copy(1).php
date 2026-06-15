<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Общая страница' order='220'>
    <!-- Здесь ваши редактируемые поля для этой страницы -->
    <cms:editable name='page_title' label='Заголовок страницы' type='text' />
</cms:template>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lounge Garden</title>
    <link rel="icon" type="image/png" href="</favicon.png/>favicon.png">
    <cms:embed 'styles.html' /> 
    <cms:embed 'seo_tags.html' />
 <!-- Libraries (ONE TIME globally) -->
<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=Montserrat:wght@200;300;400;500;600&display=swap">

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
   
    
    <?php COUCH::invoke(); ?>
</body>
</html>
