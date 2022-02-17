<?php

namespace Artisanssoft\Coingate;

use Illuminate\Support\ServiceProvider;


class CoingateServiceProvider extends ServiceProvider 
{

    
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
 
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('coingate', function ($app) {
            return new Coingate($app['request']->server());
        });

        $this->app->alias('coingate', Coingate::class);
    }

}
