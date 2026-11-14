<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (!in_array(request()->getHost(), ['localhost', '127.0.0.1', 'case3-dkp.test'])) {
            URL::forceScheme('https');
        }
    }
}