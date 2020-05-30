<?php

namespace Sculptor\Stages\V1804;

use Exception;
use Sculptor\Contracts\Stage;
use Sculptor\Stages\Environment;
use Sculptor\Stages\StageBase;
use Illuminate\Support\Facades\Log;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Ntp extends StageBase implements Stage
{

    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
    {
        try {
            if (
                !$this->write(
                    '/etc/systemd/timesyncd.conf',
                    $this->template('ntp.conf'),
                    'Unable to write configuration'
                )
            ) {
                return false;
            }

            $this->command(['timedatectl', 'set-ntp', 'true']);

            $this->command(['timedatectl', 'set-timezone', 'UTC']);

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
        return 'Ntp';
    }
}
