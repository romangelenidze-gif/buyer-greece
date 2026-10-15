<?php

namespace App\Http\Controllers;

use App\Services\GeminiProductSearchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductSearchController extends Controller
{
    public function index(Request $request, GeminiProductSearchService $searchService): View
    {
        $query = trim($request->input('q', ''));
        $searchResult = null;

        if (!empty($query)) {
            $query = mb_substr($query, 0, 200);
            $searchResult = $searchService->search($query);
        }

        return view('public.product-search', compact('query', 'searchResult'));
    }
}