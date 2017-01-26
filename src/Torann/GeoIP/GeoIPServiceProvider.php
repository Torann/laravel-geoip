<?php namespace Torann\GeoIP;

use Illuminate\Support\ServiceProvider;
use Torann\GeoIP\Console\UpdateCommand;

class GeoIPServiceProvider extends ServiceProvider {

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
        $this->publishes([
            __DIR__.'/../../config/geoip.php' => config_path('geoip.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('geoip', function ($app) {
            return new GeoIP($app['config'], $app['session.store']);
        });

        $this->app->singleton('command.geoip.update', function ($app) {
            return new UpdateCommand($app['config']);
        });

        $this->commands(['command.geoip.update']);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('geoip', 'command.geoip.update');
    }

}