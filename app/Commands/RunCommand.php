<?php namespace App\Commands;

use Sculptor\Stages;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class RunCommand extends Command
{
    use CommonCommand;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'run {?--dump}';

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
    public function handle(Stages $stages): int
    {
        $this->preamble();

        if (!$stages->run($this)) {

            $this->error($stages->error());

            $this->dump();

            return 1;
        }

        $this->info("Run time taken {$this->elapsed()}");

        $this->info('Here your master credentials, save in a safe location!');

        $this->table(['name', 'value'], $stages->show());


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
