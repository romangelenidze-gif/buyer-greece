<?php

/**
 * Категории на главной (компактные плашки без картинок), до 20 штук.
 *
 * label — название (переводится через __()).
 * q     — поисковый запрос, на который ведёт плашка.
 * where — как отобрать товары фида для подсчёта. Допустимые ключи (можно сочетать):
 *           category_top  — точное значение колонки category_top (Παπούτσια / Ένδυση / Αξεσουάρ)
 *           category_path — подстрока в category_path; можно списком: совпадение с любой из них
 *           gender        — men | women | unisex | kids
 *           discount      — true: только товары со скидкой
 *
 * Плашка без товаров в наличии скрывается автоматически.
 * Порядок здесь = порядок на странице.
 */
return [
    'feed' => 'adidas',

    'tiles' => [
        // По полу и возрасту
        ['label' => 'Женская обувь',      'q' => 'adidas women shoes',    'where' => ['gender' => 'women', 'category_top' => 'Παπούτσια']],
        ['label' => 'Женская одежда',     'q' => 'adidas women clothing', 'where' => ['gender' => 'women', 'category_top' => 'Ένδυση']],
        ['label' => 'Мужская обувь',      'q' => 'adidas men shoes',      'where' => ['gender' => 'men', 'category_top' => 'Παπούτσια']],
        ['label' => 'Мужская одежда',     'q' => 'adidas men clothing',   'where' => ['gender' => 'men', 'category_top' => 'Ένδυση']],
        ['label' => 'Детская обувь',      'q' => 'adidas kids shoes',     'where' => ['gender' => 'kids', 'category_top' => 'Παπούτσια']],
        ['label' => 'Детская одежда',     'q' => 'adidas kids clothing',  'where' => ['gender' => 'kids', 'category_top' => 'Ένδυση']],

        // Обувь
        ['label' => 'Кроссовки',          'q' => 'adidas sneakers',       'where' => ['category_path' => 'Sneakers']],
        ['label' => 'Беговая обувь',      'q' => 'adidas running shoes',  'where' => ['category_path' => 'Παπούτσια Τρεξίματος']],
        ['label' => 'Футбольные бутсы',   'q' => 'adidas football boots', 'where' => ['category_path' => 'Παπούτσια Ποδοσφαίρου']],
        ['label' => 'Обувь для зала',     'q' => 'adidas training shoes', 'where' => ['category_path' => 'Παπούτσια Γυμναστηρίου']],
        ['label' => 'Сланцы и шлёпанцы',  'q' => 'adidas slides',         'where' => ['category_path' => 'Παντόφλες και σαγιονάρες']],

        // Одежда
        ['label' => 'Спортивные костюмы', 'q' => 'adidas tracksuit',      'where' => ['category_path' => 'Αθλητικές Φόρμες']],
        ['label' => 'Худи и толстовки',   'q' => 'adidas hoodie',         'where' => ['category_path' => 'Φούτερ Και Ζακέτες']],
        ['label' => 'Куртки',             'q' => 'adidas jacket',         'where' => ['category_path' => ['Jackets', 'Μπουφάν']]],
        ['label' => 'Футболки и джерси',  'q' => 'adidas t-shirt',        'where' => ['category_path' => ['Φανέλες', 'Μπλούζες']]],
        ['label' => 'Шорты и леггинсы',   'q' => 'adidas shorts',         'where' => ['category_path' => ['Σορτς', 'Κολάν']]],
        ['label' => 'Платья и юбки',      'q' => 'adidas dress',          'where' => ['category_path' => 'Φούστες και Φορέματα']],

        // Аксессуары и подборки
        ['label' => 'Сумки и рюкзаки',    'q' => 'adidas bag',            'where' => ['category_path' => 'Τσάντες']],
        
    ],
];
