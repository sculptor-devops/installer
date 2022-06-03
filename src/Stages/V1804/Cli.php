<?php

namespace Sculptor\Stages\V1804;

use Exception;
use League\Flysystem\FileNotFoundException;
use Sculptor\Contracts\Stage;
use Sculptor\Stages\Environment;
use Sculptor\Stages\StageBase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Cli extends StageBase implements Stage
{
    /**
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function run(Environment $env): bool
    {
        $cli = '/bin/sculptor';

        $copy = copy('https://github.com/sculptor-devops/sculptor-cli/releases/latest/download/sculptor', $cli);

        if (!$copy) {
            $this->internal = "Unable to download sculptor client";

            return false;
        }

        $this->command(['sculptor', 'sculptor:init']);

        return true;
    }

    public function name(): string
    {
        return 'Client';
    }
}
