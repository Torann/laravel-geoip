<?php

namespace Torann\GeoIP;

use ArrayAccess;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

/**
 * Class Location
 *
 * @property string|null $ip
 * @property string|null $iso_code
 * @property string|null $country
 * @property string|null $city
 * @property string|null $state
 * @property string|null $state_name
 * @property string|null $postal_code
 * @property float|null $lat
 * @property float|null $lon
 * @property string|null $timezone
 * @property string|null $continent
 * @property string|null $currency
 * @property bool $default
 * @property bool $cached
 *
 * @package Torann\GeoIP
 */
class Location implements ArrayAccess
{
    /**
     * The location's attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Create a new location instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * Determine if the location is for the same IP address.
     *
     * @param  string $ip
     *
     * @return bool
     */
    public function same($ip)
    {
        return $this->getAttribute('ip') == $ip;
    }

    /**
     * Set a given attribute on the location.
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Get an attribute from the $attributes array.
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = Arr::get($this->attributes, $key);

        // First we will check for the presence of a mutator for the set operation
        // which simply lets the developers tweak the attribute as it is set.
        if (method_exists($this, 'get' . Str::studly($key) . 'Attribute')) {
            $method = 'get' . Str::studly($key) . 'Attribute';

            return $this->{$method}($value);
        }

        return $value;
    }

    /**
     * Return the display name of the location.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return preg_replace('/^,\s/', '', "{$this->city}, {$this->state}");
    }

    /**
     * Is the location the default.
     *
     * @return bool
     */
    public function getDefaultAttribute($value)
    {
        return is_null($value) ? false : $value;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Get the location's attribute
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Set the location's attribute
     *
     * @param  string $key
     * @param  mixed  $value
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed $offset
     * @param  mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }

    /**
     * Check if the location's attribute is set
     *
     * @param $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Unset an attribute on the location.
     *
     * @param  string $key
     *
     * @return void
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }
}
