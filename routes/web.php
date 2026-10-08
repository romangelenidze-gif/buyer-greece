<?php

use App\Http\Controllers\OrderMediaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Публичные маршруты
|--------------------------------------------------------------------------
*/
Route::view('/', 'public.home')->name('home');
Route::view('/how-it-works', 'public.how-it-works')->name('how-it-works');
Route::view('/buy-for-me', 'public.buy-for-me')->name('buy-for-me');
Route::view('/forwarding', 'public.forwarding')->name('forwarding');
Route::view('/help', 'public.help')->name('help');

// Заглушка отправки формы «Выкупите за меня»
Route::post('/orders', function () {
    return redirect()->back()->with('success', 'Заявка принята');
})->name('orders.store');

// Временные заглушки авторизации для гостей
Route::get('/login', function () {
    return redirect()->route('home');
})->name('login');

Route::get('/register', function () {
    return redirect()->route('home');
})->name('register');


// Страницы входа / регистрации
Route::view('/login', 'auth.login')->name('login');

Route::middleware(['auth', 'verified'])->group(function () {
    // ... предыдущие маршруты ...
    
    // Посылки и Отправки
    Route::get('/packages', function () { 
        return view('app.packages.index'); 
    })->name('packages.index');

    Route::get('/shipments', function () { 
        return view('app.shipments.index'); 
    })->name('shipments.index');
});

/*
|--------------------------------------------------------------------------
| Личный кабинет пользователя (Защищенная зона)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Дашборд
    Route::get('/dashboard', function () {
        return view('app.dashboard', [
            'stats' => [
                'expected_packages' => 0,
                'received_packages' => 0,
                'ready_shipments' => 0,
            ],
            'activeOrders' => [
                (object)[
                    'id' => 1,
                    'order_number' => 'BG-1001',
                    'status' => 'quote_ready',
                    'status_label' => 'Расчет готов',
                    'product_name' => 'Zara Wool Coat (Winter Collection)',
                    'created_at' => now(),
                ]
            ],
        ]);
    })->name('dashboard');

    // Профиль пользователя
    Route::get('/profile', function () {
        return view('app.profile');
    })->name('profile.show');

    Route::post('/profile', function () {
        return redirect()->back()->with('success', 'Профиль и код Camex успешно сохранены!');
    })->name('profile.update');

    // Заказы
    Route::get('/orders', function () {
        return redirect()->route('dashboard');
    })->name('orders.index');

    Route::get('/orders/{order}', function ($order) {
        return view('app.orders.show', [
            'order' => (object)[
                'id' => $order,
                'order_number' => 'BG-1001',
                'status' => 'quote_ready',
                'status_label' => 'Расчет готов',
                'product_name' => 'Zara Wool Coat (Winter Collection)',
                'product_url' => 'https://www.zara.com/gr/en/wool-coat-p01234567.html',
                'quantity' => 1,
                'size' => 'M',
                'color' => 'Black',
                'item_price' => 89.90,
                'local_shipping' => 4.50,
                'service_fee' => 10.00,
                'total_amount' => 104.40,
                'created_at' => now(),
            ]
        ]);
    })->name('orders.show');

    // Загрузка чека оплаты заказа
    Route::post('/orders/{order}/payments', function ($order) {
        return redirect()->back()->with('success', 'Чек успешно загружен! Менеджер проверяет поступление средств.');
    })->name('orders.payments.store');

    // Посылки и Отправки
    Route::get('/packages', function () { return view('app.dashboard'); })->name('packages.index');
    Route::get('/shipments', function () { return view('app.dashboard'); })->name('shipments.index');

    // Медиафайлы бэкенда
    Route::get('/orders/{order}/payments/{payment}/receipt', [OrderMediaController::class, 'downloadReceipt'])
        ->name('orders.payments.receipt');

    Route::get('/packages/{package}/photo', [OrderMediaController::class, 'downloadPackagePhoto'])
        ->name('packages.photo');

    // Выход
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('home');
    })->name('logout');
});