<?php

use Illuminate\Support\Facades\Validator;
use PrasanthJ\Otpify\Facades\Otpify;
use PrasanthJ\Otpify\Rules\OtpRule;

it('passes validation for a correct otp', function () {
    $generated = Otpify::generate('user@example.com', 'login');

    $validator = Validator::make(
        ['otp' => $generated->token],
        ['otp' => [new OtpRule('user@example.com', 'login')]]
    );

    expect($validator->passes())->toBeTrue();
});

it('fails validation for an incorrect otp', function () {
    Otpify::generate('user@example.com', 'login');

    $validator = Validator::make(
        ['otp' => 'wrong-token'],
        ['otp' => [new OtpRule('user@example.com', 'login')]]
    );

    expect($validator->fails())->toBeTrue();
});
