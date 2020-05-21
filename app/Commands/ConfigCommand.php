<?php

namespace App\Commands;

use Eppak\Services\Configuration;
use Eppak\Services\Templates;
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
    protected $signature = 'config {?--templates}';

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
     * @param Templates $templates
     * @return mixed
     */
    public function handle(Configuration $configuration, Templates $templates): int
    {
        $this->configuration($configuration);

        if ($this->option('templates')) {
            $this->templates($templates);
        }

        return 0;
    }

    private function configuration(Configuration $configuration): void
    {
        $filename = getcwd() . '/' . APP_CONFIG_FILENAME;
        if (File::exists($filename)) {
            $this->warn("Customized configuration already exists {$filename}");

            return;
        }

        $this->info("Writing customizable {$filename}");

        $config = $configuration->template();

        File::put($filename, $config);
    }

    private function templates(Templates $templates): void
    {
        $path = getcwd() . '/' . APP_CONFIG_CUSTOM_TEMPLATE;

        if (!File::exists($path)) {
            File::makeDirectory($path);
        }

        foreach ($templates->all() as $config) {

            $filename = "{$path}/" . $config['name'];

            if(!File::exists($filename)) {
                $this->info("Writing template {$filename}");

                File::put($filename, $config['content']);

                continue;
            }

            $this->warn("Customized template already exists {$filename}");
        }
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
