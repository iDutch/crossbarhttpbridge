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
        $this->publishes([
            __DIR__ . '/config/crossbarhttpbridge.php' => config_path('crossbarhttpbridge.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('iDutch\CrossbarHttpBridge\CrossbarHttpBridgeInterface', function ($app) {
            return new CrossbarHttpBridgeClient(new GuzzleClient(config('crossbarhttpbridge.guzzle')), config('crossbarhttpbridge.options'));
        });
    }
}
