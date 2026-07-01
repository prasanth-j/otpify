<?php

namespace PrasanthJ\Otpify\Drivers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PrasanthJ\Otpify\Contracts\OtpDriver;

class DatabaseDriver implements OtpDriver
{
    protected string $table = 'otpify_tokens';

    public function generate(string $identifier, string $purpose, string $tokenHash, Carbon $expiresAt): void
    {
        DB::table($this->table)
            ->where('identifier', $identifier)
            ->where('purpose', $purpose)
            ->delete();

        DB::table($this->table)->insert([
            'identifier' => $identifier,
            'purpose' => $purpose,
            'token' => $tokenHash,
            'expires_at' => $expiresAt,
            'used_at' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public function validate(string $identifier, string $purpose, string $token): string
    {
        $record = DB::table($this->table)
            ->where('identifier', $identifier)
            ->where('purpose', $purpose)
            ->first();

        if (!$record) {
            return 'not_found';
        }

        if ($record->used_at !== null) {
            return 'already_used';
        }

        if (Carbon::parse($record->expires_at)->isPast()) {
            return 'expired';
        }

        if (!hash_equals($record->token, hash('sha256', $token))) {
            return 'invalid';
        }

        DB::table($this->table)
            ->where('id', $record->id)
            ->update([
                'used_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        return 'valid';
    }

    public function invalidate(string $identifier, string $purpose): bool
    {
        return DB::table($this->table)
            ->where('identifier', $identifier)
            ->where('purpose', $purpose)
            ->delete() > 0;
    }

    public function exists(string $identifier, string $purpose): bool
    {
        return DB::table($this->table)
            ->where('identifier', $identifier)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->where('expires_at', '>', Carbon::now())
            ->exists();
    }
}
