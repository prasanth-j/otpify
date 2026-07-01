<?php

namespace PrasanthJ\Otpify\Facades;

use Illuminate\Support\Facades\Facade;
use PrasanthJ\Otpify\OtpResult;

/**
 * @method static OtpResult generate(string $identifier, string $purpose = 'default', array $options = [])
 * @method static OtpResult validate(string $identifier, string $token, string $purpose = 'default')
 * @method static bool invalidate(string $identifier, string $purpose = 'default')
 * @method static OtpResult resend(string $identifier, string $purpose = 'default', array $options = [])
 *
 * @see \PrasanthJ\Otpify\Otpify
 */
class Otpify extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'otpify';
    }
}
