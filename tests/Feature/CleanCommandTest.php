<?php

use Illuminate\Support\Facades\DB;

it('deletes expired and used otp tokens', function () {
    DB::table('otpify_tokens')->insert([
        [
            'identifier' => 'expired@example.com',
            'purpose' => 'default',
            'token' => hash('sha256', '111111'),
            'expires_at' => now()->subMinute(),
            'used_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'identifier' => 'used@example.com',
            'purpose' => 'default',
            'token' => hash('sha256', '222222'),
            'expires_at' => now()->addMinutes(10),
            'used_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'identifier' => 'active@example.com',
            'purpose' => 'default',
            'token' => hash('sha256', '333333'),
            'expires_at' => now()->addMinutes(10),
            'used_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    $this->artisan('otpify:clean')->assertExitCode(0);

    expect(DB::table('otpify_tokens')->count())->toBe(1);
    expect(DB::table('otpify_tokens')->first()->identifier)->toBe('active@example.com');
});

it('warns instead of deleting when the cache driver is active', function () {
    config(['otpify.driver' => 'cache']);

    $this->artisan('otpify:clean')->assertExitCode(0);
});
