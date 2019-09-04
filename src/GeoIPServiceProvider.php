<?php

namespace Torann\GeoIP;

use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

class GeoIPServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerGeoIpService();

        if ($this->app->runningInConsole()) {
            $this->registerResources();
            $this->registerGeoIpCommands();
        }

        if ($this->isLumen() === false) {
            $this->mergeConfigFrom(__DIR__ . '/../config/geoip.php', 'geoip');
        }
    }

    /**
     * Register currency provider.
     *
     * @return void
     */
    public function registerGeoIpService()
    {
        $this->app->singleton('geoip', function ($app) {
            return new GeoIP(
                $app->config->get('geoip', []),
                $app['cache']
            );
        });
    }

    /**
     * Register resources.
     *
     * @return void
     */
    public function registerResources()
    {
        if ($this->isLumen() === false) {
            $this->publishes([
                __DIR__ . '/../config/geoip.php' => config_path('geoip.php'),
            ], 'config');
        }
    }

    /**
     * Register commands.
     *
     * @return void
     */
    public function registerGeoIpCommands()
    {
        $this->commands([
            Console\Update::class,
            Console\Clear::class,
        ]);
    }

    /**
     * Check if package is running under Lumen app
     *
     * @return bool
     */
    protected function isLumen()
    {
        return Str::contains($this->app->version(), 'Lumen') === true;
    }
}
