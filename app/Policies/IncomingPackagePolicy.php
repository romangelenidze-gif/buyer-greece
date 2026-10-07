<?php

namespace App\Policies;

use App\Models\IncomingPackage;
use App\Models\User;

class IncomingPackagePolicy
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

    public function view(User $user, IncomingPackage $incomingPackage): bool
    {
        return $user->role === 'customer' 
            && $user->customer_id !== null 
            && $incomingPackage->customer_id === $user->customer_id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'customer' && $user->customer_id !== null;
    }

    public function update(User $user, IncomingPackage $incomingPackage): bool
    {
        return false; // Клиенты не редактируют входящие посылки напрямую
    }

    public function delete(User $user, IncomingPackage $incomingPackage): bool
    {
        return false;
    }
}