<?php namespace Sculptor\Stages\V1804;

use Sculptor\Contracts\Stage;
use Sculptor\Stages\StageBase;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class Crontab extends StageBase implements Stage
{
    public function run(array $env = null): bool
    {
        try {

            if(!$this->add('panel.crontab', '/etc/cron.d/sculptor.admin', APP_PANEL_USER)) {
                return false;
            }

            if(!$this->add('www-data.crontab', '/etc/cron.d/sculptor.www', APP_PANEL_HTTP_PANEL)) {
                return false;
            }

            return true;
        } catch (Exception $e) {

            Log::error($e->getMessage());

            return false;
        }
    }

    private function add(string $filename, string $destination, string $user): bool
    {
        $conf = $this->template($filename);

        $written = File::put($destination, $conf);

        if (!$written) {
            $this->internal = "Cannot write to {$destination}";
            return false;
        }

        $this->command(['crontab', '-u', $user, $destination]);

        return true;
    }

    public function name(): string
    {
        return 'Crontab';
    }

    public function env(): ?array
    {
        return null;
    }
}
