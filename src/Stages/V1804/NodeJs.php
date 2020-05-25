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

class NodeJs extends StageBase implements Stage
{
    public function run(array $env = null): bool
    {
        try {
            $setup = '/tmp/setup_12.x';

            $copy = copy('https://deb.nodesource.com/setup_12.x', $setup);

            if (!$copy) {
                $this->internal = "Unable to download setup";

                return false;
            }

            $this->command(['sh', '/tmp/setup_12.x'], false);

            $this->command(['apt-get', 'install', '-y', 'nodejs']);

            $delete = unlink($setup);

            if (!$delete) {
                $this->internal = "Unable to delete setup";

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
        return 'NodeJs';
    }

    public function env(): ?array
    {
        return null;
    }
}
