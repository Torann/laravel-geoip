<?php

namespace Torann\GeoIP;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class GeoIPServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerGeoIpConfig();
        $this->registerGeoIpService();

        if ($this->app->runningInConsole()) {
            $this->registerResources();
            $this->registerGeoIpCommands();
        }
    }

    /**
     * Register currency provider.
     *
     * @return void
     */
    public function registerGeoIpService()
    {
        $this->app->singleton(Contracts\GeoIPInterface::class, function ($app) {
            return new GeoIP(
                $app['config']->get('geoip', []), $app['cache']
            );
        });

        $this->app->bind('geoip', Contracts\GeoIPInterface::class);
    }

    /**
     * Register resources.
     *
     * @return void
     */
    public function registerResources()
    {
        if ( ! $this->isLumen()) {
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
     * Register the GeoIp config.
     */
    private function registerGeoIpConfig()
    {
        if ( ! $this->isLumen()) {
            $this->mergeConfigFrom(__DIR__ . '/../config/geoip.php', 'geoip');
        }
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

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Contracts\GeoIPInterface::class, 'geoip'];
    }
}
