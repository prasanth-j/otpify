<?php

namespace PrasanthJ\Otpify\Facades;

use Illuminate\Support\Facades\Facade;

class Otpify extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \PrasanthJ\Otpify\Otpify::class;
    }
}
