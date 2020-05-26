<?php namespace Sculptor\Stages\V1804;

use Sculptor\Contracts\Stage;
use Sculptor\Stages\Environment;
use Sculptor\Stages\StageBase;

use Illuminate\Support\Facades\File;
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

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Ntp';
    }

    /**
     * @return Environment|null
     */
    public function env(): ?Environment
    {
        return null;
    }
}
