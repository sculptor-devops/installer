<?php namespace Sculptor\Stages\V1804;

use Sculptor\Contracts\Stage;
use Sculptor\Stages\StageBase;

use Exception;
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
        $www = APP_PANEL_HTTP_USER;
        $php = APP_PANEL_PHP_VERSION;

        try {
            $conf = $this->template('php.ini');

            if (!$this->write("/etc/php/{$php}/fpm/conf.d/sculptor.ini", $conf, 'Cannot write ini configuration')) {
                return false;
            }

            $pool = $this->replaceTemplate('php-pool.conf')
                ->replace("{USER}", $www)
                ->value();

            if (!$this->write("/etc/php/{$php}/fpm/pool.d/{$www}.conf", $pool, 'Cannot write pool configuration')) {
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
