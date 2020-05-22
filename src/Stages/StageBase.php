<?php namespace Eppak\Stages;

use Eppak\Contracts\Runner;
use Eppak\Contracts\RunnerResult;
use Eppak\Services\Daemons;
use Eppak\Services\Templates;
use Illuminate\Support\Facades\File;
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

    protected $timeout = 3600;

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
    /**
     * @var Templates
     */
    private $templates;

    public function __construct(Runner $runner, Daemons $daemons, Templates $templates)
    {
        $this->runner = $runner;

        $this->daemons = $daemons;

        $this->templates = $templates;
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
        return $this->templates->read($name);
    }

    public function error(): ?string
    {
        if ($this->error == null) {

            return $this->internal;
        }

        return $this->error->error();
    }

    public function remove(array $env = null): bool
    {
        throw new Exception("Unimplemented");
    }

    protected function write(string $file, string $content, string $error): bool
    {
        $written = File::put($file, $content);

        if (!$written) {
            $this->internal = $error;

            return false;
        }

        return true;
    }
}
