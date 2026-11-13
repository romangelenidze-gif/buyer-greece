<?php

namespace App\Http\Controllers;

use App\Models\FeedProduct;
use App\Services\FeedCategoryTiles;
use App\Services\Feeds\FeedEntityLocalizer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request, FeedCategoryTiles $categoryTiles, FeedEntityLocalizer $localizer): View
    {
        $locale = app()->getLocale();

        // 1. Популярные товары Adidas (бестселлеры по рейтингу)
        $rawPopular = FeedProduct::query()
            ->where('feed', 'adidas')
            ->where('is_active', true)
            ->where('in_stock', true)
            ->orderByRaw('rating IS NULL')
            ->orderByDesc('rating')
            ->limit(8)
            ->get();

        $popularProducts = collect(
            $localizer->localizeCardTitles(
                $rawPopular->map(fn (FeedProduct $p) => $p->toCard())->all(),
                $locale
            )
        );

        // 2. Специальные предложения и скидки Adidas (Awin Deals)
        $rawDiscounts = FeedProduct::query()
            ->where('feed', 'adidas')
            ->where('is_active', true)
            ->where('in_stock', true)
            ->where('discount_percent', '>', 0)
            ->orderByDesc('discount_percent')
            ->limit(4)
            ->get();

        $discountProducts = collect(
            $localizer->localizeCardTitles(
                $rawDiscounts->map(fn (FeedProduct $p) => $p->toCard())->all(),
                $locale
            )
        );

        // 3. Баннер Awin из конфига
        $promoBanner = [
            'title' => 'Adidas Official Collection',
            'subtitle' => __('Официальные спортивные технологии, одежда и обувь с доставкой из партнерского каталога.'),
            'image_url' => config('feeds.feeds.adidas.banner_image'),
            'link' => route('product-search', ['q' => 'adidas']),
        ];

        // 4. Плитки категорий (данные из фида, список в config/home_categories.php)
        $categoryTiles = $categoryTiles->get();

        return view('public.home', compact('popularProducts', 'discountProducts', 'promoBanner', 'categoryTiles'));
    }
}