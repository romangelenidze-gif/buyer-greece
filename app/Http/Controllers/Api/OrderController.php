<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show(Request $request, Order $order)
    {
        $user = $request->user();

        if ($user && isset($user->customer_id) && $user->customer_id !== $order->customer_id) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json($order);
    }
}