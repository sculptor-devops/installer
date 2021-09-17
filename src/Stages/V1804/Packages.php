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

class Packages extends StageBase implements Stage
{
    /**
     * @var string
     */
    private $service = 'unattended-upgrades';

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
        'sudo',
        'ufw',
        'git',
        'unattended-upgrades',
        'sysstat'
    ];

    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
    {
        try {
            $this->noninteractive();

            $this->command(['apt-get', 'update']);

            $this->command($this->packages);

            $this->command(['apt-get', '-y', 'autoremove']);

            if (
                !$this->write(
                    "/etc/apt/apt.conf.d/20auto-upgrades",
                    $this->template('20auto-upgrades.conf')
                )
            ) {
                return false;
            }

            if (!$this->enable($this->service)) {
                return false;
            }
            
            if (!$this->restart($this->service)) {
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
        return 'Base Packages';
    }
}
