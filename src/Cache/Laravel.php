<?php

namespace Torann\GeoIP\Cache;

use Torann\GeoIP\Location;
use Torann\GeoIP\Contracts\CacheInterface;

class Laravel extends AbstractCache implements CacheInterface
{
    /**
     * Instance of cache manager.
     *
     * @var \Illuminate\Cache\CacheManager
     */
    protected $cache;

    /**
     * Lifetime of the cache.
     *
     * @var int
     */
    protected $minutes = 60;

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->cache = app('cache');

        $this->minutes = $this->config('expires', $this->minutes);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        $value = $this->cache->tags(['torann-geoip-location'])->get($name);

        return $this->hydrate($value);
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, Location $location)
    {
        return $this->cache->tags(['torann-geoip-location'])->put($name, $location->toArray(), $this->minutes);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return $this->cache->tags(['torann-geoip-location'])->flush();
    }
}