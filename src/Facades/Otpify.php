<?php

namespace PrasanthJ\Otpify\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array generate(string $identifier, int $userId = null, string $otpType = null, int $digits = null, int $validity = null)
 * @method static array validate(string $identifier, string $token, string $otpType = null)
 */
class Otpify extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Otpify';
    }
}
