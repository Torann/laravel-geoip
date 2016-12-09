<?php

if ( ! function_exists('geoip')) {
    /**
     * Get the location of the provided IP.
     *
     * @param  string  $ip
     *
     * @return \Torann\GeoIP\Contracts\GeoIPInterface|\Torann\GeoIP\Location
     */
    function geoip($ip = null)
    {
        /** @var  Torann\GeoIP\Contracts\GeoIPInterface  $geoip */
        $geoip = app(Torann\GeoIP\Contracts\GeoIPInterface::class);

        return is_null($ip) ? $geoip : $geoip->getLocation($ip);
    }
}
