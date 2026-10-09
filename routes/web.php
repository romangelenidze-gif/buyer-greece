<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderMediaController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ShipmentController;
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

/*
|--------------------------------------------------------------------------
| Гостевые маршруты авторизации
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Личный кабинет пользователя (Защищенная зона)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Создание заявки «Выкупите за меня»
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

    // Дашборд и просмотр заказов
    Route::get('/dashboard', [OrderController::class, 'index'])->name('dashboard');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Принятие и отклонение расчета (Quote)
    Route::post('/orders/{order}/quotes/{quote}/accept', [OrderController::class, 'acceptQuote'])->name('orders.quotes.accept');
    Route::post('/orders/{order}/quotes/{quote}/reject', [OrderController::class, 'rejectQuote'])->name('orders.quotes.reject');

    // Профиль пользователя и код Camex
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Загрузка чека оплаты заказа
    Route::post('/orders/{order}/payments', [PaymentController::class, 'store'])->name('orders.payments.store');

    // Посылки и Отправки Camex
    Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');

    Route::get('/shipments', function () {
        return view('app.shipments.index');
    })->name('shipments.index');

    // Безопасное скачивание медиафайлов (через Policies)
    Route::get('/orders/{order}/payments/{payment}/receipt', [OrderMediaController::class, 'downloadReceipt'])
        ->name('orders.payments.receipt');

    Route::get('/packages/{package}/photo', [OrderMediaController::class, 'downloadPackagePhoto'])
        ->name('packages.photo');

    // Выход из системы
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('home');
    })->name('logout');
});