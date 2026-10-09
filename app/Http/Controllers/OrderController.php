<?php

namespace App\Http\Controllers;

use App\Actions\Orders\CreateOrderAction;
use App\Actions\Quotes\AcceptQuoteAction;
use App\Actions\Quotes\RejectQuoteAction;
use App\Enums\OrderType;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Quote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Обработка и сохранение формы «Выкупите за меня».
     */
    public function store(Request $request, CreateOrderAction $createOrderAction): RedirectResponse
    {
        $validated = $request->validate([
            'product_url' => ['required', 'url', 'max:2048'],
            'product_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'size' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'model' => ['nullable', 'string', 'max:100'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'screenshot' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $user = Auth::user();
        $customer = $user->customer;

        if (!$customer) {
            $nameParts = explode(' ', trim($user->name ?? 'Пользователь'), 2);
            $firstName = $nameParts[0] ?? 'Пользователь';
            $lastName = $nameParts[1] ?? '';

            $customer = Customer::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $user->email,
                'phone' => '',
                'status' => 'active',
            ]);

            $user->customer_id = $customer->id;
            $user->save();
        }

        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $screenshotPath = $request->file('screenshot')->store('orders/screenshots', 'private');
        }

        $itemsData = [
            [
                'product_url' => $validated['product_url'],
                'product_name' => $validated['product_name'],
                'quantity' => $validated['quantity'],
                'size' => $validated['size'] ?? null,
                'color' => $validated['color'] ?? null,
                'model' => $validated['model'] ?? null,
                'requested_comment' => $validated['comment'] ?? null,
                'screenshot_path' => $screenshotPath,
            ]
        ];

        $order = $createOrderAction->execute(
            customer: $customer,
            type: OrderType::BUY_FOR_ME,
            itemsData: $itemsData,
            internalNote: $validated['comment'] ?? null
        );

        return redirect()->back()->with([
            'order_created' => true,
            'order_number' => $order->public_order_number,
            'order_id' => $order->id,
            'success' => 'Заявка на выкуп успешно сформирована!',
        ]);
    }

    /**
     * Отображение списка заказов авторизованного пользователя.
     */
    public function index(): View
    {
        $user = Auth::user();
        $customer = $user->customer;

        $activeOrders = $customer
            ? Order::where('customer_id', $customer->id)
                ->with(['items', 'activeQuote'])
                ->latest()
                ->get()
            : collect();

        return view('app.dashboard', [
            'activeOrders' => $activeOrders,
            'stats' => [
                'expected_packages' => $customer ? $customer->incomingPackages()->where('status', 'expected')->count() : 0,
                'received_packages' => $customer ? $customer->incomingPackages()->where('status', 'received')->count() : 0,
                'ready_shipments' => 0,
            ],
        ]);
    }

    /**
     * Просмотр детализации конкретного заказа.
     */
    public function show(Order $order): View
    {
        $user = Auth::user();
        $customer = $user->customer;

        if (!$customer || $order->customer_id !== $customer->id) {
            abort(403, 'У вас нет доступа к данному заказу.');
        }

        $order->load(['items', 'activeQuote', 'payments', 'incomingPackages']);

        // Выбираем активный расчет из связи либо последний по времени создания
        $quote = $order->activeQuote ?? $order->quotes()->latest()->first();

        return view('app.orders.show', compact('order', 'quote'));
    }

    /**
     * Принятие расчета стоимости клиентом.
     */
    public function acceptQuote(Order $order, Quote $quote, AcceptQuoteAction $acceptQuoteAction): RedirectResponse
    {
        $user = Auth::user();
        $customer = $user->customer;

        if (!$customer || $order->customer_id !== $customer->id || $quote->order_id !== $order->id) {
            abort(403, 'У вас нет доступа к данному расчету.');
        }

        $acceptQuoteAction->execute($quote);

        $order->update([
            'active_quote_id' => $quote->id,
        ]);

        return redirect()->back()->with('success', 'Расчет стоимости успешно принят! Вы можете приступать к оплате.');
    }

    /**
     * Отклонение расчета стоимости клиентом.
     */
    public function rejectQuote(Request $request, Order $order, Quote $quote, RejectQuoteAction $rejectQuoteAction): RedirectResponse
    {
        $user = Auth::user();
        $customer = $user->customer;

        if (!$customer || $order->customer_id !== $customer->id || $quote->order_id !== $order->id) {
            abort(403, 'У вас нет доступа к данному расчету.');
        }

        $reason = $request->input('reason', 'Отклонено клиентом в личном кабинете');
        $rejectQuoteAction->execute($quote, $reason);

        return redirect()->back()->with('success', 'Расчет стоимости отклонен. Менеджер свяжется с вами для уточнения деталей.');
    }
}