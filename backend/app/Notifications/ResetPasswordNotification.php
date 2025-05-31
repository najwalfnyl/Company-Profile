<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    protected $resetLink;

    public function __construct($resetLink)
    {
        $this->resetLink = $resetLink;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Cek apakah token sudah kadaluarsa
        if (Carbon::now('Asia/Jakarta')->greaterThan($notifiable->reset_password_expires)) {
            return (new MailMessage)
                ->subject('Reset Password')
                ->line('Token reset password Anda sudah kadaluarsa. Silakan minta token baru.')
                ->line('Jika Anda tidak meminta reset password, abaikan email ini.');
        }

        // Jika token masih berlaku, kirimkan email untuk reset password
        return (new MailMessage)
            ->subject('Reset Password')
            ->line('Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.')
            ->action('Reset Password', $this->resetLink)
            ->line('Jika Anda tidak meminta reset password, abaikan email ini.');
    }
}
