# GeoIP for Laravel 5

[![Latest Stable Version](https://poser.pugx.org/torann/geoip/v/stable.png)](https://packagist.org/packages/torann/geoip) [![Total Downloads](https://poser.pugx.org/torann/geoip/downloads.png)](https://packagist.org/packages/torann/geoip)

Determine the geographical location of website visitors based on their IP addresses. [Homepage](http://lyften.com/projects/laravel-geoip/)

----------

## Installation

- [GeoIP for Laravel 5 on Packagist](https://packagist.org/packages/torann/geoip)
- [GeoIP for Laravel 5 on GitHub](https://github.com/Torann/laravel-geoip)
- [Laravel 5.0/Pre-5.5 PHP Installation](https://github.com/Torann/laravel-geoip/tree/0.2.2)
- [Laravel 4 Installation](https://github.com/Torann/laravel-geoip/tree/0.1.1)

### Composer

From the command line run:

```bash
$ composer require torann/geoip
```

### Laravel

Once installed you need to register the service provider with the application. Open up `config/app.php` and find the `providers` key.

```php
'providers' => [

    \Torann\GeoIP\GeoIPServiceProvider::class,

]
```

This package also comes with a facade, which provides an easy way to call the the class. Open up `config/app.php` and find the aliases key.

```php
'aliases' => [

    'GeoIP' => \Torann\GeoIP\Facades\GeoIP::class,

];
```

### Publish the configurations

Run this on the command line from the root of your project:

```bash
php artisan vendor:publish --provider="Torann\GeoIP\GeoIPServiceProvider"
```

A configuration file will be publish to `config/geoip.php`.

### Update max mind cities database/continents

```
$ php artisan geoip:update
```

**Database Service**: To use the database version of [MaxMind](http://www.maxmind.com) services download the `GeoLite2-City.mmdb` from [http://dev.maxmind.com/geoip/geoip2/geolite2/](http://dev.maxmind.com/geoip/geoip2/geolite2/) and extract it to `storage/app/geoip.mmdb`. And that's it.

**IP-API Continents**: To use continents with IP-API.com you must run `php artisan geoip:update` to download the continent file.

## Usage

Get the location data for a website visitor:

```php
$location = geoip();
```

> When an IP is not given the `$_SERVER["REMOTE_ADDR"]` is used.

Getting the location data for a given IP:

```php
$location = geoip('232.223.11.11');
```

### Example Data

```php
[
    'ip'           => '232.223.11.11',
    'iso_code'     => 'US',
    'country'      => 'United States',
    'city'         => 'New Haven',
    'state'        => 'CT',
    'state_name'   => 'Connecticut',
    'postal_code'  => '06510',
    'lat'          => 41.28,
    'lon'          => -72.88,
    "timezone'     => 'America/New_York',
    'continent'    => 'NA',
    'currency'     => 'USD',
    'default'      => false,
]
```

#### Default Location

In the case that a location is not found the fallback location will be returned with the `default` parameter set to `true`. To set your own default change it in the configurations `config/geoip.php`

## Change Log

#### v1.0.0

- Major code refactoring and cleanup
- Add currency support
- Add `state_name` to `$location` array #46
- Set locales in config #45
- Raise PHP requirement to 5.5
- Support custom Geo IP services
  - Added ip-api.com service (Thanks to [nikkiii](https://github.com/nikkiii))

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

