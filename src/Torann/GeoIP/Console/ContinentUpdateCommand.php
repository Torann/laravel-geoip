<?php namespace Torann\GeoIP\Console;

use GuzzleHttp\Client;
use Illuminate\Config\Repository;
use Illuminate\Console\Command;

class ContinentUpdateCommand extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'geoip:continents';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update geoip continents';

	/**
	 * The output file.
	 *
	 * @var string
	 */
	private $output;

	/**
	 * Create a new console command instance.
	 *
	 * @param \Illuminate\Config\Repository $config
	 */
	public function __construct(Repository $config)
	{
		parent::__construct();

		$this->output = $config->get('geoip.ipapi.continent_path');
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$client = new Client();

		$res = $client->get('http://dev.maxmind.com/static/csv/codes/country_continent.csv');

		$lines = collect(explode("\n", $res->getBody()));

		$csv = $lines->map(function($line) {
			return str_getcsv($line);
		});

		// Pop off the headers
		$csv->pop();

		file_put_contents($this->output, $csv->toJson());
	}
}