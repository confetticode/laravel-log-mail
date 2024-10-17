<?php

namespace ConfettiCode\LaravelLog;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class LogMailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Log::extend('mail', function (Application $app, array $config) {
            $creator = new CreateMailLogger;

            return call_user_func($creator, $config);
        });
    }
}
