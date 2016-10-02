<?php

namespace Torann\GeoIP\Cache;

use Torann\GeoIP\Location;
use Illuminate\Support\Arr;
use Torann\GeoIP\Contracts\CacheInterface;

abstract class AbstractCache implements CacheInterface
{
    /**
     * Driver config
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new service instance.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;

        $this->boot();
    }

    /**
     * The "booting" method of the service.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Create a location instance from the provided attributes.
     *
     * @param array $attributes
     *
     * @return Location
     */
    public function hydrate($attributes = [])
    {
        return is_array($attributes)
            ? new Location($attributes)
            : null;
    }

    /**
     * Get configuration value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function config($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }
}