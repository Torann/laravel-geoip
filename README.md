# GeoIP for Laravel 5

[![Latest Stable Version](https://poser.pugx.org/torann/geoip/v/stable.png)](https://packagist.org/packages/torann/geoip) [![Total Downloads](https://poser.pugx.org/torann/geoip/downloads.png)](https://packagist.org/packages/torann/geoip)

Determine the geographical location of website visitors based on their IP addresses. [Homepage](http://lyften.com/projects/laravel-geoip/)

----------

## Installation

- [GeoIP for Laravel 5 on Packagist](https://packagist.org/packages/torann/geoip)
- [GeoIP for Laravel 5 on GitHub](https://github.com/Torann/laravel-geoip)
- [Laravel 4 Installation](https://github.com/Torann/laravel-geoip/tree/0.1.1)

To get the latest version of GeoIP simply require it in your `composer.json` file.

~~~
"torann/geoip": "0.2.*@dev"
~~~

You'll then need to run `composer install` to download it and have the autoloader updated.

Once GeoIP is installed you need to register the service provider with the application. Open up `config/app.php` and find the `providers` key.

~~~php
'providers' => array(

    'Torann\GeoIP\GeoIPServiceProvider',

)
~~~

GeoIP also ships with a facade which provides the static syntax for creating collections. You can register the facade in the `aliases` key of your `config/app.php` file.

~~~php
'aliases' => array(

    'GeoIP' => 'Torann\GeoIP\GeoIPFacade',

)
~~~

### Publish the configurations

Run this on the command line from the root of your project:

~~~
$ php artisan vendor:publish
~~~

A configuration file will be publish to `config/geoip.php`

### Update max mind cities database

~~~
$ php artisan geoip:update
~~~

**Database Service**: To use the database version of [MaxMind](http://www.maxmind.com) services download the `GeoLite2-City.mmdb` from [http://dev.maxmind.com/geoip/geoip2/geolite2/](http://dev.maxmind.com/geoip/geoip2/geolite2/) and extract it to `storage/app/geoip.mmdb`. And that's it.

## Usage

Get the location data for a website visitor:

```php
$location = GeoIP::getLocation();
```

> When an IP is not given the `$_SERVER["REMOTE_ADDR"]` is used.

Getting the location data for a given IP:

```php
$location = GeoIP::getLocation('232.223.11.11');
```

### Example Data

```php
array (
    "ip"           => "232.223.11.11",
    "isoCode"      => "US",
    "country"      => "United States",
    "city"         => "New Haven",
    "state"        => "CT",
    "postal_code"  => "06510",
    "lat"          => 41.28,
    "lon"          => -72.88,
    "timezone"     => "America/New_York",
    "continent"    => "NA",
    "default"      => false
);
```

#### Default Location

In the case that a location is not found the fallback location will be returned with the `default` parameter set to `true`. To set your own default change it in the configurations `config/geoip.php`

## Change Log

#### v0.2.1

- Add database_path to config
- Add update_url to config
- Add GeoIP database update command "php artisan geoip:update"
- Add some test
- Format code

#### v0.2.0

- Update to Laravel 5
- Support IPv6
- Log address not found exceptions
- Supports a custom default location

