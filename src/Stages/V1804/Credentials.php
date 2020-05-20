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
        $this->internal = 'Generic Error';

        if ($env == null) {
            $env = [];
        }

        if (!array_key_exists('ip', $env)) {
            // Alternative ['dig', '+short', 'ANY', 'myip.opendns.com', '@resolver1.opendns.com']

            $env['ip'] = quoted(clearNl($this->runner->run(['dig', '-4', 'TXT', '+short', 'o-o.myaddr.l.google.com', '@ns1.google.com'])->output()));
        }

        if (!array_key_exists('password', $env)) {
            $env['db_password'] = clearNl($this->runner->run(['openssl', 'rand', '-base64', '20'])->output());
        }

        if (!array_key_exists('ip', $env)) {
            $env['db_password'] = clearNl($this->runner->run(['openssl', 'rand', '-base64', '16'])->output());
        }

        $this->env = $env;

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
