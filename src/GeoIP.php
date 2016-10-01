<?php

namespace Torann\GeoIP;

use Exception;
use Monolog\Logger;
use Illuminate\Support\Arr;
use Monolog\Handler\StreamHandler;
use Illuminate\Session\Store as SessionStore;

class GeoIP
{
    /**
     * The session store.
     *
     * @var \Illuminate\Session\Store
     */
    protected $session;

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
     * Location data.
     *
     * @var array
     */
    protected $location = null;

    /**
     * Currency data.
     *
     * @var array
     */
    protected $currencies = [];

    /**
     * GeoIP service instance.
     *
     * @var Contracts\ServiceInterface
     */
    protected $service;

    /**
     * Default Location data.
     *
     * @var array
     */
    protected $default_location = [
        'ip' => '127.0.0.0',
        'isoCode' => 'US', // @deprecated
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
        'currency' => null,
        'default' => true,
    ];

    /**
     * Create a new GeoIP instance.
     *
     * @param array        $config
     * @param SessionStore $session
     */
    public function __construct(array $config, SessionStore $session)
    {
        $this->config = $config;
        $this->session = $session;

        // Set custom default location
        $this->default_location = array_merge(
            $this->default_location,
            $this->config('default_location', [])
        );

        // Include currencies
        if ($this->config('include_currency', false)) {
            $this->currencies = include(__DIR__ . '/Support/Currencies.php');
        }

        // Set IP
        $this->remote_ip = $this->default_location['ip'] = $this->getClientIP();
    }

    /**
     * Get the location from the provided IP.
     *
     * @param string $ip
     *
     * @return array
     */
    public function getLocation($ip = null)
    {
        // Get location data
        $this->location = $this->find($ip);

        // Save user's location
        if ($ip === null) {
            $this->session->set('geoip-location', $this->location);
        }

        return $this->location;
    }

    /**
     * Find location from IP.
     *
     * @param  string $ip Optional
     *
     * @return array
     * @throws \Exception
     */
    private function find($ip = null)
    {
        // Check session for location
        if ($ip === null && $position = $this->session->get('geoip-location')) {
            return $position;
        }

        // Set location
        $location = $this->default_location;

        // If IP not set, user remote IP
        $ip = $ip ?: $this->remote_ip;

        // Check if the ip is not local or empty
        if ($this->isValid($ip)) {
            try {
                // Find location
                $location = $this->getService()->locate($ip);

                // Set currency if not already set by the service
                if (isset($location['currency']) === false) {
                    $location['currency'] = $this->getCurrency($location['iso_code']);
                }

                // Set default
                $location['default'] = false;
            }
            catch (\Exception $e) {
                if ($this->config('log_failures', true) === true) {
                    $log = new Logger('geoip');
                    $log->pushHandler(new StreamHandler(storage_path('logs/geoip.log'), Logger::ERROR));
                    $log->addError($e);
                }
            }
        }

        return $location;
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
        return Arr::get($this->currencies, $iso);
    }

    /**
     * Get service instance.
     *
     * @return \Torann\GeoIP\Contracts\ServiceInterface
     */
    public function getService()
    {
        if ($this->service === null) {
            // Get driver configuration
            $config = $this->config('services.' . $this->config('service'), []);

            // Get driver class
            $driver = Arr::pull($config, 'class');

            // Create driver instance
            $this->service = app($driver, [$config]);
        }

        return $this->service;
    }

    /**
     * Get the client IP address.
     *
     * @return string
     */
    public function getClientIP()
    {
        if ($ip = getenv('HTTP_CLIENT_IP')) {
            return $ip;
        }
        else if ($ip = getenv('HTTP_X_FORWARDED_FOR')) {
            return $ip;
        }
        else if ($ip = getenv('HTTP_X_FORWARDED')) {
            return $ip;
        }
        else if ($ip = getenv('HTTP_FORWARDED_FOR')) {
            return $ip;
        }
        else if ($ip = getenv('HTTP_FORWARDED')) {
            return $ip;
        }
        else if ($ip = getenv('REMOTE_ADDR')) {
            return $ip;
        }
        else if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
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
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
            && !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE)
        ) {
            return false;
        }

        return true;
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
