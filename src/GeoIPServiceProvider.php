<?php

namespace Torann\GeoIP;

use Illuminate\Support\ServiceProvider;
use Torann\GeoIP\Console\UpdateCommand;

class GeoIPServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->isLumen() === false) {
            $this->publishes([
                __DIR__ . '/config/geoip.php' => config_path('geoip.php'),
            ], 'config');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['geoip'] = $this->app->share(function ($app) {
            return new GeoIP(
                $app->config->get('geoip', []),
                $app['session.store']
            );
        });

        $this->app['command.geoip.update'] = $this->app->share(function ($app) {
            return new UpdateCommand($app['geoip']);
        });

        $this->commands(['command.geoip.update']);
    }

    /**
     * Check if package is running under Lumen app
     *
     * @return bool
     */
    protected function isLumen()
    {
        return str_contains($this->app->version(), 'Lumen') === true;
    }
}