<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SlaMonitorService;

class MonitorSla extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'sla:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor SLA warnings and breaches';

    /**
     * Execute the console command.
     */

    public function handle(SlaMonitorService $monitor): int {

        $monitor->monitor();

        $this->info('SLA monitoring completed.');

        return self::SUCCESS;
    }
}