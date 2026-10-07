<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        // Суперадмин имеет доступ к любым панелям
        if ($this->role === 'super_admin') {
            return true;
        }

        if ($panel->getId() === 'admin') {
            return in_array($this->role, ['manager', 'finance']);
        }

        if ($panel->getId() === 'customer') {
            return $this->role === 'customer';
        }

        return false;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}