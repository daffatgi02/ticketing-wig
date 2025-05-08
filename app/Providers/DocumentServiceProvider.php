<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DocumentGenerator;

class DocumentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('document.generator', function ($app) {
            return new DocumentGenerator();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
