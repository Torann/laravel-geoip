<?php

use Torann\GeoIP\GeoIP;

if (!function_exists(GeoIP::class)) {
    /**
     * Get the location of the provided IP.
     *
     * @param string $ip
     *
     * @return \Torann\GeoIP\GeoIP|\Torann\GeoIP\Location
     */
    function geoip($ip = null)
    {
        if (is_null($ip)) {
            return app(GeoIP::class);
        }

        return app(GeoIP::class)->getLocation($ip);
    }
}