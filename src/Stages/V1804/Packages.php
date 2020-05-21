<?php namespace Eppak\Stages\V1804;

use Exception;
use Eppak\Contracts\Stage;
use Eppak\Stages\StageBase;
use Illuminate\Support\Facades\Log;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Packages extends StageBase implements Stage
{
    private $packages = [
        'apt-get',
        '-y',
        'install',
        'zip',
        'unzip',
        'openssl',
        'curl',
        'dirmngr',
        'apt-transport-https',
        'lsb-release',
        'ca-certificates',
        'dnsutils',
        'htop',
        'apt-utils',
        'debconf-utils',
        'supervisor',
        'sudo'
    ];

    public function run(array $env = null): bool
    {
        try {

            $this->command(['apt-get', 'update'], false);

            $this->command($this->packages, false);

            $this->command(['apt-get', '-y', 'autoremove'], false);

            return true;

        } catch (Exception $e) {

            Log::error($e->getMessage());

            return false;
        }
    }

    public function name(): string
    {
        return 'Base Packages';
    }

    public function env(): ?array
    {
        return null;
    }
}
