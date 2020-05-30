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

class Composer extends StageBase implements Stage
{
    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
    {
        try {
            $setup = '/tmp/composer-setup.php';

            $copy = copy('https://getcomposer.org/installer', $setup);

            if (!$copy) {
                $this->internal = "Unable to download setup";

                return false;
            }

            $this->command(['php', $setup, '--install-dir=/bin', '--filename=composer']);

            $delete = unlink($setup);

            if (!$delete) {
                $this->internal = "Unable to delete setup";

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
        return 'Composer';
    }
}
