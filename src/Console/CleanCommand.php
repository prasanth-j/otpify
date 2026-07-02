<?php

namespace PrasanthJ\Otpify\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanCommand extends Command
{
    protected $signature = 'otpify:clean';

    protected $description = 'Delete expired and used OTP tokens (database driver only).';

    public function handle(): int
    {
        if (config('otpify.driver') !== 'database') {
            $this->warn('Otpify is not using the database driver. Nothing to clean.');

            return self::SUCCESS;
        }

        $deleted = DB::table('otpify_tokens')
            ->where(function ($query) {
                $query->whereNotNull('used_at')
                    ->orWhere('expires_at', '<', now());
            })
            ->delete();

        $this->info("Deleted {$deleted} expired/used OTP token(s).");

        return self::SUCCESS;
    }
}
