<?php

namespace Torann\GeoIP\Cache;

use Torann\GeoIP\Location;
use Torann\GeoIP\Contracts\CacheInterface;

class Session extends AbstractCache implements CacheInterface
{
    /**
     * Instance of session manager.
     *
     * @var \Illuminate\Session\SessionManager
     */
    protected $session;

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->session = app('session');
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        $value = $this->session->get('torann-geoip-location');

        return $this->hydrate($value);
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, Location $location)
    {
        return $this->session->set('torann-geoip-location', $location->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return 'not supported in sessions';
    }
}