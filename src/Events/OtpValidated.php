<?php

namespace PrasanthJ\Otpify\Events;

class OtpValidated
{
    public function __construct(
        public readonly string $identifier,
        public readonly string $purpose,
    ) {
    }
}
