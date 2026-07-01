<?php

use PrasanthJ\Otpify\Facades\Otpify;

it('validates a correct otp', function () {
    $generated = Otpify::generate('user@example.com');

    $result = Otpify::validate('user@example.com', $generated->token);

    expect($result->isValid())->toBeTrue()
        ->and($result->token)->toBeNull();
});

it('rejects an incorrect otp', function () {
    Otpify::generate('user@example.com');

    $result = Otpify::validate('user@example.com', '000000');

    expect($result->isInvalid())->toBeTrue();
});

it('rejects an otp that does not exist', function () {
    $result = Otpify::validate('missing@example.com', '123456');

    expect($result->status)->toBe('not_found');
});

it('rejects an expired otp', function () {
    $generated = Otpify::generate('user@example.com', 'default', ['validity' => -1]);

    $result = Otpify::validate('user@example.com', $generated->token);

    expect($result->isExpired())->toBeTrue();
});

it('rejects an otp that was already used', function () {
    $generated = Otpify::generate('user@example.com');

    Otpify::validate('user@example.com', $generated->token);
    $result = Otpify::validate('user@example.com', $generated->token);

    expect($result->isAlreadyUsed())->toBeTrue();
});
