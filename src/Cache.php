<?php

namespace Torann\GeoIP;

use Illuminate\Cache\CacheManager;

class Cache
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
    protected $expires;

    /**
     * Create a new cache instance.
     *
     * @param CacheManager $cache
     * @param array        $tags
     * @param int          $expires
     */
    public function __construct(CacheManager $cache, $tags, $expires = 30)
    {
        $this->cache = $tags ? $cache->tags($tags) : $cache;
        $this->expires = $expires;
    }

    /**
     * Get an item from the cache.
     *
     * @param string $name
     *
     * @return Location|null
     */
    public function get($name)
    {
        $value = $this->cache->get($name);

        return is_array($value)
            ? new Location($value)
            : null;
    }

    /**
     * Store an item in cache.
     *
     * @param string   $name
     * @param Location $location
     *
     * @return bool
     */
    public function set($name, Location $location)
    {
        return $this->cache->put($name, $location->toArray(), $this->expires);
    }

    /**
     * Flush cache for tags.
     *
     * @return bool
     */
    public function flush()
    {
        return $this->cache->flush();
    }
}