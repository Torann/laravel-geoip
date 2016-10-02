<?php

namespace Torann\GeoIP\Console;

use Illuminate\Console\Command;

class Clear extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'geoip:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear GeoIP cached locations.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->output->write("Clearing cache...");

        if (is_string($result = app('geoip')->getCache()->flush())) {
            $this->output->writeln('<error>' . ($result ?: 'Failed') . '</error>');
        }
        else {
            $this->output->writeln("<info>success</info>");
        }
    }
}