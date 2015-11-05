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
	private $outputFile;

	/**
	 * Create a new console command instance.
	 *
	 * @param \Illuminate\Config\Repository $config
	 */
	public function __construct(Repository $config)
	{
		parent::__construct();

		$this->outputFile = $config->get('geoip.ipapi.continent_path');
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

		$lines = explode("\n", $res->getBody());

		array_shift($lines);

		$output = [];

		foreach ($lines as $line) {
			$arr = str_getcsv($line);

			if (count($arr) < 2) {
				continue;
			}

			$output[$arr[0]] = $arr[1];
		}

		file_put_contents($this->outputFile, json_encode($output));
	}
}