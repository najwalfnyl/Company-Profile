<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'storage/*'], // Sesuaikan jalur API yang diakses

    'allowed_methods' => ['*'], // Bisa menggunakan metode spesifik seperti ['GET', 'POST', 'PUT', 'DELETE']

    'allowed_origins' => ['http://localhost:3000'], // Pastikan domain frontend sudah benar

    'allowed_origins_patterns' => [], // Jika menggunakan pola regex, tambahkan di sini

    'allowed_headers' => ['*'], // Dapat disesuaikan, misalnya ['Content-Type', 'X-Requested-With']

    'exposed_headers' => [], // Header yang boleh diekspos ke frontend

    'max_age' => 0, // Atur durasi cache preflight request (dalam detik)

    'supports_credentials' => true, // Set ke true untuk mengizinkan cookies/token dikirim
];
