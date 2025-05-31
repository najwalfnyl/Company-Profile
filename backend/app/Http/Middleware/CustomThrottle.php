<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\RateLimiter;

class CustomThrottle
{
    public function handle($request, Closure $next)
    {
        // Sesuaikan rate limit berdasarkan environment
        $limit = app()->environment('local') ? '1000,1' : '60,1';

        // Terapkan rate limit ke route yang digunakan
        RateLimiter::for('api', function () use ($limit) {
            return Limit::perMinute(explode(',', $limit)[0]);
        });

        return $next($request);
    }
}
