<?php namespace Sculptor\Stages\V1804;

use Sculptor\Contracts\Stage;
use Sculptor\Stages\StageBase;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Redis extends StageBase implements Stage
{
    public function run(array $env = null): bool
    {
        try {
            $this->command(['apt', '-y', 'install', 'redis-server']);

            $enabled = $this->daemons->enable('redis-server.service');

            if (!$enabled) {
                $this->internal = 'Cannot enable service';

                return false;
            }

            $restarted = $this->daemons->restart('redis-server.service');

            if (!$restarted) {
                $this->internal = 'Cannot restart service';

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
        return 'Redis Server';
    }

    public function env(): ?array
    {
        return null;
    }
}
