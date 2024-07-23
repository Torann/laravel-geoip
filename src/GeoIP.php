<?php

namespace Torann\GeoIP;

use Exception;
use Monolog\Logger;
use Illuminate\Support\Arr;
use Illuminate\Cache\CacheManager;
use Monolog\Handler\StreamHandler;

class GeoIP
{
    /**
     * Illuminate config repository instance.
     *
     * @var array
     */
    protected $config;

    /**
     * Remote Machine IP address.
     *
     * @var float
     */
    protected $remote_ip = null;

    /**
     * Current location instance.
     *
     * @var Location
     */
    protected $location = null;

    /**
     * Currency data.
     *
     * @var array
     */
    protected $currencies = null;

    /**
     * GeoIP service instance.
     *
     * @var Contracts\ServiceInterface
     */
    protected $service;

    /**
     * Cache manager instance.
     *
     * @var \Illuminate\Cache\CacheManager
     */
    protected $cache;

    /**
     * Default Location data.
     *
     * @var array
     */
    protected $default_location = [
        'ip' => '127.0.0.0',
        'iso_code' => 'US',
        'country' => 'United States',
        'city' => 'New Haven',
        'state' => 'CT',
        'state_name' => 'Connecticut',
        'postal_code' => '06510',
        'lat' => 41.31,
        'lon' => -72.92,
        'timezone' => 'America/New_York',
        'continent' => 'NA',
        'currency' => 'USD',
        'default' => true,
        'cached' => false,
    ];

    /**
     * Create a new GeoIP instance.
     *
     * @param array        $config
     * @param CacheManager $cache
     */
    public function __construct(array $config, CacheManager $cache)
    {
        $this->config = $config;

        // Create caching instance
        $this->cache = new Cache(
            $cache,
            $this->config('cache_tags'),
            $this->config('cache_expires', 30)
        );

        // Set custom default location
        $this->default_location = array_merge(
            $this->default_location,
            $this->config('default_location', [])
        );

        // Set IP
        $this->remote_ip = $this->default_location['ip'] = $this->getClientIP();
    }

    /**
     * Get the location from the provided IP.
     *
     * @param string $ip
     *
     * @return \Torann\GeoIP\Location
     * @throws \Exception
     */
    public function getLocation($ip = null)
    {
        // Get location data
        $this->location = $this->find($ip);

        // Should cache location
        if ($this->shouldCache($this->location, $ip)) {
            $this->getCache()->set($ip, $this->location);
        }

        return $this->location;
    }

    /**
     * Find location from IP.
     *
     * @param string $ip
     *
     * @return \Torann\GeoIP\Location
     * @throws \Exception
     */
    private function find($ip = null)
    {
        // If IP not set, user remote IP
        $ip = $ip ?: $this->remote_ip;

        // Check cache for location
        if ($this->config('cache', 'none') !== 'none' && $location = $this->getCache()->get($ip)) {
            $location->cached = true;

            return $location;
        }

        // Check if the ip is not local or empty
        if ($this->isValid($ip)) {
            try {
                // Find location
                $location = $this->getService()->locate($ip);

                // Set currency if not already set by the service
                if (! $location->currency) {
                    $location->currency = $this->getCurrency($location->iso_code);
                }

                // Set default
                $location->default = false;

                return $location;
            } catch (\Exception $e) {
                if ($this->config('log_failures', true) === true) {
                    $log = new Logger('geoip');
                    $log->pushHandler(new StreamHandler(storage_path('logs/geoip.log'), Logger::ERROR));
                    $log->error($e);
                }
            }
        }

        return $this->getService()->hydrate($this->default_location);
    }

    /**
     * Get the currency code from ISO.
     *
     * @param string $iso
     *
     * @return string
     */
    public function getCurrency($iso)
    {
        if ($this->currencies === null && $this->config('include_currency', false)) {
            $this->currencies = include(__DIR__ . '/Support/Currencies.php');
        }

        return Arr::get($this->currencies, $iso);
    }

    /**
     * Get service instance.
     *
     * @return \Torann\GeoIP\Contracts\ServiceInterface
     * @throws Exception
     */
    public function getService()
    {
        if ($this->service === null) {
            // Get service configuration
            $config = $this->config('services.' . $this->config('service'), []);

            // Get service class
            $class = Arr::pull($config, 'class');

            // Sanity check
            if ($class === null) {
                throw new Exception('No GeoIP service is configured.');
            }

            // Create service instance
            $this->service = new $class($config);
        }

        return $this->service;
    }

    /**
     * Get cache instance.
     *
     * @return \Torann\GeoIP\Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Get the client IP address.
     *
     * @return string
     */
    public function getClientIP()
    {
        $remotes_keys = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CLIENT_IP',
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
            'HTTP_X_FORWARDED_IP',
            'HTTP_X_CLUSTER_CLIENT_IP',
        ];

        foreach ($remotes_keys as $key) {
            if ($address = getenv($key)) {
                foreach (explode(',', $address) as $ip) {
                    if ($this->isValid($ip)) {
                        return $ip;
                    }
                }
            }
        }

        return '127.0.0.0';
    }

    /**
     * Checks if the ip is valid.
     *
     * @param string $ip
     *
     * @return bool
     */
    private function isValid($ip)
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
            && ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the location should be cached.
     *
     * @param Location    $location
     * @param string|null $ip
     *
     * @return bool
     */
    private function shouldCache(Location $location, $ip = null)
    {
        if ($location->default === true || $location->cached === true) {
            return false;
        }

        switch ($this->config('cache', 'none')) {
            case 'all':
            case 'some' && $ip === null:
                return true;
        }

        return false;
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
