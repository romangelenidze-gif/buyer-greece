<?php

namespace App\Http\Controllers;

use App\Services\SmartProductSearchManager;
use App\Services\ProductAutocompleteService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ProductSearchController extends Controller
{
    protected SmartProductSearchManager $searchManager;

    public function __construct(SmartProductSearchManager $searchManager)
    {
        $this->searchManager = $searchManager;
    }

    public function index(Request $request): View
    {
        $query = trim($request->input('q', ''));
        $searchResult = null;

        // Собираем и валидируем пользовательские фильтры из запроса
        $filters = [
            'min_price' => $request->filled('min_price') ? (float)$request->input('min_price') : null,
            'max_price' => $request->filled('max_price') ? (float)$request->input('max_price') : null,
            'sort'      => in_array($request->input('sort'), ['relevance', 'price_asc', 'price_desc']) 
                           ? $request->input('sort') 
                           : 'relevance',
        ];

        // Убираем пустые фильтры, чтобы не засорять логику поиска
        $filters = array_filter($filters, fn($val) => !is_null($val) && $val !== '');

        if (!empty($query)) {
            $query = mb_substr($query, 0, 200);
            // Передаем запрос, текущую локаль и собранные фильтры в модернизированный менеджер поиска
            $searchResult = $this->searchManager->search($query, app()->getLocale(), $filters);
        }

        return view('public.product-search', compact('query', 'searchResult', 'filters'));
    }

    /**
     * Обработка AJAX-запросов автокомплита.
     */
    public function autocomplete(Request $request, ProductAutocompleteService $autocompleteService): JsonResponse
    {
        $query = trim($request->input('q', ''));
        $suggestions = $autocompleteService->getSuggestions($query, app()->getLocale());

        return response()->json([
            'suggestions' => $suggestions
        ]);
    }
}