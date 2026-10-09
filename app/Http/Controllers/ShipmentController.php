<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    /**
     * Отображение списка отправок в Грузию (Camex)
     */
    public function index(): View
    {
        $user = Auth::user();
        $customer = $user->customer;

        $shipments = collect();

        if ($customer) {
            $shipments = Shipment::where('customer_id', $customer->id)
                ->with(['packages'])
                ->latest()
                ->get();
        }

        return view('app.shipments.index', compact('shipments'));
    }
}