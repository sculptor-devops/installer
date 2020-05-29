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
     * @var array<string>
     */
    private $ports = [ 'ssh', 'http', 'https', 'Nginx Full' ];

    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
    {
        try {
            $port = $env->get('port');

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

            if (!$this->firewall->enable()) {
                $this->internal = 'Cannot enable firewall';

                return false;
            }

            if($port != APP_PANEL_HTTP_PORT) {
                $this->firewall->allow($port, true);
            }

            foreach ($this->ports as $port) {
                if (!$this->allow($port)) {

                    return false;
                }
            }

            return true;

        } catch (Exception $e) {

            Log::error($e->getMessage());

            return false;
        }
    }

    /**
     * @param string $port
     * @return bool
     */
    private function allow(string $port): bool
    {
        if (!$this->firewall->allow($port)) {

            $this->internal = "Cannot enable firewall rule on {$port}";

            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Firewall';
    }
}
