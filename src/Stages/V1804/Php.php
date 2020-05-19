<?php


namespace Eppak\Stages\V1804;


use Eppak\Contracts\Stage;
use Eppak\Stages\StageBase;
use Illuminate\Support\Facades\File;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Php extends StageBase implements Stage
{
    public function run(array $env = null): bool
    {
        $conf = $this->template('php.ini');

        $written = File::put('/etc/php/7.4/fpm/conf.d/cipi.ini', $conf);

        if(!$written) {
            $this->internal = 'Cannot write to configuration';

            return false;
        }

        $restart = $this->daemons->restart('php7.4-fpm');

        if(!$restart) {
            $this->internal = 'Cannot restart service';

            return false;
        }


        return true;
    }

    public function name(): string
    {
        return 'Php Cgi';
    }

    public function env(): ?array
    {
        return null;
    }
}
