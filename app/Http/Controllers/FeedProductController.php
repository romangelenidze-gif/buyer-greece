<?php

namespace App\Http\Controllers;

use App\Models\FeedProduct;
use App\Services\GroqAiService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Детальная страница товара из партнёрского фида: /product/{feed}/{sku}.
 */
class FeedProductController extends Controller
{
    public function show(Request $request, string $feed, string $sku, GroqAiService $groq): View
    {
        $product = $this->findProduct($feed, $sku);

        // Другие расцветки той же модели.
        $colorways = $product->model_code
            ? FeedProduct::query()
                ->where('feed', $feed)
                ->where('model_code', $product->model_code)
                ->where('is_active', true)
                ->orderBy('colour')
                ->get(['id', 'feed', 'sku', 'colour', 'image_url'])
            : collect([$product]);

        // Название, характеристики и категории переводим на язык интерфейса одним вызовом.
        // Готовые переводы берутся из кэша GroqAiService (30 дней), поэтому повторные открытия бесплатны.
        $categories = array_values(array_filter(array_map('trim', explode(',', (string) $product->category_path))));
        $specs = (array) ($product->specs ?? []);
        $texts = array_merge([$product->name], $specs, $categories);
        $translated = $groq->translateProductsBack($texts, app()->getLocale());

        if (count($translated) !== count($texts)) {
            $translated = $texts;
        }

        $previous = url()->previous();
        $backUrl = str_contains($previous, '/product-search') ? $previous : route('product-search');

        return view('public.feed-product', [
            'product' => $product,
            'title' => (string) $translated[0],
            'specs' => array_slice($translated, 1, count($specs)),
            'categories' => array_slice($translated, 1 + count($specs)),
            'colorways' => $colorways,
            'backUrl' => $backUrl,
            'outboundUrl' => $product->outboundUrl(),
            'storeName' => (string) config("feeds.feeds.{$feed}.store_name", $feed),
        ]);
    }


    protected function findProduct(string $feed, string $sku): FeedProduct
    {
        return FeedProduct::query()
            ->where('feed', $feed)
            ->where('sku', $sku)
            ->where('is_active', true)
            ->firstOrFail();
    }
}
