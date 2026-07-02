<?php

namespace PrasanthJ\Otpify;

use Illuminate\Support\ServiceProvider;
use PrasanthJ\Otpify\Console\CleanCommand;
use PrasanthJ\Otpify\Contracts\OtpDriver;
use PrasanthJ\Otpify\Drivers\CacheDriver;
use PrasanthJ\Otpify\Drivers\DatabaseDriver;
use PrasanthJ\Otpify\Exceptions\InvalidOtpDriverException;

class OtpifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/otpify.php', 'otpify');

        $this->app->bind(DatabaseDriver::class);
        $this->app->bind(CacheDriver::class);

        $this->app->bind('otpify.driver', function ($app) {
            $driver = config('otpify.driver', 'database');

            return match ($driver) {
                'database' => $app->make(DatabaseDriver::class),
                'cache' => $app->make(CacheDriver::class),
                default => throw new InvalidOtpDriverException(
                    "Invalid Otpify driver [{$driver}]. Allowed drivers: database, cache."
                ),
            };
        });

        $this->app->bind(OtpDriver::class, fn ($app) => $app->make('otpify.driver'));

        $this->app->singleton('otpify', fn () => new Otpify);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/otpify.php' => config_path('otpify.php'),
            ], 'otpify-config');

            $this->publishes([
                __DIR__.'/../database/migrations/create_otpify_tokens_table.php' => database_path(
                    'migrations/'.date('Y_m_d_His').'_create_otpify_tokens_table.php'
                ),
            ], 'otpify-migrations');
        }
    }
}
