<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = [
            ['name' => 'Мобильные телефоны', 'img' => 'mobile.png', 
            'url' => 'https://www.bestprice.gr/cat/806/mobile-phones.html'],
            ['name' => 'iPhone', 'img' => 'iphone.png',
            'url' => 'https://www.bestprice.gr/hub/25/iphone.html'],
            ['name' => 'Бытовая техника', 'img' => 'coffee-machine.png',
            'url' => 'https://www.bestprice.gr/cat/2483/home-appliances.html'],
            ['name' => 'Парфюмерия', 'img' => 'perfume.png',
            'url' => 'https://www.bestprice.gr/cat/3035/aromata.html'],
            ['name' => 'Фотоаппараты и камеры', 'img' => 'cameras.png',
            'url' => 'https://www.bestprice.gr/cat/8494/fotografikes-mixanes.html'],
            ['name' => 'Ноутбуки', 'img' => 'laptops.png',
            'url' => 'https://www.bestprice.gr/cat/2591/laptops.html'],
            ['name' => 'Мужские куртки', 'img' => 'mens-jackets.png',
            'url' => 'https://www.bestprice.gr/cat/3423/andrika-mpoufan.html'],
            ['name' => 'Женские куртки', 'img' => 'womens-jackets.png',
            'url' => 'https://www.bestprice.gr/cat/3424/gynaikeia-mpoufan.html'],
            ['name' => 'Мужская спортивная обувь', 'img' => 'mens-sports-shoes.png',
            'url' => 'https://www.bestprice.gr/cat/2125/andrika-athlitika-papoutsia.html'],
            ['name' => 'Игрушки', 'img' => 'toys.png',
            'url' => 'https://www.bestprice.gr/cat/875/paidika-paixnidia.html'],
            ['name' => 'Смарт-часы', 'img' => 'smartwatches.png',
            'url' => 'https://www.bestprice.gr/cat/6280/smartwatches.html'],
            ['name' => 'Кремы для лица', 'img' => 'face-creams.png',
            'url' => 'https://www.bestprice.gr/cat/6025/kremes-prosopou.html'],
            ['name' => 'Женские кроссовки', 'img' => 'womens-sneakers.png',
            'url' => 'https://www.bestprice.gr/cat/6385/gynaikeia-sneakers.html'],
            ['name' => 'Оливковое масло', 'img' => 'olive-oil.png',
            'url' => 'https://www.bestprice.gr/cat/8210/elaiolado.html?from=search'],
            ['name' => 'Женские джинсы', 'img' => 'womens-jeans.png',
            'url' => 'https://www.bestprice.gr/cat/2073/gynaikeia-jeans.html'],
            ['name' => 'Планшеты', 'img' => 'tablets.png',
            'url' => 'https://www.bestprice.gr/cat/3446/tablets.html'],
            ['name' => 'Детская мода', 'img' => 'childrens-fashion.png',
            'url' => 'https://www.bestprice.gr/cat/2140/paidiki-vrefiki-moda.html'],
            ['name' => 'Женские ботинки', 'img' => 'womens-boots.png',
            'url' => 'https://www.bestprice.gr/cat/2087/gynaikeia-papoutsia.html'],
            ['name' => 'Платья', 'img' => 'dresses.png',
            'url' => 'https://www.bestprice.gr/cat/2072/foremata.html'],
            ['name' => 'Акции и скидки', 'img' => 'sales-offers.png',
            'url' => 'https://www.bestprice.gr/deals']
        ];

        // Массив брендов с прямыми ссылками на греческие сайты (на английском)
        $brands = [
            ['name' => 'Zara', 'url' => 'https://www.zara.com/gr/en/'],
            ['name' => 'Mango', 'url' => 'https://shop.mango.com/gr/en'],
            ['name' => 'Bershka', 'url' => 'https://www.bershka.com/gr/en/'],
            ['name' => 'Massimo Dutti', 'url' => 'https://www.massimodutti.com/gr/en/'],
            ['name' => 'Marks & Spencer', 'url' => 'https://www.marksandspencer.com/en-gr/home'],
            ['name' => 'H&M', 'url' => 'https://www2.hm.com/en_gr/index.html'],
            ['name' => 'Pull&Bear', 'url' => 'https://www.pullandbear.com/gr/en/'],
        ];

        return view('public.home', compact('categories', 'brands'));
    }
}