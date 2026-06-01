# Инструкция по исправлению верстки Garden Lounge

## Что это за проект

Сайт лежит в папке `public_html`. Основная главная страница филиала находится здесь:

- `public_html/admiralteyskaya/index.php`

Главная страница собирается из отдельных блоков CouchCMS:

1. `couch/snippets/header.html` - первый экран, меню, hero.
2. `couch/snippets/about.html` - второй блок главной, блок Philosophy / Концепция.
3. `couch/snippets/gallery.html` - галерея.
4. `couch/snippets/menu.html` - меню.
5. `couch/snippets/akzii.html` - акции.
6. `couch/snippets/reservation.html` - бронирование.
7. `couch/snippets/contacts.html` - контакты.
8. `couch/snippets/filial.html` - филиалы.

Общие стили подключаются из:

- `public_html/admiralteyskaya/main.css`
- `public_html/admiralteyskaya/couch/snippets/styles.html`
- также часть CSS сейчас встроена прямо внутрь отдельных сниппетов.

## Текущая проблема

Верстка начала съезжать начиная со второго блока главной страницы. Второй блок - это `about.html`, контейнер:

```html
<div id="about-us" class="philosophy-section-container">
```

В активном `about.html` используются классы:

- `philosophy-section-container`
- `philosophy-wrapper`
- `content-limiter`
- `title-philosophy`
- `title-concept`
- `slogan-rituals`
- `separator-img`

Но в активном `main.css` и `styles.html` не хватает полного описания `philosophy-wrapper` и `content-limiter`. В старых копиях сниппета, например `about12345.html`, эти стили есть. Из-за этого второй блок может терять высоту, центрирование, ограничения ширины и нормальные отступы.

Также в `public_html/admiralteyskaya/couch/snippets/styles.html` в самом конце есть незакрытый `@media (max-width: 767px)`: перед `</style>` не хватает закрывающей фигурной скобки. Это нужно исправить в первую очередь, потому что одна ошибка CSS может ломать последующие правила.

## Правила правки

1. Сначала править только активные файлы, не копии:
   - `public_html/admiralteyskaya/couch/snippets/about.html`
   - `public_html/admiralteyskaya/main.css`
   - `public_html/admiralteyskaya/couch/snippets/styles.html`

2. Не удалять старые файлы-копии. Они могут быть резервными версиями.

3. Не менять данные CouchCMS и PHP-поля без необходимости. Контент подставляется через `cms:show`, его не нужно заменять статичным текстом.

4. Не использовать фиксированную ширину больше экрана. Для секций использовать:
   - `width: 100%`
   - `max-width`
   - `padding-inline`
   - `box-sizing: border-box`

5. Не использовать `width: 100vw` вместе с `left: 50%` и `margin-left: -50vw`, если блок уже находится в обычном потоке страницы. Это частая причина горизонтального съезда на мобильных и планшетах.

6. Все изображения должны иметь:
   - `max-width: 100%`
   - `height: auto`
   - для фоновых/карточных изображений при необходимости `object-fit: cover`

7. Для мобильной версии не опираться только на брейкпоинт `767px`. Проверять и поддерживать минимум:
   - 360px
   - 390px
   - 430px
   - 768px
   - 820px
   - 1024px
   - 1280px
   - 1440px

8. Планшеты не считать десктопом автоматически. Для диапазона 768-1024px лучше добавлять отдельные мягкие правила: меньше отступы, аккуратнее сетки, меньше фиксированных высот.

## План исправления второго блока

1. Исправить синтаксис в `styles.html`: закрыть незакрытый мобильный `@media`.

2. Вынести базовые стили второго блока в `main.css`, чтобы активный `about.html` не зависел от старых копий:

```css
.philosophy-section-container {
  width: 100%;
  margin: 0;
  padding: 0;
  background: #000;
  position: relative;
  overflow: hidden;
}

.philosophy-wrapper {
  width: 100%;
  min-height: clamp(520px, 70vh, 720px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: clamp(48px, 8vw, 96px) 20px;
  position: relative;
}

.content-limiter {
  width: 100%;
  max-width: 600px;
  margin: 0 auto;
  position: relative;
  z-index: 2;
}
```

3. Для планшетов добавить отдельную настройку:

```css
@media (min-width: 768px) and (max-width: 1024px) {
  .philosophy-wrapper {
    min-height: auto;
    padding: 72px 32px;
  }
}
```

4. Для мобильных убрать лишнюю высоту и зафиксировать нормальные отступы:

```css
@media (max-width: 767px) {
  .philosophy-wrapper {
    min-height: auto;
    padding: 48px 20px 56px;
  }

  .content-limiter {
    max-width: 100%;
  }

  .title-concept,
  .slogan-rituals {
    overflow-wrap: anywhere;
  }
}
```

5. После второго блока проверить следующий блок `gallery.html`, потому что визуально съезд может начинаться во втором блоке, а усиливаться уже на галерее.

## Как проверять

После каждой правки смотреть главную страницу сверху вниз:

- hero не перекрывает второй блок;
- второй блок не уходит вбок;
- нет горизонтальной прокрутки;
- текст не вылезает за экран;
- разделитель-картинка не растягивается;
- следующий блок начинается ровно под вторым;
- меню и кнопка вверх не перекрывают важный текст на мобильных.

Минимальная проверка размеров:

- iPhone SE / 360px
- iPhone 12-15 / 390-430px
- iPad mini / 768px
- iPad Air / 820px
- планшет горизонтально / 1024px
- ноутбук / 1280px
- широкий экран / 1440px

## Нужна ли база данных

Для исправления CSS и верстки база данных не нужна.

Для полноценного локального запуска сайта с реальным контентом база данных нужна, потому что сайт работает на CouchCMS и берет поля через `cms:show`, `cms:pages`, `cms:editable`. Настройки подключения уже есть в:

- `public_html/admiralteyskaya/couch/config.php`

Если нужно увидеть сайт локально точно как на хостинге, понадобится дамп MySQL-базы CouchCMS. Если задача только поправить верстку по файлам, можно начинать без дампа, но финальную проверку лучше делать на рабочем окружении или с базой.

## Главный принцип

Сначала стабилизировать общий каркас: ширина, контейнеры, отступы, отсутствие горизонтального скролла. Потом уже доводить декоративные детали, анимации и точные размеры текста.
