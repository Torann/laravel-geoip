<?php

namespace Torann\GeoIP\Services;

use Torann\GeoIP\GeoIP;
use Illuminate\Support\Arr;
use Torann\GeoIP\Contracts\ServiceInterface;

abstract class AbstractService implements ServiceInterface
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
     * Get configuration value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getConfig($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }
}