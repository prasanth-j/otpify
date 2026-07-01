<?php

namespace PrasanthJ\Otpify;

use Carbon\Carbon;
use InvalidArgumentException;
use PrasanthJ\Otpify\Contracts\OtpDriver;
use PrasanthJ\Otpify\Events\OtpFailed;
use PrasanthJ\Otpify\Events\OtpGenerated;
use PrasanthJ\Otpify\Events\OtpValidated;
use PrasanthJ\Otpify\Exceptions\InvalidOtpTypeException;

class Otpify
{
    protected const TYPES = ['numeric', 'alpha', 'alphanumeric'];

    protected const CHARSETS = [
        'numeric' => '0123456789',
        'alpha' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'alphanumeric' => '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
    ];

    public function generate(string $identifier, string $purpose = 'default', array $options = []): OtpResult
    {
        $digits = $options['digits'] ?? config('otpify.digits', 6);
        $validity = $options['validity'] ?? config('otpify.validity', 10);
        $type = $options['type'] ?? config('otpify.type', 'numeric');

        if ($digits < 4 || $digits > 8) {
            throw new InvalidArgumentException('OTP digits must be between 4 and 8.');
        }

        if (! in_array($type, self::TYPES, true)) {
            throw new InvalidOtpTypeException(
                "Invalid OTP type [{$type}]. Allowed types: ".implode(', ', self::TYPES).'.'
            );
        }

        $token = $this->generateToken($type, $digits);
        $expiresAt = Carbon::now()->addMinutes($validity);

        $this->driver()->generate($identifier, $purpose, $this->hash($token), $expiresAt);

        $result = OtpResult::generated($token, $expiresAt);

        event(new OtpGenerated($identifier, $purpose, $token, $expiresAt));

        return $result;
    }

    public function validate(string $identifier, string $token, string $purpose = 'default'): OtpResult
    {
        $status = $this->driver()->validate($identifier, $purpose, $token);

        $result = match ($status) {
            'valid' => OtpResult::valid(),
            'expired' => OtpResult::expired(),
            'already_used' => OtpResult::alreadyUsed(),
            'not_found' => OtpResult::notFound(),
            default => OtpResult::invalid(),
        };

        if ($result->isValid()) {
            event(new OtpValidated($identifier, $purpose));
        } else {
            event(new OtpFailed($identifier, $purpose, $result->status));
        }

        return $result;
    }

    public function invalidate(string $identifier, string $purpose = 'default'): bool
    {
        return $this->driver()->invalidate($identifier, $purpose);
    }

    public function resend(string $identifier, string $purpose = 'default', array $options = []): OtpResult
    {
        $this->invalidate($identifier, $purpose);

        return $this->generate($identifier, $purpose, $options);
    }

    protected function generateToken(string $type, int $digits): string
    {
        $charset = self::CHARSETS[$type];
        $max = strlen($charset) - 1;

        $token = '';

        for ($i = 0; $i < $digits; $i++) {
            $token .= $charset[random_int(0, $max)];
        }

        return $token;
    }

    protected function hash(string $token): string
    {
        return hash('sha256', $token);
    }

    protected function driver(): OtpDriver
    {
        return app('otpify.driver');
    }
}
