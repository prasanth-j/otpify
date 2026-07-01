<?php

namespace PrasanthJ\Otpify;

use Carbon\Carbon;

class OtpResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $token,
        public readonly ?Carbon $expiresAt,
        public readonly string $message,
        public readonly string $status,
    ) {
    }

    public static function generated(string $token, Carbon $expiresAt, string $message = 'OTP generated successfully.'): self
    {
        return new self(true, $token, $expiresAt, $message, 'generated');
    }

    public static function valid(string $message = 'OTP is valid.'): self
    {
        return new self(true, null, null, $message, 'valid');
    }

    public static function invalid(string $message = 'OTP is invalid.'): self
    {
        return new self(false, null, null, $message, 'invalid');
    }

    public static function expired(string $message = 'OTP has expired.'): self
    {
        return new self(false, null, null, $message, 'expired');
    }

    public static function alreadyUsed(string $message = 'OTP has already been used.'): self
    {
        return new self(false, null, null, $message, 'already_used');
    }

    public static function notFound(string $message = 'OTP not found.'): self
    {
        return new self(false, null, null, $message, 'not_found');
    }

    public function isValid(): bool
    {
        return $this->status === 'valid';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    public function isInvalid(): bool
    {
        return $this->status === 'invalid';
    }

    public function isAlreadyUsed(): bool
    {
        return $this->status === 'already_used';
    }

    public function wasGenerated(): bool
    {
        return $this->status === 'generated';
    }
}
