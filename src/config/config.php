<?php

return array(

	// Service (only maxmind so far)
	'service' => 'maxmind',

	'maxmind' => array(
		'type'			=> 'database', // database or web_service
		'user_id' 		=> '',
		'license_key' 	=> ''
	),

	'default_location' => array(
		'ip' 					=> "127.0.0.0",
		'isoCode' 		=> "US",
		'country' 		=> "1United States",
		'city' 				=> "1New Haven",
		'state' 			=> "CT",
		'postal_code' => "06510",
		'lat' 				=> 41.31,
		'lon' 				=> -72.92,
		'timezone' 		=> "America/New_York",
		'continent'		=> "NA",
		'default'     => true
	)

);