<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Otpify Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default driver that will be used to store and
    | retrieve OTPs. Otpify ships with two drivers out of the box: "database"
    | and "cache". The database driver is durable and works with the
    | otpify:clean command; the cache driver relies on your cache store's
    | TTL to expire OTPs automatically.
    |
    | Supported: "database", "cache"
    |
    */

    'driver' => env('OTPIFY_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Default OTP Length
    |--------------------------------------------------------------------------
    |
    | This value determines how many characters a generated OTP will contain
    | by default. It can be overridden per call via the "digits" option
    | passed to Otpify::generate(). Must be between 4 and 8.
    |
    */

    'digits' => env('OTPIFY_DIGITS', 6),

    /*
    |--------------------------------------------------------------------------
    | Default OTP Validity
    |--------------------------------------------------------------------------
    |
    | This value determines, in minutes, how long a generated OTP remains
    | valid before it expires. It can be overridden per call via the
    | "validity" option passed to Otpify::generate().
    |
    */

    'validity' => env('OTPIFY_VALIDITY', 10), // minutes

    /*
    |--------------------------------------------------------------------------
    | Default OTP Type
    |--------------------------------------------------------------------------
    |
    | This value determines the default character set used to generate an
    | OTP. It can be overridden per call via the "type" option passed to
    | Otpify::generate().
    |
    | Supported: "numeric", "alpha", "alphanumeric"
    |
    */

    'type' => env('OTPIFY_TYPE', 'numeric'),

    /*
    |--------------------------------------------------------------------------
    | Cache Driver Options
    |--------------------------------------------------------------------------
    |
    | These options are only used when the "cache" driver is active. The
    | prefix is used to namespace Otpify's cache keys, which are built as
    | "{prefix}:{identifier}:{purpose}". The store option controls which of
    | your configured cache stores (see config/cache.php) Otpify writes to;
    | leave it as null to use your application's default cache store.
    |
    */

    'cache' => [
        'prefix' => 'otpify',
        'store' => env('OTPIFY_CACHE_STORE', null),
    ],

];
