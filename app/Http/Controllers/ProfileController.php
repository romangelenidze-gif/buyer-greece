<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Отображение страницы профиля пользователя.
     */
    public function show(): View
    {
        $user = Auth::user();
        $customer = $user->customer;

        return view('app.profile', compact('user', 'customer'));
    }

    /**
     * Обновление данных профиля и кода Camex.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'camex_personal_number' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();
        $customer = $user->customer;

        // Если у пользователя по какой-то причине отсутствует связанная запись Customer — создаем ее
        if (!$customer) {
            $customer = Customer::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $user->email,
                'phone' => $validated['phone'],
                'camex_personal_number' => $validated['camex_personal_number'] ?? null,
                'city' => $validated['city'] ?? null,
                'address' => $validated['address'] ?? null,
                'status' => 'active',
            ]);

            $user->customer_id = $customer->id;
        } else {
            $customer->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'],
                'camex_personal_number' => $validated['camex_personal_number'] ?? null,
                'city' => $validated['city'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);
        }

        // Синхронизируем имя в модели User
        $user->name = trim($validated['first_name'] . ' ' . $validated['last_name']);
        $user->save();

        return redirect()->back()->with('success', 'Профиль и данные доставки успешно сохранены!');
    }
}