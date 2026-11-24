<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class QueuedResetPassword extends ResetPassword implements ShouldQueue, ShouldBeEncrypted
{
    use Queueable;

    public int $tries = 5;

    public array $backoff = [60, 300, 900, 1800];

    public function __construct($token)
    {
        parent::__construct($token);
        $this->onConnection('database');
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset password ' . config('brand.current.name'))
            ->greeting('Halo, ' . $notifiable->name . '.')
            ->line('Kami menerima permintaan untuk mengubah password akun Anda.')
            ->action('Atur ulang password', $url)
            ->line('Tautan ini berlaku selama ' . config('auth.passwords.users.expire') . ' menit.')
            ->line('Jika Anda tidak meminta perubahan password, abaikan email ini.');
    }
}
