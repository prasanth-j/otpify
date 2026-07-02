<?php

namespace PrasanthJ\Otpify\Rules;

use Illuminate\Contracts\Validation\Rule;
use PrasanthJ\Otpify\Facades\Otpify;

// Uses the deprecated Rule contract (not ValidationRule) because ValidationRule
// doesn't exist in Laravel 9, which this package still supports.
class OtpRule implements Rule
{
    protected string $message = 'The :attribute is invalid.';

    public function __construct(
        protected string $identifier,
        protected string $purpose = 'default',
    ) {}

    public function passes($attribute, $value): bool
    {
        $result = Otpify::validate($this->identifier, (string) $value, $this->purpose);

        $this->message = $result->message;

        return $result->isValid();
    }

    public function message(): string
    {
        return $this->message;
    }
}
