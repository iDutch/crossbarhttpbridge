<?php

namespace iDutch\CrossbarHttpBridge;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\ServiceProvider;

class CrossbarHttpBridgeProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('iDutch\CrossbarHttpBridge\CrossbarHttpBridgeInterface', function ($app) {
            return new CrossbarHttpBridgeClient(new GuzzleClient(config('app.crossbar_http_bridge.guzzle')), config('app.crossbar_http_bridge.options'));
        });
    }
}
