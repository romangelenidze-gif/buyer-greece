<?php

namespace App\Http\Controllers;

use App\Models\FeedProduct;
use App\Services\FeedCategoryTiles;
use App\Services\Feeds\FeedEntityLocalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FeedCategoryController extends Controller
{
    public function show(Request $request, string $slug, FeedCategoryTiles $tiles, FeedEntityLocalizer $localizer): View|JsonResponse
    {
        $tile = $tiles->tile($slug);

        if ($tile === null) {
            abort(404);
        }

        $page = max(1, (int) $request->input('page', 1));
        $perPage = (int) config('feeds.search_limit', 24);

        $filters = [
            'min_price' => $request->filled('min_price') ? (float) $request->input('min_price') : null,
            'max_price' => $request->filled('max_price') ? (float) $request->input('max_price') : null,
            'sort' => in_array($request->input('sort'), ['relevance', 'price_asc', 'price_desc'], true)
                ? $request->input('sort')
                : 'relevance',
        ];
        $filters = array_filter($filters, fn ($val) => $val !== null && $val !== '');

        $base = $tiles->productsQuery($tile);

        if (!empty($filters['min_price'])) {
            $base->where('price', '>=', (float) $filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $base->where('price', '<=', (float) $filters['max_price']);
        }

        $grouped = (clone $base)
            ->selectRaw("feed, COALESCE(NULLIF(model_code, ''), sku) as group_key, MIN(id) as primary_id, MIN(price) as min_price, MAX(rating) as max_rating, MAX(discount_percent) as max_discount, COUNT(*) as colorways_count")
            ->groupBy('feed', 'group_key');

        $total = DB::query()->fromSub($grouped, 'g')->count();
        $sort = $filters['sort'] ?? 'relevance';

        if ($sort === 'price_asc') {
            $grouped->orderBy('min_price');
        } elseif ($sort === 'price_desc') {
            $grouped->orderByDesc('min_price');
        } else {
            $grouped->orderByRaw('COALESCE(MAX(rating), -1) DESC')->orderByDesc('max_discount')->orderBy('primary_id');
        }

        $rows = $grouped
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $primaryIds = $rows->pluck('primary_id')->all();
        $colorwaysMap = $rows->pluck('colorways_count', 'primary_id')->all();

        $productsMap = $primaryIds !== []
            ? FeedProduct::query()->whereIn('id', $primaryIds)->get()->keyBy('id')
            : collect();

        $cards = [];
        foreach ($primaryIds as $id) {
            /** @var FeedProduct|null $p */
            $p = $productsMap->get($id);
            if (!$p) {
                continue;
            }

            $store = (string) config("feeds.feeds.{$p->feed}.store_name", $p->brand ?: $p->feed);
            $formatted = '€' . number_format($p->price, 2, '.', ' ');

            $cards[] = $p->toCard() + [
                'original_title' => $p->name,
                'merchant' => $store,
                'price_from' => $p->price,
                'price_formatted' => $formatted,
                'price_note' => $formatted,
                'formatted_price' => $formatted,
                'offers_count' => 1,
                'rating_count' => null,
                'colorways_count' => (int) ($colorwaysMap[$id] ?? 1),
            ];
        }

        $cards = $localizer->localizeCardTitles($cards, app()->getLocale());

        $query = __((string) $tile['label']);
        $searchResult = [
            'query_summary' => $query,
            'recommendation' => __('Найдено моделей: ') . $total,
            'products' => $cards,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => max(1, (int) ceil($total / $perPage)),
            ],
            'active_filters' => $filters,
        ];

        if ($request->expectsJson()) {
            $html = '';
            foreach ($cards as $product) {
                $html .= view('components.public-ui.product-card', [
                    'product' => $product,
                    'isFirst' => false,
                ])->render();
            }

            return response()->json([
                'html' => $html,
                'pagination' => $searchResult['pagination'],
            ]);
        }

        return view('public.product-search', compact('query', 'searchResult', 'filters'));
    }
}