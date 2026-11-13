<?php

namespace App\Http\Controllers;

use App\Models\FeedProduct;
use App\Services\Feeds\FeedEntityLocalizer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedProductController extends Controller
{
    public function show(Request $request, string $feed, string $sku, FeedEntityLocalizer $localizer): View
    {
        $product = $this->findProduct($feed, $sku);

        $colorways = $product->model_code
            ? FeedProduct::query()
                ->where('feed', $feed)
                ->where('model_code', $product->model_code)
                ->where('is_active', true)
                ->orderBy('colour')
                ->get(['id', 'feed', 'sku', 'colour', 'image_url'])
            : collect([$product]);

        $relatedProducts = FeedProduct::query()
            ->where('feed', $feed)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('in_stock', true)
            ->when($product->category_top, fn ($q) => $q->where('category_top', $product->category_top))
            ->when($product->gender, fn ($q) => $q->where('gender', $product->gender))
            ->limit(4)
            ->get();

        $localized = $localizer->localizeProduct($product, app()->getLocale());

        $previous = url()->previous();
        $backUrl = str_contains($previous, '/product-search') ? $previous : route('product-search');

        return view('public.feed-product', [
            'product' => $product,
            'title' => $localized['title'],
            'specs' => $localized['specs'],
            'categories' => $localized['categories'],
            'colorways' => $colorways,
            'relatedProducts' => $relatedProducts,
            'backUrl' => $backUrl,
            'outboundUrl' => $product->outboundUrl(),
            'storeName' => (string) config("feeds.feeds.{$feed}.store_name", $product->brand ?: $feed),
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