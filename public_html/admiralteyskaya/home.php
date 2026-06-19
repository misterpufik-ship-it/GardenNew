<cms:template title='Главная' name='home_section' executable='0' order='1'>

    <cms:editable name='home_main_title' label='Заголовок главной' type='text'>Войти в оазис</cms:editable>
    <cms:editable name='home_subtitle' label='Подзаголовок' type='text'>Выберите филиал</cms:editable>

    <cms:editable name='home_gallery_group' label='Галереи' type='group' />
    <cms:repeatable name='home_gallery_images' label='Фото для главной' group='home_gallery_group'>
        <cms:editable name='home_img' label='Фото' type='image' />
        <cms:editable name='home_img_caption' label='Подпись' type='text' />
    </cms:repeatable>

    <cms:editable name='home_socials_group' label='Соцсети' type='group' />
    <cms:editable name='home_instagram' label='Instagram' group='home_socials_group' type='text'>https://instagram.com/garden_lounge_spb/</cms:editable>
    <cms:editable name='home_vk' label='VK' group='home_socials_group' type='text'>https://vk.com/loungegarden</cms:editable>
    <cms:editable name='home_youtube' label='YouTube' group='home_socials_group' type='text'>https://youtube.com/@garden.lounge</cms:editable>

</cms:template>
