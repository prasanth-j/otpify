<?php

return [
    'driver'   => env('OTPIFY_DRIVER', 'database'),
    'digits'   => env('OTPIFY_DIGITS', 6),
    'validity' => env('OTPIFY_VALIDITY', 10), // minutes
    'type'     => env('OTPIFY_TYPE', 'numeric'), // numeric|alpha|alphanumeric
    'cache'    => [
        'prefix' => 'otpify',
        'store'  => env('OTPIFY_CACHE_STORE', null), // null = default cache store
    ],
];
