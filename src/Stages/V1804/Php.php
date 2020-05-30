<?php

namespace Sculptor\Stages\V1804;

use Exception;
use Sculptor\Contracts\Stage;
use Sculptor\Stages\Environment;
use Sculptor\Stages\StageBase;
use Illuminate\Support\Facades\Log;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Php extends StageBase implements Stage
{
    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
    {
        $www = APP_PANEL_HTTP_USER;

        $php = $env->get('php');

        try {
            if (
                !$this->write(
                    "/etc/php/{$php}/fpm/conf.d/sculptor.ini",
                    $this->template('php.ini'),
                    'Cannot write ini configuration'
                )
            ) {
                return false;
            }

            $pool = $this->replaceTemplate('php-pool.conf')
                ->replace("{USER}", $www)
                ->value();

            if (!$this->write("/etc/php/{$php}/fpm/pool.d/{$www}.conf", $pool, 'Cannot write pool configuration')) {
                return false;
            }

            if (!$this->restart("php{$php}-fpm")) {
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
     * @return string
     */
    public function name(): string
    {
        return 'Php Cgi';
    }
}
