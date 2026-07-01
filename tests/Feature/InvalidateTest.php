<?php

use PrasanthJ\Otpify\Facades\Otpify;

it('invalidates an otp so it can no longer be validated', function () {
    $generated = Otpify::generate('user@example.com');

    expect(Otpify::invalidate('user@example.com'))->toBeTrue();

    $result = Otpify::validate('user@example.com', $generated->token);

    expect($result->status)->toBe('not_found');
});

it('returns false when invalidating a non-existent otp', function () {
    expect(Otpify::invalidate('nobody@example.com'))->toBeFalse();
});
