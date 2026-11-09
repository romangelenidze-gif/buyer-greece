<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use App\Traits\Auditable;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory, Auditable, Notifiable;

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

    /**
     * Отправка кастомного письма верификации в Apple-стиле.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new class extends VerifyEmailNotification {
            public function toMail($notifiable)
            {
                $verificationUrl = $this->verificationUrl($notifiable);

                return (new MailMessage)
                    ->subject(__('auth.verify_subject'))
                    ->markdown('emails.auth.verify', ['url' => $verificationUrl, 'user' => $notifiable]);
            }
        });
    }

    /**
     * Отправка кастомного письма сброса пароля в Apple-стиле.
     */
    public function sendPasswordResetNotification($token)
    {
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ], false));

        $this->notify(new class($resetUrl) extends ResetPasswordNotification {
            public $resetUrl;

            public function __construct($resetUrl)
            {
                $this->resetUrl = $resetUrl;
            }

            public function toMail($notifiable)
            {
                return (new MailMessage)
                    ->subject(__('auth.reset_subject'))
                    ->markdown('emails.auth.reset', ['url' => $this->resetUrl, 'user' => $notifiable]);
            }
        });
    }
}