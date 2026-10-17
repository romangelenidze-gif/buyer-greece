<?php

namespace App\Http\Controllers;

use App\Services\SmartProductSearchManager;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

        if (!empty($query)) {
            $query = mb_substr($query, 0, 200);
            $searchResult = $this->searchManager->search($query, app()->getLocale());
        }

        // Возвращаем правильный путь к шаблону в папке public
        return view('public.product-search', compact('query', 'searchResult'));
    }
}