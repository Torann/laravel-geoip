<?php

namespace Torann\GeoIP\Cache;

use Torann\GeoIP\Location;
use Torann\GeoIP\Contracts\CacheInterface;

class Sync extends AbstractCache implements CacheInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, Location $location)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return true;
    }
}