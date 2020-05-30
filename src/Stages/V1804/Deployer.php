<?php

namespace Sculptor\Stages\V1804;

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

class Deployer extends StageBase implements Stage
{

    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
    {
        try {
            $setup = '/tmp/deployer.phar';

            $copy = copy('https://deployer.org/deployer.phar', $setup);

            if (!$copy) {
                $this->internal = "Unable to download setup";

                return false;
            }

            $this->command(['mv', $setup, '/usr/local/bin/dep']);

            $this->command(['chmod', '+x', '/usr/local/bin/dep']);

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
        return "Deployer";
    }
}
