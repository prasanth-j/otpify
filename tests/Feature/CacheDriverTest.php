<?php

use PrasanthJ\Otpify\Facades\Otpify;

beforeEach(function () {
    config(['otpify.driver' => 'cache']);
});

it('generates and validates an otp using the cache driver', function () {
    $generated = Otpify::generate('user@example.com');

    $result = Otpify::validate('user@example.com', $generated->token);

    expect($result->isValid())->toBeTrue();
});

it('rejects an already used otp with the cache driver', function () {
    $generated = Otpify::generate('user@example.com');

    Otpify::validate('user@example.com', $generated->token);
    $result = Otpify::validate('user@example.com', $generated->token);

    expect($result->isAlreadyUsed())->toBeTrue();
});

it('invalidates an otp with the cache driver', function () {
    $generated = Otpify::generate('user@example.com');

    expect(Otpify::invalidate('user@example.com'))->toBeTrue();
    expect(Otpify::validate('user@example.com', $generated->token)->status)->toBe('not_found');
});
