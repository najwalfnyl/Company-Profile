<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use App\Notifications\ResetPasswordNotification;

class Admin extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable, CanResetPassword;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'phone',
        'photo',
        'reset_password_token',
        'reset_password_expires'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function generatePasswordResetToken()
    {
        $this->reset_password_token = Str::random(60);
        $this->reset_password_expires = Carbon::now('Asia/Jakarta')->addMinutes(15); // Kadaluarsa dalam 15 menit (Waktu Jakarta)
        $this->save();
    
        // Kirim notifikasi reset password
        $this->sendPasswordResetNotification($this->reset_password_token);
    }
    
    
    public function isPasswordResetTokenValid($token)
    {
        $resetToken = $this->reset_password_token;
        $expiresAt = $this->reset_password_expires;
    
        // Menggunakan zona waktu Jakarta (WIB)
        $nowInJakarta = Carbon::now('Asia/Jakarta');
    
        if ($resetToken === $token && $nowInJakarta->lessThanOrEqualTo($expiresAt)) {
            return true;
        }
    
        return false;
    }

    

    // Fungsi untuk menghapus token reset password setelah digunakan
    public function clearPasswordResetToken()
    {
        $this->reset_password_token = null;
        $this->reset_password_expires = null;
        $this->save();
    }

 // Fungsi untuk mengirim notifikasi reset password
 public function sendPasswordResetNotification($token)
 {
     // Ambil URL frontend dari konfigurasi .env
     $frontendUrl = config('cors.allowed_origins')[0];
     $resetLink = "{$frontendUrl}/reset-password?token={$token}&email={$this->email}";
     $this->notify(new ResetPasswordNotification($resetLink));
 }
}
