<?php namespace Sculptor\Stages\V1804;

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

class Packages extends StageBase implements Stage
{
    /**
     * @var string[]
     */
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

    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
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

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Base Packages';
    }

    /**
     * @return Environment|null
     */
    public function env(): ?Environment
    {
        return null;
    }
}
