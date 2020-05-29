<?php namespace Sculptor\Stages\V1804;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

use Sculptor\Contracts\Stage;
use Sculptor\Stages\Environment;
use Sculptor\Stages\StageBase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Sshd extends StageBase implements Stage
{
    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
    {
        try {

            if (!$this->write('/etc/ssh/sshd_config',
                $this->template('sshd.conf'),
                'Cannot read configuration')) {

                return false;
            }

            if (!$this->daemons->restart('sshd')) {
                $this->internal = 'Cannot restart service';

                return false;
            }

            return true;

        } catch (Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'SSH Daemon';
    }
}
