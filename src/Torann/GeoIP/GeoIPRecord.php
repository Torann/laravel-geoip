<?php namespace Torann\GeoIP;

use Illuminate\Database\Eloquent\Model;

class GeoIPRecord extends Model {
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'geoip_records';
	
	/**
	 * The attributes that are hidden from the return value.
	 *
	 * @var array
	 */
	protected $hidden = ['id', 'created_at', 'updated_at'];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['ip', 'isoCode', 'country', 'city', 'state', 'postal_code', 'lat', 'lon', 'timezone', 'continent'];
	
}