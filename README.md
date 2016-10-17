# GeoIP for Laravel 5

[![Build Status](https://travis-ci.org/Torann/laravel-geoip.svg?branch=master)](https://travis-ci.org/Torann/laravel-geoip)
[![Latest Stable Version](https://poser.pugx.org/torann/geoip/v/stable.png)](https://packagist.org/packages/torann/geoip)
[![Total Downloads](https://poser.pugx.org/torann/geoip/downloads.png)](https://packagist.org/packages/torann/geoip)
[![Patreon donate button](https://img.shields.io/badge/patreon-donate-yellow.svg)](https://www.patreon.com/torann)
[![Donate weekly to this project using Gratipay](https://img.shields.io/badge/gratipay-donate-yellow.svg)](https://gratipay.com/~torann)
[![Donate to this project using Flattr](https://img.shields.io/badge/flattr-donate-yellow.svg)](https://flattr.com/profile/torann)
[![Donate to this project using Paypal](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4CJA2A97NPYVU)

Determine the geographical location and currency of website visitors based on their IP addresses.

- [GeoIP for Laravel 5 on Packagist](https://packagist.org/packages/torann/geoip)
- [GeoIP for Laravel 5 on GitHub](https://github.com/Torann/laravel-geoip)
- [Upgrade Guides](http://lyften.com/projects/laravel-geoip/doc/upgrade.html)

## Official Documentation

Documentation for the package can be found on [Lyften.com](http://lyften.com/projects/laravel-geoip/).

## Laravel 4

For Laravel 4 Installation see [version 0.1.1](https://github.com/Torann/laravel-geoip/tree/0.1.1)

## Change Log

#### v1.0.2

- Support double IP addresses #25

#### v1.0.1

- Fix bug #60

#### v1.0.0

- Major code refactoring and cleanup
- Add currency support
- Add Location object
- Add cache drivers
- Add `state_name` to `$location` array #46
- Set locales in config #45
- Raise PHP requirement to 5.5
- Fix file structure to adher to PSR-4 file structure. #40
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

## Contributions

Many people have contributed to project since its inception.

Thanks to:

- [nikkiii](https://github.com/nikkiii)
- [jeffhennis](https://github.com/jeffhennis)
- [max-kovpak](https://github.com/max-kovpak)
- [dotpack](https://github.com/dotpack)