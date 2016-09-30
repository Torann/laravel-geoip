<?php

namespace Torann\GeoIP\Console;

use Torann\GeoIP\GeoIP;
use Illuminate\Console\Command;

class UpdateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'geoip:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update geoip database files to the latest version';

    /**
     * GeoIP instance.
     *
     * @var \Torann\GeoIP\GeoIP
     */
    protected $geoip;

    /**
     * Create a new console command instance.
     *
     * @param GeoIP $geoip
     */
    public function __construct(GeoIP $geoip)
    {
        parent::__construct();

        $this->geoip = $geoip;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        // Get default service
        $service = $this->geoip->getService();

        // Ensure the selected service supports updating
        if (method_exists($service, 'update') === false) {
            $this->error('The current service "' . get_class($service). '" does not support updating.');
            return;
        }

        $this->comment('Updating...');

        // Perform update
        if ($result = $service->update()) {
            $this->info($result);
        }
        else {
            $this->error('Update failed!');
        }
    }
}