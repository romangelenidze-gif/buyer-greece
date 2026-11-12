<?php

namespace App\Http\Controllers;

use App\Services\CategoryMatcher;
use App\Services\ProductAutocompleteService;
use App\Services\SmartProductSearchManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductSearchController extends Controller
{
    public function __construct(protected SmartProductSearchManager $searchManager)
    {
    }

    public function index(Request $request, CategoryMatcher $categories): View|RedirectResponse|JsonResponse
    {
        $query = trim((string) $request->input('q', ''));
        $page = max(1, (int) $request->input('page', 1));
        $searchResult = null;

        $filters = [
            'min_price' => $request->filled('min_price') ? (float) $request->input('min_price') : null,
            'max_price' => $request->filled('max_price') ? (float) $request->input('max_price') : null,
            'sort' => in_array($request->input('sort'), ['relevance', 'price_asc', 'price_desc'], true)
                ? $request->input('sort')
                : 'relevance',
        ];

        $filters = array_filter($filters, fn ($val) => $val !== null && $val !== '');

        if ($query !== '') {
            $query = mb_substr($query, 0, 200);
            $hasPriceFilter = isset($filters['min_price']) || isset($filters['max_price']);

            // Если запрос — чистая категория без фильтров цены и не AJAX-подгрузка, ведём на страницу категории
            if (!$hasPriceFilter && !$request->expectsJson() && ($slug = $categories->resolve($query))) {
                $sort = $filters['sort'] ?? null;

                return redirect()->route('feed-category.show', array_filter([
                    'slug' => $slug,
                    'sort' => in_array($sort, ['price_asc', 'price_desc'], true) ? $sort : null,
                ]));
            }

            $searchResult = $this->searchManager->search($query, app()->getLocale(), $filters, $page);

            // Поддержка кнопки «Показать ещё» (AJAX)
            if ($request->expectsJson()) {
                $html = '';
                foreach ($searchResult['products'] ?? [] as $product) {
                    $html .= view('components.public-ui.product-card', [
                        'product' => $product,
                        'isFirst' => false,
                    ])->render();
                }

                return response()->json([
                    'html' => $html,
                    'pagination' => $searchResult['pagination'] ?? null,
                ]);
            }
        }

        return view('public.product-search', compact('query', 'searchResult', 'filters'));
    }

    public function autocomplete(Request $request, ProductAutocompleteService $autocompleteService): JsonResponse
    {
        $query = trim((string) $request->input('q', ''));
        $suggestions = $autocompleteService->getSuggestions($query, app()->getLocale());

        return response()->json([
            'suggestions' => $suggestions,
        ]);
    }
}