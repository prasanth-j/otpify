<?php

use Illuminate\Support\Facades\Event;
use PrasanthJ\Otpify\Events\OtpFailed;
use PrasanthJ\Otpify\Events\OtpGenerated;
use PrasanthJ\Otpify\Events\OtpValidated;
use PrasanthJ\Otpify\Facades\Otpify;

it('dispatches OtpGenerated on generate', function () {
    Event::fake();

    Otpify::generate('user@example.com');

    Event::assertDispatched(OtpGenerated::class);
});

it('dispatches OtpValidated on successful validation', function () {
    $generated = Otpify::generate('user@example.com');

    Event::fake();

    Otpify::validate('user@example.com', $generated->token);

    Event::assertDispatched(OtpValidated::class);
});

it('dispatches OtpFailed on failed validation', function () {
    Otpify::generate('user@example.com');

    Event::fake();

    Otpify::validate('user@example.com', 'wrong');

    Event::assertDispatched(OtpFailed::class);
});
