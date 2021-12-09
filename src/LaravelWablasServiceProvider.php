<?php

namespace Shadowbane\LaravelWablas;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\ServiceProvider;

/**
 * Class LaravelWablasServiceProvider.
 *
 * @package Shadowbane\LaravelWablas
 */
class LaravelWablasServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->offerPublishing();

        $this->app->when(LaravelWablasChannel::class)
            ->needs(LaravelWablas::class)
            ->give(static function () {
                return new LaravelWablas(
                    config('laravel-wablas.token'),
                    app(HttpClient::class),
                    config('laravel-wablas.endpoint')
                );
            });
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-wablas.php',
            'laravel-wablas'
        );
    }

    protected function offerPublishing()
    {
        if (! function_exists('config_path')) {
            // function not available and 'publish' not relevant in Lumen
            return;
        }

        $this->publishes([
            __DIR__.'/../config/laravel-wablas.php' => config_path('laravel-wablas.php'),
        ], 'config');
    }
}
