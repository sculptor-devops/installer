<?php


namespace Eppak\Stages\V1804;


use Eppak\Contracts\Stage;
use Eppak\Stages\StageBase;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Php extends StageBase implements Stage
{
    public function run(array $env = null): bool
    {
        $php = '7.4';

        try {
            $conf = $this->template('php.ini');

            if (!$this->write("/etc/php/{$php}/fpm/conf.d/sculptor.ini", $conf, 'Cannot write ini configuration')) {
                return false;
            }

            $pool = $this->template('php-pool.conf');

            $pool = str_replace("{USER}", APP_PANEL_USER, $pool);

            if (!$this->write("/etc/php/{$php}/fpm/pool.d/sculptor.conf", $pool, 'Cannot write pool configuration')) {
                return false;
            }

            $restart = $this->daemons->restart("php{$php}-fpm");

            if (!$restart) {
                $this->internal = 'Cannot restart service';

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
        return 'Php Cgi';
    }

    public function env(): ?array
    {
        return null;
    }
}
