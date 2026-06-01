<?php require_once( '../../couch/cms.php' ); ?>
<cms:template title='Importer' hidden='1' order='99' />

<!-- 
    ИНСТРУКЦИЯ:
    1. Загрузите этот файл на сервер рядом с index.php
    2. Откройте в браузере: ваш-сайт/import.php
    3. Дождитесь надписи "Done!"
    4. Удалите этот файл с сервера.
-->

<?php
// Полный массив данных для импорта в CMS
$menu_data = [
    // --- АКЦИИ ---
    ['folder'=>'promo', 'type'=>'header', 'title'=>'Специальные Предложения', 'price'=>'', 'desc'=>''],
    ['folder'=>'promo', 'type'=>'promo', 'title'=>'Smoky Lunch', 'price'=>'30% СКИДКА', 'cond'=>'БУДНИ ДО 17:00', 'desc'=>'Магический момент спокойствия в самом сердце мегаполиса. Время замедлить ход событий.'],
    ['folder'=>'promo', 'type'=>'promo', 'title'=>'Celebration', 'price'=>'10% СКИДКА', 'cond'=>'ДЕНЬ РОЖДЕНИЯ', 'desc'=>'Ваш особенный день в атмосфере абсолютной гармонии. Празднуйте красиво в нашем саду.'],
    ['folder'=>'promo', 'type'=>'promo', 'title'=>'Ladies Day', 'price'=>'50% НА КАЛЬЯН', 'cond'=>'ПО СРЕДАМ', 'desc'=>'Вечер грации и мелодичного шума фонтана. Эксклюзивное предложение для женских компаний.'],

    // --- КАЛЬЯНЫ ---
    ['folder'=>'hookah', 'type'=>'header', 'title'=>'Авторский', 'price'=>'', 'desc'=>''],
    ['folder'=>'hookah', 'type'=>'item', 'title'=>'Garden Edition', 'price'=>'3500', 'desc'=>'Каменный куб, заросший мхом, будто пролежал в саду под дождями и только сейчас решил проявиться. Тяга мягкая, но уверенная. Его не выбирают ради эксперимента — его признают своим.'],
    ['folder'=>'hookah', 'type'=>'header', 'title'=>'Классический', 'price'=>'', 'desc'=>''],
    ['folder'=>'hookah', 'type'=>'item', 'title'=>'Classic', 'price'=>'2000', 'desc'=>'Классическая чаша.'],
    ['folder'=>'hookah', 'type'=>'header', 'title'=>'Премиальные', 'price'=>'', 'desc'=>''],
    ['folder'=>'hookah', 'type'=>'item', 'title'=>'World Tobacco Original (WTO)', 'price'=>'+1200', 'desc'=>'Уникальный продукт, изготовленный из дорогих сигарных табачных листьев, прошедших выдержанную ферментацию более 6 лет.'],
    ['folder'=>'hookah', 'type'=>'item', 'title'=>'Deus Perfume', 'price'=>'+1200', 'desc'=>'Парфюмированный кальянный табак от компании «DEUS» с ароматами, вдохновленными легендарными композициями из мира парфюмерии.'],
    ['folder'=>'hookah', 'type'=>'header', 'title'=>'Фруктовые Чаши', 'price'=>'', 'desc'=>''],
    ['folder'=>'hookah', 'type'=>'item', 'title'=>'Грейпфрут', 'price'=>'+1000', 'desc'=>'Сессия курения на фруктовой чаше 70-90 мин.'],
    ['folder'=>'hookah', 'type'=>'item', 'title'=>'Гранат', 'price'=>'+1200', 'desc'=>'Сессия курения на фруктовой чаше 70-90 мин.'],
    ['folder'=>'hookah', 'type'=>'item', 'title'=>'Помело', 'price'=>'+1200', 'desc'=>'Сессия курения на фруктовой чаше 70-90 мин.'],

    // --- БАР Б|А (ЧАЙ/КОФЕ) ---
    ['folder'=>'na_bar', 'type'=>'header', 'title'=>'Классика. Черный Чай (0.9)', 'price'=>'', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Чай Ассам', 'price'=>'550', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Эрл Грей Классик', 'price'=>'550', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Ройбуш', 'price'=>'550', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'header', 'title'=>'Классика. Зеленый Чай (0.9)', 'price'=>'', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Сенча', 'price'=>'550', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Чай жасмин', 'price'=>'550', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Молочный улун', 'price'=>'700', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'header', 'title'=>'Авторские Чаи (0.9)', 'price'=>'', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Мятная малина', 'price'=>'760', 'desc'=>'В основе чай-Эрл Грей. Яркий вкус малины прекрасно дополняет душистый аромат мяты.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Травяной чай', 'price'=>'760', 'desc'=>'Полезный и вкусный напиток, на основе зеленого чая с добавлением мяты, чабреца и розмарина.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Лесные ягоды', 'price'=>'760', 'desc'=>'В основе чай-Эрл Грей. Сочетание лесных ягод, листьев смородины и специй.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Молочный чай с пряностями', 'price'=>'760', 'desc'=>'На основе чёрного чая, душистых пряностей и молока.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Облепиха & имбирь', 'price'=>'760', 'desc'=>'Облепиховый чай - напиток долголетия! Источник витаминов.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Молочный улун & белый шоколад', 'price'=>'900', 'desc'=>'Чай на основе молочного улуна с добавлением апельсина и свежей мяты. Сироп со вкусом белого шоколада.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Пуэр на вишневом соке', 'price'=>'900', 'desc'=>'Чай идеально дополняется нотами кисловато-сладких спелых вишен.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Топпинги', 'price'=>'70', 'desc'=>'Лимон, апельсин, молоко, мед, мята, сироп, сливки, чабрец, имбирь, листья смородины'],
    ['folder'=>'na_bar', 'type'=>'header', 'title'=>'Китайская Коллекция', 'price'=>'', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Те Гуань Инь «Красный Олень»', 'price'=>'830', 'desc'=>'Нежно-сливочный, ярко-сиреневый.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Зелёный Феникс', 'price'=>'830', 'desc'=>'Древесно-хвойный, печёная груша.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Габа с горы Радости', 'price'=>'980', 'desc'=>'Полевые травы, макадамия.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Шен Пуэр «Биндао Гу Шу»', 'price'=>'870', 'desc'=>'Травянистый, луговые цветы.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Небесный Гиббон', 'price'=>'870', 'desc'=>'Сливочно-травянистый, клевер.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Дянь Хун «Сосновые Иглы»', 'price'=>'830', 'desc'=>'Шиповник, дуб, варенье.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Черное Золото', 'price'=>'870', 'desc'=>'Дуб, смородина, жженый сахар.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Габа «Дзя Руби»', 'price'=>'1050', 'desc'=>'Липа, гречишный мёд.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Красная Пагода', 'price'=>'830', 'desc'=>'Маслянистая сосна, корица.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Дикий Фиолет', 'price'=>'940', 'desc'=>'Виноград, чернослив.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Шу Пуэр «Красная Лента»', 'price'=>'830', 'desc'=>'Орехово-древесный.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Тернистый Феникс', 'price'=>'790', 'desc'=>'Хмель, лесные ягоды.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Шу Пуэр «Древесное Вино»', 'price'=>'940', 'desc'=>'Древесный, дубовый.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Шу Пуэр «Старые Чайные Головы»', 'price'=>'830', 'desc'=>'Сливочный, пивная горчинка.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Да Хун Пао Тан Бэй', 'price'=>'890', 'desc'=>'Корица, черный хлеб, шоколад.'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Габа «Вересковый Мёд»', 'price'=>'1250', 'desc'=>'Цветочный мёд, орехи.'],
    ['folder'=>'na_bar', 'type'=>'header', 'title'=>'Coffee', 'price'=>'', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Эспрессо', 'price'=>'320', 'desc'=>'0.03'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Американо', 'price'=>'320', 'desc'=>'0.15'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Капучино', 'price'=>'400', 'desc'=>'0.3'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Латте', 'price'=>'400', 'desc'=>'0.25'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Матча-латте', 'price'=>'390', 'desc'=>'0.25'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Флэт Уайт', 'price'=>'390', 'desc'=>'0.3'],
    ['folder'=>'na_bar', 'type'=>'header', 'title'=>'Cold Coffee', 'price'=>'', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Айс-Латте', 'price'=>'390', 'desc'=>'0.4'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Ягодный Эспрессо-Тоник', 'price'=>'390', 'desc'=>'0.3'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Эспрессо-Тоник', 'price'=>'390', 'desc'=>'0.3'],
    ['folder'=>'na_bar', 'type'=>'header', 'title'=>'Lemonade / Juice', 'price'=>'', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Coca-Cola', 'price'=>'385', 'desc'=>'0.25'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Evervess апельсин / лемон-лайм', 'price'=>'385', 'desc'=>'0.25'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Тоник Evervess', 'price'=>'385', 'desc'=>'0.25'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Adrenaline Rush', 'price'=>'385', 'desc'=>'0.25'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Сок Swell', 'price'=>'390', 'desc'=>'яблоко, апельсин, вишня. 0.25'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Фреш апельсин', 'price'=>'390', 'desc'=>'0.2'],
    ['folder'=>'na_bar', 'type'=>'header', 'title'=>'Homemade Lemonade (0.4 / 1.0)', 'price'=>'', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Chinese Berry', 'price'=>'490/990', 'desc'=>'Личи, Черная смородина, Бузина'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Green Bro', 'price'=>'490/990', 'desc'=>'Киви, Зеленое яблоко'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Summer Breeze', 'price'=>'490/990', 'desc'=>'Клубника, бузина'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Tropical Extaz', 'price'=>'490/990', 'desc'=>'Тропические фрукты'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Raffaello Red Berry', 'price'=>'490/990', 'desc'=>'Малина, Кокос'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Cosmo Flower', 'price'=>'490/990', 'desc'=>'Черная смородина, Лаванда'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Tayga', 'price'=>'490/990', 'desc'=>'Уникальный вкус меняющийся с настроением сада'],
    ['folder'=>'na_bar', 'type'=>'header', 'title'=>'Water', 'price'=>'', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Legend of Baikal (газ/бг)', 'price'=>'390', 'desc'=>'0.33'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Legend of Baikal (газ/бг)', 'price'=>'700', 'desc'=>'0.75'],
    ['folder'=>'na_bar', 'type'=>'header', 'title'=>'Безалкогольные Коктейли', 'price'=>'', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Hot Wine n.a.', 'price'=>'650', 'desc'=>'Сок вишневый, пряности'],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Whiskey Sour n.a.', 'price'=>'650', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Negroni n.a.', 'price'=>'650', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Clover club n.a.', 'price'=>'650', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Tropical gin-tonic', 'price'=>'650', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Pina Colada n.a.', 'price'=>'540', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Mojito n.a.', 'price'=>'540', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Aperol n.a.', 'price'=>'540', 'desc'=>''],
    ['folder'=>'na_bar', 'type'=>'item', 'title'=>'Gin-tonic n.a.', 'price'=>'540', 'desc'=>''],

    // --- БАР ---
    ['folder'=>'bar', 'type'=>'header', 'title'=>'Авторские Коктейли', 'price'=>'', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Okinawa (Япония)', 'price'=>'950', 'desc'=>'Роза, сакура, юзу.'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Mestre (Италия)', 'price'=>'950', 'desc'=>'Горько-сладкий, персик.'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Birstall (Англия)', 'price'=>'950', 'desc'=>'Сладкий имбирный.'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Chios (Греция)', 'price'=>'950', 'desc'=>'Мастиковое дерево, цветы.'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Jerez de la Frontera', 'price'=>'950', 'desc'=>'Сладкий, миндаль, орех.'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Jalisco (Мексика)', 'price'=>'950', 'desc'=>'Кисло-сладкий, текила.'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Late Breakfast', 'price'=>'950', 'desc'=>'Мед, миндаль, гречка.'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Maisons-Alfort', 'price'=>'950', 'desc'=>'Травянисто-горький.'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Barbados', 'price'=>'950', 'desc'=>'Тропический, пряный.'],
    ['folder'=>'bar', 'type'=>'header', 'title'=>'Классика & Аперитив', 'price'=>'', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Long Island Ice Tea', 'price'=>'790', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Pina Colada', 'price'=>'690', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Mai Thai', 'price'=>'790', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Mojito', 'price'=>'750', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Gin-Tonic Berry', 'price'=>'750', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Aperol Spritz', 'price'=>'790', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Hot Wine', 'price'=>'690', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'White Russian', 'price'=>'650', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Clover Club', 'price'=>'750', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Margarita', 'price'=>'790', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Porn Star Martini', 'price'=>'750', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'New York Sour', 'price'=>'750', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Gin Sour', 'price'=>'750', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Negroni', 'price'=>'750', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Whiskey Sour', 'price'=>'750', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Daiquiri', 'price'=>'690', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Lemon Pie', 'price'=>'750', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Oakheart & Cola', 'price'=>'650', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Martini Fiero & Tonic', 'price'=>'650', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Bianco & tonic', 'price'=>'650', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Royal Raspberry', 'price'=>'650', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Bosford & Tonic', 'price'=>'650', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Whiskey & cola', 'price'=>'650', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Berry Bang', 'price'=>'4шт 770', 'desc'=>'Шоты'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Strawberry Morning', 'price'=>'4 шота 770', 'desc'=>'Шоты'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Boyarskiy', 'price'=>'390', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Nagasaki', 'price'=>'490', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Red Dog', 'price'=>'490', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Б-52', 'price'=>'490', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'header', 'title'=>'Beer', 'price'=>'', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Guinness Draught', 'price'=>'0,44 650', 'desc'=>'0.44'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Lowenbrau Original', 'price'=>'0,45 550', 'desc'=>'0.45'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Spaten Munchen', 'price'=>'0,44 600', 'desc'=>'0.44'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Corona Extra', 'price'=>'0,355 650', 'desc'=>'0.355'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Franziskaner Hefe-Weisse', 'price'=>'0,44 600', 'desc'=>'0.44'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Kasteel Rouge', 'price'=>'0,33 690', 'desc'=>'0.33'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Tsingtao Premium Lager', 'price'=>'0,33 540', 'desc'=>'0.33'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Tsingtao Stout', 'price'=>'0,33 540', 'desc'=>'0.33'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Tsingtao Wheat', 'price'=>'0,33 540', 'desc'=>'0.33'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Stella Artois N.A.', 'price'=>'0,44 490', 'desc'=>'0.44'],
    ['folder'=>'bar', 'type'=>'header', 'title'=>'Разливное Пиво', 'price'=>'', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Hösl Mein Helles', 'price'=>'0,5 700', 'desc'=>'0.5 Разливное'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Hösl Weissbier Resi', 'price'=>'0,5 700', 'desc'=>'0.5 Разливное'],
    ['folder'=>'bar', 'type'=>'header', 'title'=>'Медовуха (0.5)', 'price'=>'', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Плата Мимира', 'price'=>'650', 'desc'=>'Голубика-орех, Россия, 4.7%'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Кровь Ётуна', 'price'=>'650', 'desc'=>'Ежевика, Россия, 4.7%'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Огненный Великан Сурт', 'price'=>'650', 'desc'=>'Облепиха, Россия, 4.7%'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Дыхание Одина', 'price'=>'650', 'desc'=>'Имбирный Эль, Россия, 4.7%'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Хёд', 'price'=>'650', 'desc'=>'Охмеленная медовуха, Россия, 4.7%'],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Чудь', 'price'=>'650', 'desc'=>'Абрикос-каштан, Россия, 4.7%'],
    ['folder'=>'bar', 'type'=>'header', 'title'=>'Крепкий Алкоголь (0.04)', 'price'=>'', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Водка Царская золотая', 'price'=>'430', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Водка Pila', 'price'=>'650', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Ром Oakheart Original Spiced Gold', 'price'=>'550', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Ром Caribu Black-Strap Spiced', 'price'=>'600', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Ром Highball Express Reserve Blend 12', 'price'=>'950', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Ром Ron Zacapa Centenario XO', 'price'=>'1750', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Текила Espolon Blanco', 'price'=>'740', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Мескаль Buen Amigo', 'price'=>'740', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Текила Clase Azul Plata', 'price'=>'3500', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Виски William Lawson\'s', 'price'=>'490', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Виски Ballantine’s', 'price'=>'490', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Виски Dewars White Label', 'price'=>'550', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Виски Jameson', 'price'=>'650', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Бурбон Jim Beam Red Stag', 'price'=>'700', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Виски Jack Daniels', 'price'=>'660', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Виски Singleton 12 Years Old', 'price'=>'1050', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Виски Lagavulin 8 Y.O', 'price'=>'1750', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Виски Singleton 18 Years Old', 'price'=>'1750', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Джин Bosford', 'price'=>'500', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Джин Hokku summer', 'price'=>'550', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Джин Bulldog London Dry', 'price'=>'690', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Джин Bombay Sapphire', 'price'=>'690', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Джин Ukiyo Japanese Blossom/Yuzu', 'price'=>'740', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Бренди Magno', 'price'=>'570', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Коньяк Martell VS', 'price'=>'990', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Коньяк Martell VSOP', 'price'=>'1200', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Коньяк Hennessy V.S.O.P.', 'price'=>'1400', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Вермуты Martini (Bianco/Rosso/Fiero/Extra Dry)', 'price'=>'270', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Jagermeister', 'price'=>'540', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Aperol', 'price'=>'380', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Campari', 'price'=>'360', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Baileys', 'price'=>'420', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Cynar', 'price'=>'490', 'desc'=>''],
    ['folder'=>'bar', 'type'=>'item', 'title'=>'Skinos', 'price'=>'590', 'desc'=>''],

    // --- КУХНЯ ---
    ['folder'=>'kitchen', 'type'=>'header', 'title'=>'Закуски к пиву', 'price'=>'', 'desc'=>''],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Фисташки', 'price'=>'540', 'desc'=>'100г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Сырокопченое мясо курица', 'price'=>'450', 'desc'=>'40г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Сырокопченое мясо свинина', 'price'=>'540', 'desc'=>'40г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Сырокопченое мясо говядина', 'price'=>'540', 'desc'=>'40г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Сырокопченое мясо оленина', 'price'=>'650', 'desc'=>'40г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Бастурма говядина', 'price'=>'760', 'desc'=>'50г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Сыр охотничий спагетти', 'price'=>'430', 'desc'=>'100г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Арахис копченый', 'price'=>'330', 'desc'=>'100г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Чипсы Lay\'s', 'price'=>'300', 'desc'=>'70г, в ассортименте'],
    ['folder'=>'kitchen', 'type'=>'header', 'title'=>'Закуски/Допы', 'price'=>'', 'desc'=>''],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Сырное плато', 'price'=>'1750', 'desc'=>'Сыр горгонзола, сыр камамбер, сыр пармезан, сыр масдам, киви, яблоко, арахис, кешью, мед. 200г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Картофель фри', 'price'=>'490', 'desc'=>'С сырным соусом. 180/50г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Батат фри', 'price'=>'710', 'desc'=>'С трюфельным соусом. 150/50г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Креветки темпура', 'price'=>'820', 'desc'=>'С соусом васаби. 260/50г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Спринг ролл с тунцом / с лососем', 'price'=>'600', 'desc'=>'Тунец/лосось, рисовое тесто, огурец, понзу соус, салат айсберг, унаги соус, сыр сливочный, кунжут, икра масаго. 75г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Тар-Тар с тунцом / с лососем', 'price'=>'990', 'desc'=>'Тунец/лосось, сливочный мусс, гуакамоле, соус понзу, дайкон, кунжут, икра масаго, лепешка роти. 130г'],
    ['folder'=>'kitchen', 'type'=>'header', 'title'=>'Салаты', 'price'=>'', 'desc'=>''],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Салат с опаленным лососем', 'price'=>'920', 'desc'=>'Микс салата, лосось атлантический, соус понзу, огурец, томаты черри, авокадо, сыр пармезан, соус унаги. 230г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Цезарь с креветками', 'price'=>'870', 'desc'=>'Микс салата, креветки тигровые, тесто катаифи, томаты черри, сыр пармезан, соус цезарь. 240г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Греческий салат', 'price'=>'760', 'desc'=>'Томаты розовые, перец болгарский, огурец, салат ромэйн, сыр фета, маслины, лук красный, оливковый дрессинг. 210г'],
    ['folder'=>'kitchen', 'type'=>'header', 'title'=>'Роллы', 'price'=>'', 'desc'=>''],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Ролл Филадельфия классическая', 'price'=>'950', 'desc'=>'Японский рис, лосось атлантический, сливочный сыр, огурец, миндаль, ростки гороха. 240г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Ролл опаленный гребешок', 'price'=>'950', 'desc'=>'Японский рис, огурец, сливочный сыр, авокадо, гребешок, соус айоли, лук зеленый, кунжут, лепестки розы. 240г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Опаленный ролл с лососем, тунцом и гребешком', 'price'=>'1200', 'desc'=>'Японский рис, Тунец, авокадо, лосось атлантический, сливочный сыр, гребешок, соус айоле, соус унаги, соус черный трюфель, лук зеленый, икра масаго, кунжут. 260г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Ролл с хрустящей креветкой и трюфельным соусом', 'price'=>'950', 'desc'=>'Японский рис, тигровая креветка в темпуре, сырный соус, салат айсберг, краб снежный, соус айоли, лосось атлантический, соус белый трюфель, соус унаги, ростки гороха, кунжут, икра масаго. 270г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Запеченный ролл с жареной креветкой и сыром', 'price'=>'920', 'desc'=>'Японский рис, сыр гауда, снежный краб, соус шрирача, сырный соус, креветка тигровая, соус пикантный, унаги соус, кунжут, икра масаго, лук зеленый. 265г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Запеченный ролл с креветкой и соусом горгонзола', 'price'=>'920', 'desc'=>'Японский рис, сливочный сыр, огурец, соус горгонзола, тигровая креветка, соус унаги, лук зеленый, икра масаго кунжут, тесто катаифи. 240г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Запеченный ролл с креветкой темпура', 'price'=>'920', 'desc'=>'Японский рис, креветка тигровая в темпуре, лосось атлантический, снежный краб, соус васаби, соус унаги, лук зеленый, кунжут, масаго икра. 250г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Темпура ролл с креветкой', 'price'=>'920', 'desc'=>'Японский рис, кляр, тигровая креветка, авокадо, сливочный сыр, соус шрирачи, соус пикантный, соус унаги, кунжут, лук зеленый, икра масаго. 240г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Темпура ролл с гребешком', 'price'=>'920', 'desc'=>'Сливочный сыр, снежный краб, авокадо, кляр, сухари панко, соус трюфельный белый, соус унаги, икра масаго, кунжут. 200г'],
    ['folder'=>'kitchen', 'type'=>'header', 'title'=>'Боул / Поке', 'price'=>'', 'desc'=>''],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Поке с лососем', 'price'=>'870', 'desc'=>'Рис жасмин, лосось атлантический, брокколи, бобы эдамаме, кукуруза, перец болгарский, огурец, авокадо, черри, дайкон, соус унаги, соус ореховый. 315г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Поке с креветками', 'price'=>'790', 'desc'=>'Рис жасмин, креветка тигровая жареная, брокколи, бобы эдамаме, кукуруза, перец болгарский, огурец, авокадо, черри, дайкон, соус унаги, соус ореховый. 315г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Поке с тунцом', 'price'=>'790', 'desc'=>'Рис жасмин, тунец, брокколи, бобы эдамаме, кукуруза, перец болгарский, огурец, авокадо, черри, дайкон, соус пондзу. 315г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Поке с опаленным гребешком и трюфельным соусом', 'price'=>'890', 'desc'=>'Рис жасмин, гребешок опаленный, брокколи, бобы эдамаме, кукуруза, перец болгарский, огурец, авокадо, черри, дайкон, соус белый трюфель. 315г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Боул с тунцом и трюфельным соусом', 'price'=>'770', 'desc'=>'Тунец, салат айсберг, салат ромейн, огурец, брокколи, авокадо, перец болгарский, кукуруза, соус белый трюфель, соус унаги. 220г'],
    ['folder'=>'kitchen', 'type'=>'header', 'title'=>'WOK', 'price'=>'', 'desc'=>''],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Сливочный удон с морепродуктами', 'price'=>'870', 'desc'=>'Кальмар, креветки, мидии, грибы вешенки, лапша удон, фирменный соус вок, перец болгарский, лук зеленый, кинза, кунжут. 315г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Сливочный удон с курицей', 'price'=>'830', 'desc'=>'Куриное бедро, грибы вешенки, лапша удон, фирменный соус вок, перец болгарский, лук зеленый, кинза, кунжут. 315г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Удон с курицей', 'price'=>'830', 'desc'=>'Куриное бедро, соус перечный, перец болгарский, салат ромейн, грибы вешенки, лук зеленый, лапша удон, кинза, кунжут. 315г'],
    ['folder'=>'kitchen', 'type'=>'header', 'title'=>'Горячее', 'price'=>'', 'desc'=>''],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Лапша Пад-тай с креветкой', 'price'=>'870', 'desc'=>'Креветка тигровая, рисовая лапша, соус пад тай, яйцо, ростки сои, кинза, лук зеленый, арахис, перец чили. 340г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Рис по-тайски с креветками', 'price'=>'870', 'desc'=>'Креветка тигровая, рисовая лапша, соус пад тай, яйцо, ростки сои, кинза, лук зеленый, арахис, перец чили. 340г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Омлет омурайсу с курицей темпура', 'price'=>'850', 'desc'=>'Куриное бедро, кляр, яйцо, рис жасмин, перец болгарский, кукуруза, соус перечный, кокосовое молоко, унаги, пикантный соус. 325г'],
    ['folder'=>'kitchen', 'type'=>'header', 'title'=>'Супы', 'price'=>'', 'desc'=>''],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Том Ям с морепродуктами', 'price'=>'920', 'desc'=>'Креветки тигровые, кальмар, мидии, грибы вешенки, грибы маринованные Цоу Гау, кокосовое молоко, бульон том ям, лайм, рис жасмин, лук зеленый, кинза, перец чили. 370/60г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Том Ям с курицей', 'price'=>'870', 'desc'=>'Куриное бедро, грибы вешенки, грибы маринованные Цоу Гау, кокосовое молоко, бульон том ям, лайм, рис жасмин, лук зеленый, кинза, перец чили. 370/60г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Крем-суп из лосося', 'price'=>'920', 'desc'=>'Лосось, картофель, болгарский перец, стебель сельдерея, лук репчатый, сливки, креветки тигровые, икра масаго, ростки гороха, кунжут. 340г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Сливочно-сырный суп с курицей', 'price'=>'830', 'desc'=>'Куриное бедро, грибы вешенки, лук репчатый, сыр сливочный, сливки, сыр пармезан, лук зеленый. 300г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Сливочно-сырный суп с лососем', 'price'=>'890', 'desc'=>'Филе лосося, грибы вешенки, лук репчатый, сыр сливочный, сливки, сыр пармезан, лук зеленый. 280г'],
    ['folder'=>'kitchen', 'type'=>'header', 'title'=>'Десерты', 'price'=>'', 'desc'=>''],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Мороженое крафтовое в ассортименте', 'price'=>'350', 'desc'=>'Пломбир, клубника, банан, фисташка, молочный шоколад. 50г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Молочный коктейль', 'price'=>'540', 'desc'=>'Мороженое ванильное, молоко, сливки взбитые. 0.4л'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Печенье в облаках', 'price'=>'540', 'desc'=>'В основе этого нежного десерта облако сливочно-творожного мусса на шоколадной бисквитной подложке. Украшает пирожное легкий воздушный крем и шоколадное печенье. 130г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Чизкейк «матча»', 'price'=>'540', 'desc'=>'Чизкейк из сливочного сыра с добавлением зеленого чая матча, с нежным кремом на основе кокосового молока. 110г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Фруктовый ролл', 'price'=>'820', 'desc'=>'Соевое тесто, сладкий крем, ананас, киви, клубника, карамельный топинг. 200г'],
    ['folder'=>'kitchen', 'type'=>'item', 'title'=>'Сладкий ролл с ореховой пастой и шоколадом', 'price'=>'820', 'desc'=>'Соевое тесто, сладкий крем, ореховая паста, ананас, клубника, банан, манго, карамельный топинг, шоколадный топинг, кокосовая стружка. 220г']
];

echo "<h1>Starting Import...</h1>";

$i = 0;
foreach($menu_data as $item){
    $i++;
    // Создаем уникальное имя страницы
    $page_name = $CMS->slug( $item['title'] . '-' . $i ); 
    
    // Получаем ID папки
    $folder_id = $CMS->get_folder_id( $item['folder'] );
    
    if($folder_id) {
        $f = array();
        $f['k_page_title'] = $item['title'];
        $f['k_page_name'] = $page_name;
        $f['k_page_folder_id'] = $folder_id;
        
        $f['item_type'] = $item['type'];
        $f['item_price'] = $item['price'];
        $f['item_desc'] = $item['desc'];
        if(isset($item['cond'])) $f['item_cond'] = $item['cond'];
        $f['sort_order'] = $i;

        // Сохраняем в базу
        $pg = new KWebpage();
        $error = $pg->db_persist( $f );
        
        if( $error ){
            echo "Error importing '" . $item['title'] . "': " . $error . "<br>";
        } else {
            // Uncomment to see progress (optional)
            // echo "Imported: " . $item['title'] . "<br>";
        }
    } else {
        echo "Error: Folder '" . $item['folder'] . "' not found. <br>";
    }
}

echo "<h1>Done! Now delete this file.</h1>";
?>