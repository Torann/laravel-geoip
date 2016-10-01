<?php namespace Torann\GeoIP;

use GeoIp2\Database\Reader;
use GeoIp2\WebService\Client;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use GeoIp2\Exception\AddressNotFoundException;

use Illuminate\Support\Arr;
use Illuminate\Config\Repository;
use Illuminate\Session\Store as SessionStore;

class GeoIP {

	/**
	 * The session store.
	 *
	 * @var \Illuminate\Session\Store
	 */
	protected $session;

	/**
	 * Illuminate config repository instance.
	 *
	 * @var \Illuminate\Config\Repository
	 */
	protected $config;

	/**
	 * Remote Machine IP address.
	 *
	 * @var float
	 */
	protected $remote_ip = null;

	/**
	 * Location data.
	 *
	 * @var array
	 */
	protected $location = null;

	/**
	 * Reserved IP address.
	 *
	 * @var array
	 */
	protected $reserved_ips = array (
		array('0.0.0.0','2.255.255.255'),
		array('10.0.0.0','10.255.255.255'),
		array('127.0.0.0','127.255.255.255'),
		array('169.254.0.0','169.254.255.255'),
		array('172.16.0.0','172.31.255.255'),
		array('192.0.2.0','192.0.2.255'),
		array('192.168.0.0','192.168.255.255'),
		array('255.255.255.0','255.255.255.255'),
	);

	/**
	 * Default Location data.
	 *
	 * @var array
	 */
	protected $default_location = array (
		"ip" 			=> "127.0.0.0",
		"isoCode" 		=> "US",
		"country" 		=> "United States",
		"city" 			=> "New Haven",
		"state" 		=> "CT",
		"postal_code"   => "06510",
		"lat" 			=> 41.31,
		"lon" 			=> -72.92,
		"timezone" 		=> "America/New_York",
		"continent"		=> "NA",
		"default"       => true,
	);

	/**
	 * Create a new GeoIP instance.
	 *
	 * @param  \Illuminate\Config\Repository  $config
	 * @param  \Illuminate\Session\Store      $session
	 */
	public function __construct(Repository $config, SessionStore $session)
	{
		$this->config  = $config;
		$this->session = $session;

		// Set custom default location
		$this->default_location = array_merge(
			$this->default_location,
			$this->config->get('geoip.default_location', array())
		);

		// Set IP
		$this->remote_ip = $this->default_location['ip'] = $this->getClientIP();
	}

	/**
	 * Save location data in the session.
	 *
	 * @return void
	 */
	function saveLocation()
	{
		$this->session->set('geoip-location', $this->location);
	}

	/**
	 * Get location from IP.
	 *
	 * @param  string $ip Optional
	 * @return array
	 */
	public function getLocation($ip = null)
	{
		// Get location data
		$this->location = $this->find($ip);

		// Save user's location
		if ($ip === null) {
			$this->saveLocation();
		}

		return $this->location;
	}

	/**
	 * Find location from IP.
	 *
	 * @param  string $ip Optional
	 * @return array
	 * @throws \Exception
	 */
	private function find($ip = null)
	{
		// Check Session
		if ($ip === null && $position = $this->session->get('geoip-location')) {
			return $position;
		}

		// If IP not set, user remote IP
		if ($ip === null) {
			$ip = $this->remote_ip;
		}

		// Check if the ip is not local or empty
		if ($this->checkIp($ip)) {
			// Get service name
			$service = 'locate_'.$this->config->get('geoip.service');

			// Check for valid service
			if (! method_exists($this, $service)) {
				throw new \Exception("GeoIP Service not support or setup.");
			}

			return $this->$service($ip);
		}

		return $this->default_location;
	}

	private $maxmind;

	/**
	 * Maxmind Service.
	 *
	 * @param  string $ip
	 * @return array
	 */
	private function locate_maxmind($ip)
	{
		$settings = $this->config->get('geoip.maxmind');

		if (empty($this->maxmind)) {
			if (Arr::get($settings, 'type') === 'web_service') {
				$this->maxmind = new Client(
                    Arr::get($settings, 'user_id'),
                    Arr::get($settings, 'license_key'),
                    Arr::get($settings, 'locales', ['en'])
                );
			}
			else {
				$this->maxmind = new Reader(
                    Arr::get($settings, 'database_path'),
                    Arr::get($settings, 'locales', ['en'])
                );
			}
		}

		try {
			$record = $this->maxmind->city($ip);

			$location = array(
				"ip"			=> $ip,
				"isoCode" 		=> $record->country->isoCode,
				"country" 		=> $record->country->name,
				"city" 			=> $record->city->name,
				"state" 		=> $record->mostSpecificSubdivision->isoCode,
				"postal_code"   => $record->postal->code,
				"lat" 			=> $record->location->latitude,
				"lon" 			=> $record->location->longitude,
				"timezone" 		=> $record->location->timeZone,
				"continent"		=> $record->continent->code,
				"default"       => false,
			);
		}
		catch (AddressNotFoundException $e)
		{
			$location = $this->default_location;

			$logFile = 'geoip';

			$log = new Logger($logFile);
			$log->pushHandler(new StreamHandler(storage_path("logs/{$logFile}.log"), Logger::ERROR));
			$log->addError($e);
		}

		unset($record);

		return $location;
	}

	/**
	 * Get the client IP address.
	 *
	 * @return string
	 */
	private function getClientIP()
	{
		if (getenv('HTTP_CLIENT_IP')) {
			$ipaddress = getenv('HTTP_CLIENT_IP');
		}
		else if (getenv('HTTP_X_FORWARDED_FOR')) {
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		}
		else if (getenv('HTTP_X_FORWARDED')) {
			$ipaddress = getenv('HTTP_X_FORWARDED');
		}
		else if (getenv('HTTP_FORWARDED_FOR')) {
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		}
		else if (getenv('HTTP_FORWARDED')) {
			$ipaddress = getenv('HTTP_FORWARDED');
		}
		else if (getenv('REMOTE_ADDR')) {
			$ipaddress = getenv('REMOTE_ADDR');
		}
		else if (isset($_SERVER['REMOTE_ADDR'])) {
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		}
		else {
			$ipaddress = '127.0.0.0';
		}

		return $ipaddress;
	}

	/**
	 * Checks if the ip is not local or empty.
	 *
	 * @return bool
	 */
	private function checkIp($ip)
	{
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			$longip = ip2long($ip);

			if (! empty($ip)) {
				foreach ($this->reserved_ips as $r) {
					$min = ip2long($r[0]);
					$max = ip2long($r[1]);

					if ($longip >= $min && $longip <= $max) {
						return false;
					}
				}

				return true;
			}
		} else if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			return true;
		}

		return false;
	}

}
