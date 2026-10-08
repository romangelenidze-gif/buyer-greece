<?php

namespace App\Http\Controllers;

use App\Models\IncomingPackage;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderMediaController extends Controller
{
    /**
     * Безопасное скачивание чека оплаты через Policy.
     */
    public function downloadReceipt(Request $request, Order $order, Payment $payment): StreamedResponse
    {
        Gate::authorize('view', $order);

        if ($payment->order_id !== $order->id || ! $payment->receipt_file_path) {
            abort(404, 'Чек не найден.');
        }

        if (! Storage::disk('private')->exists($payment->receipt_file_path)) {
            abort(404, 'Файл отсутствует в хранилище.');
        }

        return Storage::disk('private')->download(
            $payment->receipt_file_path,
            "receipt-order-{$order->public_order_number}.pdf"
        );
    }

    /**
     * Безопасное скачивание фото посылки через Policy.
     */
    public function downloadPackagePhoto(Request $request, IncomingPackage $package): StreamedResponse
    {
        Gate::authorize('view', $package);

        if (! $package->photos_path || ! Storage::disk('private')->exists($package->photos_path)) {
            abort(404, 'Фотография не найдена.');
        }

        return Storage::disk('private')->download($package->photos_path);
    }
}