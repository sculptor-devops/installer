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
            $conf = $this->template('file2ban.conf');

            $this->command(['apt-get', '-y', 'install', 'fail2ban'], false);

            $config = File::put('/etc/fail2ban/jail.local', $conf);

            if (!$config) {
                $this->internal = 'Cannot write configuration';

                return false;
            }

            $restart = $this->daemons->restart('fail2ban');

            if (!$restart) {
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

    /**
     * @return Environment|null
     */
    public function env(): ?Environment
    {
        return null;
    }
}
