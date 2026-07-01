<?php

namespace PrasanthJ\Otpify\Drivers;

use Carbon\Carbon;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use PrasanthJ\Otpify\Contracts\OtpDriver;

class CacheDriver implements OtpDriver
{
    public function generate(string $identifier, string $purpose, string $tokenHash, Carbon $expiresAt): void
    {
        $key = $this->key($identifier, $purpose);

        $this->store()->forget($key);

        $this->store()->put($key, [
            'token' => $tokenHash,
            'expires_at' => $expiresAt->toDateTimeString(),
            'used_at' => null,
        ], $expiresAt);
    }

    public function validate(string $identifier, string $purpose, string $token): string
    {
        $key = $this->key($identifier, $purpose);
        $record = $this->store()->get($key);

        if (!$record) {
            return 'not_found';
        }

        if ($record['used_at'] !== null) {
            return 'already_used';
        }

        $expiresAt = Carbon::parse($record['expires_at']);

        if ($expiresAt->isPast()) {
            return 'expired';
        }

        if (!hash_equals($record['token'], hash('sha256', $token))) {
            return 'invalid';
        }

        $record['used_at'] = Carbon::now()->toDateTimeString();

        $this->store()->put($key, $record, $expiresAt);

        return 'valid';
    }

    public function invalidate(string $identifier, string $purpose): bool
    {
        return $this->store()->forget($this->key($identifier, $purpose));
    }

    public function exists(string $identifier, string $purpose): bool
    {
        $record = $this->store()->get($this->key($identifier, $purpose));

        if (!$record) {
            return false;
        }

        return $record['used_at'] === null && !Carbon::parse($record['expires_at'])->isPast();
    }

    protected function store(): Repository
    {
        return Cache::store(config('otpify.cache.store'));
    }

    protected function key(string $identifier, string $purpose): string
    {
        return sprintf('%s:%s:%s', config('otpify.cache.prefix', 'otpify'), $identifier, $purpose);
    }
}
