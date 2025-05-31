<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        // Pastikan pengguna yang login adalah admin
        if (!Auth::guard('sanctum')->check()) {
            return redirect()->route('login');
        }

        // Cek waktu aktivitas terakhir di sesi
        $lastActivity = session('last_activity');
        if ($lastActivity && Carbon::now()->diffInSeconds($lastActivity) > 60) {
            Auth::guard('sanctum')->logout(); // Logout otomatis setelah 1 menit inaktivitas
            return redirect()->route('login')->with('message', 'Session expired. Please log in again.');
        }

        // Update waktu aktivitas terakhir
        session(['last_activity' => Carbon::now()]);

        return $next($request);
    }
}
