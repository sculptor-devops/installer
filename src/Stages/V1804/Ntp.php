<?php namespace Eppak\Stages\V1804;

use Eppak\Contracts\Stage;
use Eppak\Stages\StageBase;

class Ntp extends StageBase implements Stage
{

    public function run(array $env = null): bool
    {

        /*
 *
 *         $content = "[Time]\nNTP={$ntp}\nFallbackNTP=0.debian.pool.ntp.org 1.debian.pool.ntp.org 2.debian.pool.ntp.org 3.debian.pool.ntp.org\n";
file_put_contents("/etc/systemd/timesyncd.conf", $content);
$this->runner->run(['timedatectl', 'set-ntp', 'true']);
 *
 */

    }

    public function name(): string
    {
        return 'Ntp';
    }

    public function env(): ?array
    {
        return null;
    }
}
