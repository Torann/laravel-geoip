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
     * Execute the console command for Laravel 5.5 and newer.
     *
     * @return void
     */
    public function handle()
    {
        $this->fire();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if ($this->isSupported() === false) {
            return $this->output->error('Default cache system does not support tags');
        }

        $this->performFlush();
    }

    /**
     * Is cache flushing supported.
     *
     * @return bool
     */
    protected function isSupported()
    {
        return empty(app('geoip')->config('cache_tags')) === false
            && in_array(config('cache.default'), ['file', 'database']) === false;
    }

    /**
     * Flush the cache.
     *
     * @return void
     */
    protected function performFlush()
    {
        $this->output->write("Clearing cache...");

        app('geoip')->getCache()->flush();

        $this->output->writeln("<info>complete</info>");
    }
}
