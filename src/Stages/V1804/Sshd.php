<?php namespace Eppak\Stages\V1804;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Eppak\Contracts\Stage;
use Eppak\Stages\StageBase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Sshd extends StageBase implements Stage
{
    public function run(array $env = null): bool
    {
        try {
            $config = $this->template('sshd.conf');

            $written = File::put('/etc/ssh/sshd_config', $config);

            if (!$written) {
                $this->internal = 'Cannot read configuration';

                return false;
            }

            $restart = $this->daemons->restart('sshd');

            if (!$restart) {
                $this->internal = 'Cannont restart service';

                return false;
            }

            return true;

        } catch (Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    public function name(): string
    {
        return 'SSH Daemon';
    }

    public function env(): ?array
    {
        return null;
    }
}
