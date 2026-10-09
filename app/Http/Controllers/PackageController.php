<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PackageController extends Controller
{
    /**
     * Отображение списка посылок клиента (Ожидаемые и Полученные)
     */
    public function index(): View
    {
        $user = Auth::user();
        $customer = $user->customer;

        $expectedPackages = collect();
        $receivedPackages = collect();

        if ($customer) {
            $expectedPackages = $customer->incomingPackages()
                ->whereIn('status', ['expected', 'announced', 'in_transit'])
                ->latest()
                ->get();

            $receivedPackages = $customer->incomingPackages()
                ->whereIn('status', ['received', 'processing', 'ready_for_shipment'])
                ->latest()
                ->get();
        }

        return view('app.packages.index', compact('expectedPackages', 'receivedPackages'));
    }
}