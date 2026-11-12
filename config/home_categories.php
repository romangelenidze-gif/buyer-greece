<?php

/**
 * Категории на главной (компактные плашки без картинок), до 20 штук.
 * Каждая плашка ведёт на свою страницу /category/{slug} со списком именно её товаров.
 *
 * slug  — адрес страницы категории (латиница, без пробелов, уникальный).
 * label — название (переводится через __()).
 * where — как отобрать товары фида. Допустимые ключи (можно сочетать):
 *           category_top  — точное значение колонки category_top (Παπούτσια / Ένδυση / Αξεσουάρ)
 *           category_path — подстрока в category_path; можно списком: совпадение с любой из них
 *           gender        — men | women | unisex | kids
 *           discount      — true: только товары со скидкой
 *
 * Категория без товаров в наличии на главной не показывается.
 * Порядок здесь = порядок на главной.
 */
return [
    'feed' => 'adidas',

    'tiles' => [
        // По полу и возрасту
        ['slug' => 'women-shoes',    'label' => 'Женская обувь',      'where' => ['gender' => 'women', 'category_top' => 'Παπούτσια']],
        ['slug' => 'women-clothing', 'label' => 'Женская одежда',     'where' => ['gender' => 'women', 'category_top' => 'Ένδυση']],
        ['slug' => 'men-shoes',      'label' => 'Мужская обувь',      'where' => ['gender' => 'men', 'category_top' => 'Παπούτσια']],
        ['slug' => 'men-clothing',   'label' => 'Мужская одежда',     'where' => ['gender' => 'men', 'category_top' => 'Ένδυση']],
        ['slug' => 'kids-shoes',     'label' => 'Детская обувь',      'where' => ['gender' => 'kids', 'category_top' => 'Παπούτσια']],
        ['slug' => 'kids-clothing',  'label' => 'Детская одежда',     'where' => ['gender' => 'kids', 'category_top' => 'Ένδυση']],

        // Обувь
        ['slug' => 'sneakers',       'label' => 'Кроссовки',          'where' => ['category_path' => 'Sneakers'],
          'keywords' => ['sneakers', 'sneaker', 'кроссовк*', 'кед*', 'ბოტას*', 'კედ*']],
        ['slug' => 'running-shoes',  'label' => 'Беговая обувь',      'where' => ['category_path' => 'Παπούτσια Τρεξίματος'],
          'keywords' => ['running', 'runners', 'бег*', 'სარბენ*', 'τρεξιμ*']],
        ['slug' => 'football-boots', 'label' => 'Футбольные бутсы',   'where' => ['category_path' => 'Παπούτσια Ποδοσφαίρου'],
          'keywords' => ['football', 'soccer', 'cleats', 'бутс*', 'футбольн*', 'საფეხბურთო*', 'ბუც*', 'ποδοσφαιρ*']],
        ['slug' => 'training-shoes', 'label' => 'Обувь для зала',     'where' => ['category_path' => 'Παπούτσια Γυμναστηρίου'],
          'keywords' => ['training', 'gym', 'зал*', 'тренировочн*', 'დარბაზ*', 'γυμναστηρ*']],
        ['slug' => 'slides',         'label' => 'Сланцы и шлёпанцы',  'where' => ['category_path' => 'Παντόφλες και σαγιονάρες'],
          'keywords' => ['slides', 'slide', 'sandals', 'сланц*', 'шлепанц*', 'сандал*', 'ჩუსტ*', 'სანდალ*', 'σαγιοναρ*', 'παντοφλ*']],

        // Одежда
        ['slug' => 'tracksuits',     'label' => 'Спортивные костюмы', 'where' => ['category_path' => 'Αθλητικές Φόρμες'],
          'keywords' => ['tracksuit', 'tracksuits', 'костюм*', 'კოსტიუმ*', 'φορμ*']],
        ['slug' => 'hoodies',        'label' => 'Худи и толстовки',   'where' => ['category_path' => 'Φούτερ Και Ζακέτες'],
          'keywords' => ['hoodie', 'hoodies', 'sweatshirt', 'sweatshirts', 'худи', 'толстовк*', 'ჰუდ*', 'სვიტშოტ*', 'φουτερ*']],
        ['slug' => 'jackets',        'label' => 'Куртки',             'where' => ['category_path' => ['Jackets', 'Μπουφάν']],
          'keywords' => ['jacket', 'jackets', 'coat', 'coats', 'куртк*', 'пуховик*', 'ქურთუკ*', 'μπουφαν*']],
        ['slug' => 't-shirts',       'label' => 'Футболки и джерси',  'where' => ['category_path' => ['Φανέλες', 'Μπλούζες']],
          'keywords' => ['tshirt', 'tshirts', 'shirt', 'shirts', 'tee', 'jersey', 'jerseys', 'футболк*', 'майк*', 'джерси', 'მაისურ*', 'ჯერსი', 'φανελ*']],
        ['slug' => 'shorts',         'label' => 'Шорты и леггинсы',   'where' => ['category_path' => ['Σορτς', 'Κολάν']],
          'keywords' => ['shorts', 'short', 'leggings', 'legging', 'шорт*', 'легинс*', 'леггинс*', 'შორტ*', 'ლეგინს*', 'κολαν*']],
        ['slug' => 'dresses',        'label' => 'Платья и юбки',      'where' => ['category_path' => 'Φούστες και Φορέματα'],
          'keywords' => ['dress', 'dresses', 'skirt', 'skirts', 'плать*', 'юбк*', 'კაბ*', 'ქვედაბოლო*', 'φορεμ*', 'φουστ*']],

        // Аксессуары и подборки
        ['slug' => 'bags',           'label' => 'Сумки и рюкзаки',    'where' => ['category_path' => 'Τσάντες'],
          'keywords' => ['bag', 'bags', 'backpack', 'backpacks', 'сумк*', 'рюкзак*', 'ჩანთ*', 'ზურგჩანთ*', 'τσαντ*', 'σακιδι*']],
        ['slug' => 'accessories',    'label' => 'Аксессуары',         'where' => ['category_top' => 'Αξεσουάρ'],
          'keywords' => ['cap', 'caps', 'hat', 'hats', 'beanie', 'socks', 'gloves', 'кепк*', 'шапк*', 'носк*', 'перчатк*', 'წინდ*', 'ქუდ*', 'κάλτσ*', 'σκουφ*']],
        ['slug' => 'sale',           'label' => 'Скидки',             'where' => ['discount' => true],
          'keywords' => ['sale', 'discount', 'discounts', 'скидк*', 'распродаж*', 'акци*', 'ფასდაკლებ*', 'ფასდაკლ*', 'εκπτωσ*']],
    ],

    /**
     * Понятия для разбора запросов без LLM (search → страница категории, подсказки).
     * Запись со звёздочкой («женск*») — основа слова: подходит любое продолжение.
     * Запись без звёздочки — слово целиком (и с «s» на конце: women → womens).
     * Греческий пишите без ударений.
     */
    'concepts' => [
        'genders' => [
            'men'   => ['men', 'mens', 'man', 'male', 'мужск*', 'мужчин*', 'парн*', 'კაც*', 'მამაკაც*', 'ανδρικ*', 'ανδρων'],
            'women' => ['women', 'womens', 'woman', 'ladies', 'lady', 'female', 'женск*', 'женщин*', 'девушк*', 'ქალ*', 'გოგო*', 'გოგონ*', 'γυναικ*'],
            'kids'  => ['kids', 'kid', 'children', 'child', 'junior', 'juniors', 'boys', 'girls', 'youth', 'детск*', 'дет*', 'ребен*', 'мальчик*', 'девочк*', 'ბავშვ*', 'საბავშვო*', 'παιδικ*'],
        ],
        'types' => [
            'shoes'       => ['top' => 'Παπούτσια', 'words' => ['shoes', 'shoe', 'footwear', 'обув*', 'ფეხსაცმ*', 'παπουτσ*']],
            'clothing'    => ['top' => 'Ένδυση',    'words' => ['clothing', 'clothes', 'apparel', 'wear', 'одежд*', 'ტანსაცმ*', 'ρουχ*', 'ενδυσ*']],
            'accessories' => ['top' => 'Αξεσουάρ',  'words' => ['accessories', 'accessory', 'аксессуар*', 'აქსესუარ*', 'αξεσουαρ*']],
        ],
        // Слова, которые сами ничего не значат, но не мешают распознать категорию («спортивные кроссовки»).
        'fillers' => ['sport', 'sports', 'sporty', 'athletic', 'спортивн*', 'სპორტულ*', 'αθλητικ*'],
    ],
];
