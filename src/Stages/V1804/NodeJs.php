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

class NodeJs extends StageBase implements Stage
{
    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
    {
        try {
            $this->noninteractive();

            $this->install($env->get('node_version'));

            return true;
        } catch (Exception $e) {
            $this->internal = $e->getMessage();

            Log::error($e->getMessage());

            return false;
        }
    }

    /**
     * @param string $version
     * @return void
     */
    private function install(string $version): void
    {
        Log::info("     Installing node js version {$version}");

        $setup = "/tmp/setup_{$version}.x";

        if (!copy("https://deb.nodesource.com/setup_{$version}.x", $setup)) {
            throw new Exception("Unable to download setup");
        }

        $this->command(['sh', $setup]);

        $this->command(['apt-get', 'install', '-y', 'nodejs']);

        if (!unlink($setup)) {
            throw new Exception("Unable to delete setup");
        }
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'NodeJs';
    }
}
