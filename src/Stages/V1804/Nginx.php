<?php namespace Eppak\Stages\V1804;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Eppak\Contracts\Stage;
use Eppak\Stages\StageBase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Nginx extends StageBase implements Stage
{
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

            if (!File::exists('/var/www/html/public')) {
                $root = File::makeDirectory('/var/www/html/public', 0755, true);

            }

            if (!$root) {
                $this->internal = 'Cannot create www root';

                return false;
            }

            $index = $this->template('index.html');

            $written = File::put('/var/www/html/public/index.html', $index);

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

    public function name(): string
    {
        return 'Nginx';
    }

    public function env(): ?array
    {
        return null;
    }
}
