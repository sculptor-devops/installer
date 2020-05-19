<?php namespace Eppak\Stages;

use Eppak\Contracts\Runner;
use Eppak\Contracts\RunnerResult;
use Eppak\Services\Daemons;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalFilesystem;
use Exception;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class StageBase
{
    protected $internal = 'Unexpected error see logs for details';

    protected $timeout = 600;

    /**
     * @var RunnerResult
     */
    protected $error;
    /**
     * @var Runner
     */
    protected $runner;
    /**
     * @var Daemons
     */
    protected $daemons;
    /**
     * @var array
     */
    protected $env;

    public function __construct(Runner $runner, Daemons $daemons)
    {
        $this->runner = $runner;
        $this->daemons = $daemons;
    }

    /**
     * @param array $commands
     * @param bool $interactive
     * @return bool
     * @throws Exception
     */
    protected function command(array $commands, bool $interactive = true): bool
    {
        $process = $this->runner
            ->timeout($this->timeout);

        if (!$interactive) {
            $process = $process->env([ 'DEBIAN_FRONTEND' => 'noninteractive' ]);
        }

        $result = $process->run($commands);

        if (!$result->success()) {
            $this->error = $result;

            throw new Exception($result->error());
        }

        return true;
    }

    protected function template(string $name)
    {
        $adapter = new LocalFilesystem(base_path('templates'));

        return (new Filesystem($adapter))->read($name);
    }

    public function error(): ?string
    {
        if ($this->error == null) {

            return $this->internal;
        }

        return $this->error->error();
    }
}
