<?php

namespace App\Commands;

use Eppak\Stages;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class StageCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'stage {--step=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Run single stage';

    /**
     * Execute the console command.
     *
     * @param Stages $stages
     * @return mixed
     */
    public function handle(Stages $stages)
    {
        $step = $this->option('step');

        $this->info("OS Version {$stages->version()}");

        $this->info("[Warning every step can take several minutes]");

        $done = $this->task("Running {$step}", function() use($stages, $step) {

            return $stages->stage($step);
        });

        if (!$done) {
            $this->error("Error: {$stages->error()}");

            return 1;
        }

        return 0;
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
