<?php

namespace App\Commands;

use Eppak\Services\Configuration;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class ConfigCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'config';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a configuration to customize';

    /**
     * Execute the console command.
     *
     * @param Configuration $configuration
     * @return mixed
     */
    public function handle(Configuration $configuration)
    {
        $config = $configuration->template();

        File::put(APP_CONFIG_FILENAME, $config);

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
