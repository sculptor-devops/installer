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
            $filename = '/etc/cron.d/panel.crontab';

            $conf = $this->template('panel.crontab');

            $written = File::put($filename, $conf);

            if (!$written) {

                return false;
            }

            $this->command(['crontab', $filename]);

            $this->internal = 'Unable to write crontab';

            return true;
        } catch (Exception $e) {

            Log::error($e->getMessage());

            return false;
        }
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
