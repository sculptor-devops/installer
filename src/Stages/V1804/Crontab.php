<?php

namespace Sculptor\Stages\V1804;

use League\Flysystem\FileNotFoundException;
use Sculptor\Contracts\Stage;
use Sculptor\Stages\Environment;
use Sculptor\Stages\StageBase;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Crontab extends StageBase implements Stage
{
    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
    {
        try {
            foreach ([
                         // panel.crontab' => ['path' => '/etc/cron.d/sculptor.admin', 'user' => APP_PANEL_USER],
                         // 'www-data.crontab' => ['path' => '/etc/cron.d/sculptor.www', 'user' => APP_PANEL_HTTP_PANEL],
                         'cli.crontab' => ['path' => '/etc/cron.d/sculptor.cli', 'user' => 'root'] /* whoami()*/
                     ] as $name => $value) {

                if (!$this->add($name, $value['path'], $value['user'])) {
                    return false;
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    /**
     * @param string $filename
     * @param string $destination
     * @param string $user
     * @return bool
     * @throws FileNotFoundException
     * @throws Exception
     */
    private function add(string $filename, string $destination, string $user): bool
    {
        if (!$this->write($destination, $this->template($filename), "Cannot write to {$destination}")) {
            return false;
        }

        $this->command(['crontab', '-u', $user, $destination]);

        return true;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Crontab';
    }
}
