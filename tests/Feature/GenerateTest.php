<?php

use PrasanthJ\Otpify\Exceptions\InvalidOtpTypeException;
use PrasanthJ\Otpify\Facades\Otpify;

it('generates an otp with the configured defaults', function () {
    $result = Otpify::generate('test@example.com');

    expect($result->wasGenerated())->toBeTrue()
        ->and($result->token)->not->toBeNull()
        ->and(strlen($result->token))->toBe(6)
        ->and($result->expiresAt)->not->toBeNull();
});

it('generates an otp with custom options', function () {
    $result = Otpify::generate('test@example.com', 'login', [
        'digits' => 4,
        'type' => 'alpha',
    ]);

    expect(strlen($result->token))->toBe(4)
        ->and(ctype_alpha($result->token))->toBeTrue();
});

it('throws for an invalid otp type', function () {
    Otpify::generate('test@example.com', 'default', ['type' => 'invalid']);
})->throws(InvalidOtpTypeException::class);

it('throws for digits outside the allowed range', function () {
    Otpify::generate('test@example.com', 'default', ['digits' => 12]);
})->throws(InvalidArgumentException::class);

it('deletes the previous otp for the same identifier and purpose on regenerate', function () {
    $first = Otpify::generate('test@example.com', 'login');
    $second = Otpify::generate('test@example.com', 'login');

    expect(Otpify::validate('test@example.com', $first->token, 'login')->status)->toBe('invalid');
    expect(Otpify::validate('test@example.com', $second->token, 'login')->status)->toBe('valid');
});
