<?php

namespace PrasanthJ\Otpify\Events;

use Carbon\Carbon;

class OtpGenerated
{
    public function __construct(
        public readonly string $identifier,
        public readonly string $purpose,
        public readonly string $token,
        public readonly Carbon $expiresAt,
    ) {
    }
}
