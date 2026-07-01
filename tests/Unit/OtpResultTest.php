<?php

use PrasanthJ\Otpify\OtpResult;

it('reports helper states correctly', function () {
    expect(OtpResult::valid()->isValid())->toBeTrue();
    expect(OtpResult::expired()->isExpired())->toBeTrue();
    expect(OtpResult::invalid()->isInvalid())->toBeTrue();
    expect(OtpResult::alreadyUsed()->isAlreadyUsed())->toBeTrue();
    expect(OtpResult::notFound()->status)->toBe('not_found');
    expect(OtpResult::generated('123456', now()->addMinutes(10))->wasGenerated())->toBeTrue();
});
