<?php namespace Sculptor\Stages\V1804;

use Sculptor\Stages\Environment;
use Sculptor\Stages\StageBase;
use Sculptor\Contracts\Stage;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Firewall extends StageBase implements Stage
{
    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
    {
        try {

            $this->command(['apt-get', '-y', 'install', 'fail2ban'], false);

            if (!$this->write('/etc/fail2ban/jail.local',
                $this->template('file2ban.conf'),
                'Cannot write configuration')) {

                return false;
            }

            if (!$this->daemons->restart('fail2ban')) {
                $this->internal = 'Cannot restart service';

                return false;
            }

            $this->command(['ufw', '--force', 'enable']);

            $this->command(['ufw', 'allow', 'ssh']);

            $this->command(['ufw', 'allow', 'http']);

            $this->command(['ufw', 'allow', 'https']);

            $this->command(['ufw', 'allow', 'Nginx Full']);

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
        return 'Firewall';
    }
}
