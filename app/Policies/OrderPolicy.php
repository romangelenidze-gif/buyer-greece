<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Администраторы и менеджеры имеют полный доступ ко всем заказам.
     */
    public function before(User $user, string $ability): ?bool
    {
        if (in_array($user->role, ['super_admin', 'manager', 'finance'])) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->role === 'customer';
    }

    public function view(User $user, Order $order): bool
    {
        return $user->role === 'customer' 
            && $user->customer_id !== null 
            && $order->customer_id === $user->customer_id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'customer' && $user->customer_id !== null;
    }

    public function update(User $user, Order $order): bool
    {
        return $user->role === 'customer' 
            && $user->customer_id !== null 
            && $order->customer_id === $user->customer_id 
            && in_array($order->status->value, ['draft', 'under_review']);
    }

    public function delete(User $user, Order $order): bool
    {
        return false; // Клиенты не могут удалять заказы
    }
}