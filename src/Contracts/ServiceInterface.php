<?php

namespace Torann\GeoIP\Contracts;

interface ServiceInterface
{
    /**
     * Determine a location based off of
     * the provided IP address.
     *
     * @param string $ip
     *
     * @return array
     */
    public function locate($ip);
}