<?php

namespace App\Http\Controllers;

use App\Actions\Orders\CreateOrderAction;
use App\Actions\Quotes\AcceptQuoteAction;
use App\Actions\Quotes\RejectQuoteAction;
use App\Enums\OrderType;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Quote;
use App\Models\ShowcaseProduct;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Отображение страницы/формы «Выкупите за меня» с автозаполнением из витрины или поиска.
     */
    public function create(Request $request): View
    {
        $showcaseProduct = null;

        if ($request->filled('showcase_product_id')) {
            $showcaseProduct = ShowcaseProduct::find($request->input('showcase_product_id'));
        }

        // Автозаполнение из параметров URL (из поиска SmartProductSearchManager)
        $prefilled = [
            'url' => $request->query('url'),
            'name' => $request->query('name'),
            'price' => $request->query('price'),
        ];

        return view('public.buy-for-me', compact('showcaseProduct', 'prefilled'));
    }

    /**
     * Обработка и сохранение формы «Выкупите за меня».
     */
    public function store(Request $request, CreateOrderAction $createOrderAction): RedirectResponse
    {
        $rules = [
            'product_url' => ['required', 'url', 'max:2048'],
            'product_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'size' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'model' => ['nullable', 'string', 'max:100'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'screenshot' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'showcase_product_id' => ['nullable', 'exists:showcase_products,id'],
        ];

        if (!Auth::check()) {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['email'] = ['required', 'email', 'max:255'];
            $rules['phone'] = ['nullable', 'string', 'max:50'];
        }

        $validated = $request->validate($rules);

        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::firstOrCreate(
                ['email' => strtolower(trim($validated['email']))],
                [
                    'name' => trim($validated['name']),
                    'password' => Hash::make(Str::random(16)),
                ]
            );

            Auth::login($user, remember: true);
        }

        $customer = $user->customer;

        if (!$customer) {
            $fullName = $validated['name'] ?? $user->name ?? 'Пользователь';
            $nameParts = explode(' ', trim($fullName), 2);
            $firstName = $nameParts[0] ?? 'Пользователь';
            $lastName = $nameParts[1] ?? '';

            $customer = Customer::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $user->email,
                'phone' => $validated['phone'] ?? '',
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
                'unit_price' => $validated['unit_price'] ?? null,
                'size' => $validated['size'] ?? null,
                'color' => $validated['color'] ?? null,
                'model' => $validated['model'] ?? null,
                'requested_comment' => $validated['comment'] ?? null,
                'screenshot_path' => $screenshotPath,
                'showcase_product_id' => $validated['showcase_product_id'] ?? null,
            ]
        ];

        $order = $createOrderAction->execute(
            customer: $customer,
            type: OrderType::BUY_FOR_ME,
            itemsData: $itemsData,
            internalNote: $validated['comment'] ?? null
        );

        return redirect()->route('orders.show', $order->id)->with([
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