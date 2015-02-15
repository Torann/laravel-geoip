<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Service
    |--------------------------------------------------------------------------
    |
    | Current only supports 'maxmind'.
    |
    */

    'service' => 'maxmind',

    /*
    |--------------------------------------------------------------------------
    | Services settings
    |--------------------------------------------------------------------------
    |
    | Service specific settings.
    |
    */

    'maxmind' => array(
        'type'        => 'database', // database or web_service
        'user_id'     => env('GEOIP_USER_ID'),
        'license_key' => env('GEOIP_LICENSE_KEY')
    ),

    /*
    |--------------------------------------------------------------------------
    | Default Location
    |--------------------------------------------------------------------------
    |
    | Return when a location is not found.
    |
    */

    'default_location' => array (
        "ip"           => "127.0.0.0",
        "isoCode"      => "US",
        "country"      => "United States",
        "city"         => "New Haven",
        "state"        => "CT",
        "postal_code"  => "06510",
        "lat"          => 41.31,
        "lon"          => -72.92,
        "timezone"     => "America/New_York",
        "continent"    => "NA"
    ),

);