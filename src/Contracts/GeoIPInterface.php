<?php

namespace Torann\GeoIP\Contracts;

interface GeoIPInterface
{
    /**
     * Get the location from the provided IP.
     *
     * @param  string  $ip
     *
     * @return \Torann\GeoIP\Location
     */
    public function getLocation($ip = null);

    /**
     * Get the currency code from ISO.
     *
     * @param  string  $iso
     *
     * @return string
     */
    public function getCurrency($iso);

    /**
     * Get service instance.
     *
     * @return \Torann\GeoIP\Contracts\ServiceInterface
     *
     * @throws \Exception
     */
    public function getService();

    /**
     * Get cache instance.
     *
     * @return \Torann\GeoIP\Cache
     */
    public function getCache();

    /**
     * Get the client IP address.
     *
     * @return string
     */
    public function getClientIP();

    /**
     * Get configuration value.
     *
     * @param  string  $key
     * @param  mixed   $default
     *
     * @return mixed
     */
    public function config($key, $default = null);
}
