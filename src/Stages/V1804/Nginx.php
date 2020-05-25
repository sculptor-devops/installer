<?php namespace Sculptor\Stages\V1804;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

use Sculptor\Contracts\Stage;
use Sculptor\Stages\StageBase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Nginx extends StageBase implements Stage
{
    private $path = '/var/www/html/current/public';

    public function run(array $env = null): bool
    {
        try {
            $conf = $this->template('nginx.conf');

            $this->command(['apt-get', '-y', 'install', 'nginx']);

            $enable = $this->daemons->enable('nginx.service');

            if (!$enable) {
                $this->internal = 'Cannot enable service';

                return false;
            }

            $this->ssl();

            $config = File::put('/etc/nginx/sites-available/default', $conf);

            if (!$config) {
                $this->internal = 'Cannot write to configuration';

                return false;
            }

            $restart = $this->daemons->restart('nginx.service');

            if (!$restart) {
                $this->internal = 'Cannot restart service';

                return false;
            }

            $root = true;

            if (!File::exists($this->path)) {
                $root = File::makeDirectory($this->path, 0755, true);
            }

            if (!$root) {
                $this->internal = 'Cannot create www root';

                return false;
            }

            $index = $this->template('index.html');

            $written = File::put("{$this->path}/index.html", $index);

            if (!$written) {
                $this->internal = 'Cannot create index file';

                return false;
            }

            return true;

        } catch (Exception $e) {

            Log::error($e->getMessage());

            return false;
        }
    }

    private function ssl()
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

    public function name(): string
    {
        return 'Nginx';
    }

    public function env(): ?array
    {
        return null;
    }
}
