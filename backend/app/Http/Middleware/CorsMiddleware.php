<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Set headers untuk mengizinkan permintaan CORS
        $response->headers->set('Access-Control-Allow-Origin', '*'); // Ganti * dengan domain frontend jika spesifik
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

        // Jika method adalah OPTIONS, langsung kirim respons tanpa proses lebih lanjut
        if ($request->getMethod() === "OPTIONS") {
            $response->setStatusCode(200);
        }

        return $response;
    }
}
