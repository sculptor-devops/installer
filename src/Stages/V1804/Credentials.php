<?php namespace Sculptor\Stages\V1804;

use Sculptor\Contracts\Stage;
use Sculptor\Stages\Environment;
use Sculptor\Stages\StageBase;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Credentials extends StageBase implements Stage
{
    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
    {
        try {

            $this->internal = 'Generic Error';

            $ip = quoteContent($this->get([
                'dig',
                '-4',
                'TXT',
                '+short',
                'o-o.myaddr.l.google.com',
                '@ns1.google.com'
            ]));

            $password = $this->get(['openssl', 'rand', '-base64', '20']);

            $dbPassword = $this->get(['openssl', 'rand', '-base64', '16']);

            $env->add('ip', $ip);

            if (!$env->has('password')) {
                $env->add('password', $password);
            }

            if (!$env->has('db_password')) {
                $env->add('db_password', $dbPassword);
            }

            if ($this->daemons->installed('mysql-server')) {
                $this->internal = 'Machine seem to be already installed (mysql server at least is present)';

                return false;
            }

            return true;

        } catch (Exception $e) {

            Log::error($e->getMessage());

            return false;
        }
    }

    /**
     * @param array<string> $command
     * @return string
     */
    private function get(array $command): string
    {
        return clearNewLine($this->runner->run($command)->output());
    }

    /**
     * @return string
     */
    private function ip(): string
    {
        $ip = quoteContent($this->get([
            'dig',
            '-4',
            'TXT',
            '+short',
            'o-o.myaddr.l.google.com',
            '@ns1.google.com'
        ]));

        if ($ip == null || $ip == '') {
            $ip = quoteContent($this->get([
                'dig',
                '-6',
                'TXT',
                '+short',
                'o-o.myaddr.l.google.com',
                '@ns1.google.com'
            ]));
        }

        return $ip;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return "Credentials";
    }
}
