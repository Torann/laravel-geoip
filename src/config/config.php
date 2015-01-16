<?php

return array(

	// Service (only maxmind so far)
	'service' => 'maxmind',

	'maxmind' => array(
		'type'			=> 'database', // database or web_service
		'user_id' 		=> '',
		'license_key' 	=> ''
	),

	// In the case that a location is not found or local ip address
	'default_location' => array(
		'ip' 					=> "127.0.0.0",
		'isoCode' 		=> "US",
		'country' 		=> "United States",
		'city' 				=> "New Haven",
		'state' 			=> "CT",
		'postal_code' => "06510",
		'lat' 				=> 41.31,
		'lon' 				=> -72.92,
		'timezone' 		=> "America/New_York",
		'continent'		=> "NA"
	)

);