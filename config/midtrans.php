<?php
return [
    'server_key'           => env('MIDTRANS_SERVER_KEY'),
    'client_key'           => env('MIDTRANS_CLIENT_KEY'),
    'is_production'        => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized'         => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds'               => env('MIDTRANS_IS_3DS', true),

    // Tambahan
    'finish_redirect_url'   => env('MIDTRANS_FINISH_URL', 'http://127.0.0.1:8000/dashboard/transaksi-spp/finish'),
    'unfinish_redirect_url' => env('MIDTRANS_UNFINISH_URL', 'http://127.0.0.1:8000/dashboard/transaksi-spp/unfinish'),
    'error_redirect_url'    => env('MIDTRANS_ERROR_URL', 'http://127.0.0.1:8000/dashboard/transaksi-spp/error'),

];