<?php namespace Eppak\Stages\V1804;

use Eppak\Contracts\Stage;
use Eppak\Stages\StageBase;
use Illuminate\Support\Facades\File;

class Ntp extends StageBase implements Stage
{

    public function run(array $env = null): bool
    {
        try {
            $conf = $this->template('ntp.conf');

            $written = File::put('/etc/systemd/timesyncd.conf', $conf);

            if (!$written) {
                $this->internal = 'Unable to write configuration';

                return false;
            }

            $this->command(['timedatectl', 'set-ntp', 'true']);

            $this->command(['timedatectl', 'set-timezone', 'UTC']);

            return true;

        } catch (\Exception $e) {

            return false;
        }
    }

    public function name(): string
    {
        return 'Ntp';
    }

    public function env(): ?array
    {
        return null;
    }
}
