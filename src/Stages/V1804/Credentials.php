<?php namespace Eppak\Stages\V1804;

use Eppak\Contracts\Stage;
use Eppak\Stages\StageBase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Credentials extends StageBase implements Stage
{
    public function run(array $env = null): bool
    {
        if ($env == null) {
            $env = [];
        }

        $env['ip'] = clearNl($this->runner->run(['dig', '+short', 'ANY', 'myip.opendns.com', '@resolver1.opendns.com'])->output());

        $env['password'] = clearNl($this->runner->run(['openssl', 'rand', '-base64', '20'])->output());

        $env['db_password'] = clearNl($this->runner->run(['openssl', 'rand', '-base64', '16'])->output());

        $this->env = $env;

        $this->internal = 'Generic Error';

        return true;
    }

    public function name(): string
    {
        return "Credentials";
    }

    public function env(): ?array
    {
        return $this->env;
    }
}
