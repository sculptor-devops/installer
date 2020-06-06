<?php

namespace Sculptor\Stages\V1804;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Sculptor\Contracts\Stage;
use Sculptor\Stages\Environment;
use Sculptor\Stages\StageBase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Nginx extends StageBase implements Stage
{
    /**
     * @var string
     */

    private $path = '/var/www/html/current/public';

    /**
     * @var string
     */
    private $default = '/var/www/default';

    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
    {
        try {
            $port = $env->get('port');

            $conf = $this->replaceTemplate('nginx.conf')
                ->replace('{PORT}', $port)
                ->replace('{USER}', APP_PANEL_HTTP_PANEL)
                ->value();

            $this->command(['apt-get', '-y', 'install', 'nginx']);

            if (!$this->enable('nginx.service')) {
                $this->internal = 'Cannot enable service';

                return false;
            }

            $this->ssl();

            $this->roots();

            if (
                !$this->write(
                    '/etc/nginx/sites-available/default',
                    $conf,
                    'Cannot write to configuration'
                )
            ) {
                return false;
            }

            if (!$this->restart('nginx.service')) {
                $this->internal = 'Cannot restart service';

                return false;
            }

            return true;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    /**
     * @throws Exception
     */
    private function ssl(): void
    {
        $path = '/etc/nginx/ssl';

        if (!File::exists($path)) {
            File::makeDirectory($path);
        }

        $this->command([
            'openssl',
            'req',
            '-x509',
            '-nodes',
            '-days',
            '365',
            '-newkey',
            'rsa:2048',
            '-subj',
            '/CN=localhost',
            '-keyout',
            "{$path}/self-signed.key",
            '-out',
            "{$path}/self-signed.crt"
        ]);
    }

    private function roots(): bool
    {
        $index = $this->template('index.html');

        foreach ([ $this->path, $this->default ] as $www) {
            $created = true;

            if (!File::exists($www)) {
                $created = File::makeDirectory($www, 0755, true);
            }

            if (!$created) {
                $this->internal = "Cannot create www root {$www}";

                return false;
            }

            if (!$this->write("{$www}/index.html", $index, "Cannot create index file in {$www}")) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Nginx';
    }
}
