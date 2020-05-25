<?php namespace Sculptor\Stages\V1804;

use Sculptor\Contracts\Stage;
use Sculptor\Stages\StageBase;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

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

            Log::error($e->getMessage());

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
