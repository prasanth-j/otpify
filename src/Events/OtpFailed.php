<?php

namespace PrasanthJ\Otpify\Events;

class OtpFailed
{
    public function __construct(
        public readonly string $identifier,
        public readonly string $purpose,
        public readonly string $reason,
    ) {}
}
