<?php

namespace PrasanthJ\Otpify\Commands;

use Illuminate\Console\Command;
use PrasanthJ\Otpify\Models\Otp;

class CleanOtps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otpify:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean all verified tokens from the Otps table.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $otps = Otp::where('verified', true);

        $this->info("Found " . $otps->count() . " verified tokens.");

        $otps->delete();

        $this->info("Verified tokens deleted successfully.");
    }
}
