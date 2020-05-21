<?php namespace Eppak\Stages\V1804;

use Eppak\Contracts\Stage;
use Eppak\Stages\StageBase;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Credentials extends StageBase implements Stage
{
    public function run(array $env = null): bool
    {
        try {


            $this->internal = 'Generic Error';

            if ($env == null) {
                $env = [];
            }

            $ip = quoted($this->get([
                'dig',
                '-4',
                'TXT',
                '+short',
                'o-o.myaddr.l.google.com',
                '@ns1.google.com'
            ]));

            $password = $this->get(['openssl', 'rand', '-base64', '20']);

            $dbPassword = $this->get(['openssl', 'rand', '-base64', '16']);

            $env = $this->push('ip', $ip, $env);

            $env = $this->push('password', $password, $env);

            $env = $this->push('db_password', $dbPassword, $env);

            $this->env = $env;

            return true;

        } catch (Exception $e) {

            Log::error($e->getMessage());

            return false;
        }
    }

    private function get(array $command): string
    {
        return clearNl($this->runner->run($command)->output());
    }

    private function push(string $key, string $value, array $env): array
    {
        if (!array_key_exists($key, $env)) {
            $env[$key] = $value;
        }

        return $env;
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
