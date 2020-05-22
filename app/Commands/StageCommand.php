<?php

namespace App\Commands;

use Eppak\Services\Logs;
use Eppak\Stages;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class StageCommand extends Command
{
    use CommonCommand;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'run-stage {--step=}';

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
    public function handle(Stages $stages): int
    {
        $this->preamble();

        $step = $this->option('step');

        $done = $this->task("Running {$step}", function() use($stages, $step) {

            return $stages->stage($step);
        });

        $this->info("Run time taken {$this->elapsed()}");

        if (!$done) {
            $this->error("Error: {$stages->error()}");

            return 1;
        }

        $this->table(['name', 'value'], $stages->show());

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
