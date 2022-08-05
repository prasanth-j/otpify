<?php

return [
    /**
     * The length of token.
     */
    'digits'    => env('OTPIFY_DIGITS', 6),

    /**
     * The expiry time of token in minutes.
     */
    'validity'  => env('OTPIFY_VALIDITY', 15)
];
