<?php

namespace App\Http\Controllers;

use App\Models\IncomingPackage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderMediaController extends Controller
{
    public function downloadReceipt(Request $request, Order $order, Payment $payment): StreamedResponse
    {
        Gate::authorize('view', $order);

        if ($payment->order_id !== $order->id || ! $payment->receipt_file_path) {
            abort(404, 'Chek ne nayden.');
        }

        if (! Storage::disk('private')->exists($payment->receipt_file_path)) {
            abort(404, 'Fayl otсутstvuet v hranilishe.');
        }

        return Storage::disk('private')->download(
            $payment->receipt_file_path,
            "receipt-order-{$order->public_order_number}.pdf"
        );
    }

    public function downloadPackagePhoto(Request $request, IncomingPackage $package): StreamedResponse
    {
        Gate::authorize('view', $package);

        if (! $package->photos_path || ! Storage::disk('private')->exists($package->photos_path)) {
            abort(404, 'Fotografiya ne naydena.');
        }

        return Storage::disk('private')->download($package->photos_path);
    }

    public function downloadItemScreenshot(Request $request, Order $order, OrderItem $item): StreamedResponse
    {
        Gate::authorize('view', $order);

        if ($item->order_id !== $order->id || ! $item->screenshot_path) {
            abort(404, 'Skrinshot ne nayden.');
        }

        if (! Storage::disk('private')->exists($item->screenshot_path)) {
            abort(404, 'Fayl otсутstvuet v hranilishe.');
        }

        return Storage::disk('private')->download($item->screenshot_path);
    }
}