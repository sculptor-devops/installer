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
    private array $crontabs = [
        'cli.crontab' => ['path' => '/etc/cron.d/sculptor.cli', 'user' => 'root'] /* whoami()*/
    ];

    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
    {
        try {
            foreach ($this->crontabs as $name => $crontab) {
                if (!$this->add("$name", $crontab['path'], $crontab['user'])) {
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
