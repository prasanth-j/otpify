<?php

namespace PrasanthJ\Otpify\Contracts;

use Carbon\Carbon;

interface OtpDriver
{
    /**
     * Store a hashed OTP for the given identifier and purpose, replacing any
     * previous OTP stored for the same identifier and purpose.
     */
    public function generate(string $identifier, string $purpose, string $tokenHash, Carbon $expiresAt): void;

    /**
     * Validate a plaintext token against the stored OTP and return one of:
     * "valid", "invalid", "expired", "already_used", "not_found".
     *
     * A "valid" result marks the OTP as used so it cannot be validated again.
     */
    public function validate(string $identifier, string $purpose, string $token): string;

    /**
     * Remove the stored OTP for the given identifier and purpose.
     * Returns true if an OTP was removed.
     */
    public function invalidate(string $identifier, string $purpose): bool;

    /**
     * Determine if an active (unused, unexpired) OTP exists for the given
     * identifier and purpose.
     */
    public function exists(string $identifier, string $purpose): bool;
}
