<?php namespace Eppak\Stages\V1804;

use Eppak\Contracts\Stage;
use Eppak\Stages\StageBase;
use Illuminate\Support\Facades\File;

class Crontab extends StageBase implements Stage
{

    public function run(array $env = null): bool
    {
        $filename = '/etc/cron.d/panel.crontab';

        $conf = $this->template('panel.crontab');

        $written = File::put($filename, $conf);

        if (!$written) {

            return false;
        }

        $this->command(['crontab', $filename]);

        $this->internal = 'Unable to write crontab';

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
