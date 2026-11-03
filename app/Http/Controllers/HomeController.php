<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = [
            ['name' => 'Mobile', 'img' => 'mobile.png', 
            'url' => 'https://www.bestprice.gr/cat/806/mobile-phones.html'],
            ['name' => 'iPhone', 'img' => 'iphone.png',
            'url' => 'https://www.bestprice.gr/hub/25/iphone.html'],
            ['name' => 'White goods', 'img' => 'coffee-machine.png',
            'url' => 'https://www.bestprice.gr/cat/2483/home-appliances.html'],
            ['name' => 'Perfume', 'img' => 'perfume.png',
            'url' => 'https://www.bestprice.gr/cat/3035/aromata.html'],
            ['name' => 'Cameras', 'img' => 'cameras.png',
            'url' => 'https://www.bestprice.gr/cat/8494/fotografikes-mixanes.html'],
            ['name' => 'Laptops', 'img' => 'laptops.png',
            'url' => 'https://www.bestprice.gr/cat/2591/laptops.html'],
            ['name' => "Men's Jackets", 'img' => 'mens-jackets.png',
            'url' => 'https://www.bestprice.gr/cat/3423/andrika-mpoufan.html'],
            ['name' => "Women's Jackets", 'img' => 'womens-jackets.png',
            'url' => 'https://www.bestprice.gr/cat/3424/gynaikeia-mpoufan.html'],
            ['name' => "Men's Sports Shoes", 'img' => 'mens-sports-shoes.png',
            'url' => 'https://www.bestprice.gr/cat/2125/andrika-athlitika-papoutsia.html'],
            ['name' => 'Toys', 'img' => 'toys.png',
            'url' => 'https://www.bestprice.gr/cat/875/paidika-paixnidia.html'],
            ['name' => 'Smartwatches', 'img' => 'smartwatches.png',
            'url' => 'https://www.bestprice.gr/cat/6280/smartwatches.html'],
            ['name' => 'Face Creams', 'img' => 'face-creams.png',
            'url' => 'https://www.bestprice.gr/cat/6025/kremes-prosopou.html'],
            ['name' => "Women's Sneakers", 'img' => 'womens-sneakers.png',
            'url' => 'https://www.bestprice.gr/cat/6385/gynaikeia-sneakers.html'],
            ['name' => 'Olive Oil', 'img' => 'olive-oil.png',
            'url' => 'https://www.bestprice.gr/cat/8210/elaiolado.html?from=search'],
            ['name' => "Women's Jeans", 'img' => 'womens-jeans.png',
            'url' => 'https://www.bestprice.gr/cat/2073/gynaikeia-jeans.html'],
            ['name' => 'Tablets', 'img' => 'tablets.png',
            'url' => 'https://www.bestprice.gr/cat/3446/tablets.html'],
            ['name' => "Children's Fashion", 'img' => 'childrens-fashion.png',
            'url' => 'https://www.bestprice.gr/cat/2140/paidiki-vrefiki-moda.html'],
            ['name' => "Women's Boots", 'img' => 'womens-boots.png',
            'url' => 'https://www.bestprice.gr/cat/2087/gynaikeia-papoutsia.html'],
            ['name' => 'Dresses', 'img' => 'dresses.png',
            'url' => 'https://www.bestprice.gr/cat/2072/foremata.html'],
            ['name' => 'Sales & Offers', 'img' => 'sales-offers.png',
            'url' => 'https://www.bestprice.gr/deals']
        ];

        $brands = ['zara', 'bershka', 'mango', 'massimodutti', 'adidas', 'h&m', 'pull&bear'];

        return view('public.home', compact('categories', 'brands'));
    }
}