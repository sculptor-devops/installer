<?php namespace Eppak\Stages;

use Eppak\Contracts\Runner;
use Eppak\Contracts\RunnerResult;
use Eppak\Services\Daemons;
use Eppak\Services\Templates;
use Illuminate\Support\Facades\File;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalFilesystem;
use Exception;
use ReflectionClass;
use ReflectionException;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class StageBase
{
    /**
     * @var string
     */
    protected $internal = 'Unexpected error see logs for details';

    /**
     * @var int
     */
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

    /**
     * StageBase constructor.
     * @param Runner $runner
     * @param Daemons $daemons
     * @param Templates $templates
     */
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

    /**
     * @param string $name
     * @return string|null
     * @throws \League\Flysystem\FileNotFoundException
     */
    protected function template(string $name)
    {
        return $this->templates->read($name);
    }

    /**
     * @return string|null
     */
    public function error(): ?string
    {
        if ($this->error == null) {

            return $this->internal;
        }

        return $this->error->error();
    }

    /**
     * @param array|null $env
     * @return bool
     * @throws Exception
     */
    public function remove(array $env = null): bool
    {
        throw new Exception("Unimplemented");
    }

    /**
     * @param string $file
     * @param string $content
     * @param string $error
     * @return bool
     */
    protected function write(string $file, string $content, string $error): bool
    {
        $written = File::put($file, $content);

        if (!$written) {
            $this->internal = $error;

            return false;
        }

        return true;
    }

    /**
     * @param bool $short
     * @return string
     * @throws ReflectionException
     */
    public function className(bool $short = true): string
    {
        if ($short) {
            return ((new ReflectionClass($this))->getShortName());
        }

        return ((new ReflectionClass($this))->getName());
    }
}
