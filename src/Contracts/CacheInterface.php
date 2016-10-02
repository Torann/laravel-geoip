<?php

namespace Torann\GeoIP\Contracts;

use Torann\GeoIP\Location;

interface CacheInterface
{
    /**
     * Get an item from the cache.
     *
     * @param string $name
     *
     * @return array
     */
    public function get($name);

    /**
     * Store an item in cache.
     *
     * @param string   $name
     * @param Location $location
     *
     * @return bool
     */
    public function set($name, Location $location);

    /**
     * Flush cache for tags.
     *
     * @return bool
     */
    public function flush();
}