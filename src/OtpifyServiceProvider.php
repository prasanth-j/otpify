<?php

namespace PrasanthJ\Otpify;

use Illuminate\Support\ServiceProvider;
use PrasanthJ\Otpify\Commands\CleanOtps;
use PrasanthJ\Otpify\Otpify;

class OtpifyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Otpify', Otpify::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanOtps::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../config/otpify.php' => config_path('otpify.php')
        ], 'otpify-config');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'otpify-migrations');
    }
}
