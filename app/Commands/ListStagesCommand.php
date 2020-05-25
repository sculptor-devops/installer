<?php namespace App\Commands;

use Sculptor\Stages;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ListStagesCommand extends Command
{
    use CommonCommand;

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
    public function handle(Stages $stages): int
    {

        $this->preamble();

        $stages = $stages->list();

        $this->table(['Step', 'Name'], $stages);

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
