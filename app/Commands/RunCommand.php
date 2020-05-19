<?php

namespace App\Commands;

use Eppak\Stages;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class RunCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'run';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run all stages';

    /**
     * Execute the console command.
     *
     * @param Stages $stages
     * @return mixed
     */
    public function handle(Stages $stages)
    {
        $this->info("OS Version {$stages->version()}");

        $this->info("You can see detailed log in {$this->logs()}");

        $this->info("Warning every step can take several minutes");

        if (!$stages->run($this)) {

            $this->error($stages->error());

            return 1;
        }

        $this->info('Here your master credentials, save in a safe location!');

        $this->table(['name', 'value'], $stages->show());

        return 0;
    }

    private function logs(): string
    {
        $log = app('log');

        return $log->driver()->getHandlers()[0]->getUrl();
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
