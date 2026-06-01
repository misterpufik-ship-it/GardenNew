<?php require_once( 'couch/cms.php' ); ?>
<cms:template title='Управление: Философия' name='philosophy_section' executable='0' order='20'>
    
    <cms:editable name='phil_title' label='Главный заголовок' type='text'>Philosophy</cms:editable>
    <cms:editable name='phil_concept' label='Текст концепции' type='text'>Концепция</cms:editable>
    
    <cms:editable name='phil_content' label='Основной текст' type='richtext'>
        Магический вечнозеленый сад, скрытый от городской суеты в самом сердце Петербурга. 
        <br><br>
        Здесь время замедляет свой ход. Роскошный интерьер, утопающий в живых тропиках, мелодичный шум фонтана и уютное тепло камина создают атмосферу абсолютной гармонии и уединения.
    </cms:editable>
    
    <cms:editable name='phil_slogan' label='Слоган (внизу)' type='textarea'>Garden Lounge — место, где рождаются ритуалы, достойные ваших воспоминаний</cms:editable>
    
    <cms:editable name='phil_sep' label='Картинка разделителя (узор)' type='image'>https://misterpufik.ru/div.png</cms:editable>

</cms:template>
<?php COUCH::invoke(); ?>