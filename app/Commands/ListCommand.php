<?php

namespace App\Commands;

use Eppak\Stages;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ListCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'list-stages';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List installation stages';

    /**
     * Execute the console command.
     *
     * @param Stages $stages
     * @return mixed
     */
    public function handle(Stages $stages)
    {
        $this->info("OS Version {$stages->version()}");

        $stages = $stages->list();

        $this->table(['step', 'name'], $stages);

        return 0;
    }

    /**
     * Define the command's schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
