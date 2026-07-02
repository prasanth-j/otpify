<?php

use PrasanthJ\Otpify\Facades\Otpify;

it('resend invalidates the previous otp and generates a new one', function () {
    $first = Otpify::generate('user@example.com');
    $second = Otpify::resend('user@example.com');

    expect(Otpify::validate('user@example.com', $first->token)->status)->toBe('invalid');
    expect(Otpify::validate('user@example.com', $second->token)->status)->toBe('valid');
});
