<?php

namespace App\Http\Controllers;

use App\Models\FeedProduct;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        // 1. Популярные товары Adidas (бестселлеры по рейтингу)
        $popularProducts = FeedProduct::query()
            ->where('feed', 'adidas')
            ->where('is_active', true)
            ->where('in_stock', true)
            ->orderByRaw('rating IS NULL')
            ->orderByDesc('rating')
            ->limit(8)
            ->get();

        // 2. Специальные предложения и скидки Adidas (Awin Deals)
        $discountProducts = FeedProduct::query()
            ->where('feed', 'adidas')
            ->where('is_active', true)
            ->where('in_stock', true)
            ->where('discount_percent', '>', 0)
            ->orderByDesc('discount_percent')
            ->limit(4)
            ->get();

        // 3. Баннер Awin из конфига
        $promoBanner = [
            'title' => 'Adidas Official Collection',
            'subtitle' => __('Официальные спортивные технологии, одежда и обувь с доставкой из партнерского каталога.'),
            'image_url' => config('feeds.feeds.adidas.banner_image'),
            'link' => route('product-search', ['q' => 'adidas']),
        ];

        // Категории для быстрого доступа (фильтры поиска по каталогу Adidas)
        $categories = [
            ['name' => 'Кроссовки (Sneakers)', 'img' => 'mens-sports-shoes.png', 'url' => route('product-search', ['q' => 'sneakers'])],
            ['name' => 'Одежда и худи', 'img' => 'mens-jackets.png', 'url' => route('product-search', ['q' => 'hoodie'])],
            ['name' => 'Спортивные костюмы', 'img' => 'childrens-fashion.png', 'url' => route('product-search', ['q' => 'tracksuit'])],
            ['name' => 'Аксессуары и сумки', 'img' => 'backpack.png', 'url' => route('product-search', ['q' => 'bag'])],
        ];

        return view('public.home', compact('popularProducts', 'discountProducts', 'promoBanner', 'categories'));
    }
}