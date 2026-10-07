<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;

class ShipmentPolicy
{
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

    public function view(User $user, Shipment $shipment): bool
    {
        return $user->role === 'customer' 
            && $user->customer_id !== null 
            && $shipment->customer_id === $user->customer_id;
    }

    public function create(User $user): bool
    {
        return false; // Отправки формирует только менеджер
    }

    public function update(User $user, Shipment $shipment): bool
    {
        return false;
    }

    public function delete(User $user, Shipment $shipment): bool
    {
        return false;
    }
}