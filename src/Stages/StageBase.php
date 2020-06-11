<?php

namespace Sculptor\Stages;

use Illuminate\Support\Facades\Log;
use Sculptor\Exceptions\CommandErrorException;
use Sculptor\Foundation\Contracts\Response;
use Sculptor\Foundation\Contracts\Runner;
use Sculptor\Foundation\Contracts\Database;
use Sculptor\Foundation\Services\Daemons;
use Sculptor\Foundation\Services\Firewall;
use Sculptor\Foundation\Support\Replacer;
use Sculptor\Services\Templates;
use Illuminate\Support\Facades\File;
use League\Flysystem\FileNotFoundException;
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
     * @var bool
     */
    private $noninteractive = false;
    /**
     * @var string
     */
    protected $internal = 'Unexpected error see logs for details';

    /**
     * @var int
     */
    protected $timeout = 3600;

    /**
     * @var Response
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
     * @var Templates
     */
    private $templates;
    /**
     * @var Database
     */
    protected $db;
    /**
     * @var Firewall
     */
    protected $firewall;

    /**
     * StageBase constructor.
     * @param Runner $runner
     * @param Daemons $daemons
     * @param Templates $templates
     * @param Database $db
     * @param Firewall $firewall
     */
    public function __construct(Runner $runner, Daemons $daemons, Templates $templates, Database $db, Firewall $firewall)
    {
        $this->runner = $runner;

        $this->daemons = $daemons;

        $this->templates = $templates;

        $this->firewall = $firewall;

        $this->db = $db;
    }

    /**
     * @param array<string> $commands
     * @param string|null $path
     * @return bool
     * @throws Exception
     */
    protected function command(array $commands, string $path = null): bool
    {
        $process = $this->runner
            ->timeout($this->timeout);

        if ($this->noninteractive) {
            $process = $process->env(['DEBIAN_FRONTEND' => 'noninteractive']);
        }

        if ($path) {
            $process = $process->from($path);
        }

        $result = $process->run($commands);

        if (!$this->dump($result)->success()) {
            throw new CommandErrorException($result->error());
        }

        return true;
    }

    /**
     *
     */
    protected function noninteractive(): void
    {
        $this->noninteractive = true;
    }

    /**
     * @param string $name
     * @return string
     * @throws FileNotFoundException
     */
    protected function template(string $name): string
    {
        return $this->templates->read($name);
    }

    /**
     * @param string $name
     * @return Replacer
     * @throws FileNotFoundException
     */
    protected function replaceTemplate(string $name): Replacer
    {
        $template = $this->template($name);

        return new Replacer($template);
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
     * @param string $file
     * @param string $content
     * @param string $error
     * @return bool
     */
    protected function write(string $file, string $content, string $error = null): bool
    {
        $written = File::put($file, $content);

        if ($error == null) {
            $error = "Error writing file {$file}";
        }

        if (!$written) {
            $this->internal = $error;

            return false;
        }

        return true;
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    public function className(): string
    {
        // return ((new ReflectionClass($this))->getName());

        return ((new ReflectionClass($this))->getShortName());
    }

    /**
     * @param int $len
     * @return string
     */
    public function password(int $len = 16): string
    {
        return clearNewLine($this->runner->run(['openssl', 'rand', '-base64', $len])->output());
    }

    /**
     * @param array<string> $command
     * @return string
     * @throws Exception
     */
    public function output(array $command): string
    {
        $result = $this->runner->run($command);

        if (!$this->dump($result)->success()) {
            throw new Exception($result->error());
        }

        return $result->output();
    }

    /**
     * @param Response $result
     * @return Response
     */
    private function dump(Response $result): Response
    {
        if (!$result->success()) {
            $this->error = $result;

            Log::error("Command: {$this->runner->line()}");
            Log::error("Error: {$result->error()}");
            Log::error("Error output: {$result->output()}");
            Log::error("Error code: {$result->code()}");
        }

        return $result;
    }

    /**
     * @param string $name
     * @param string $error
     * @return bool
     */
    protected function enable(string $name, string $error = null): bool
    {
        if ($error == null) {
            $error = "Error enabling service {$name}";
        }

        if (!$this->daemons->enable($name)) {
            $this->internal = $error;

            return false;
        }

        return true;
    }

    /**
     * @param string $name
     * @param string $error
     * @return bool
     */
    protected function restart(string $name, string $error = null): bool
    {
        if ($error == null) {
            $error = "Error restarting service {$name}";
        }
        
        if (!$this->daemons->restart($name)) {
            $this->internal = $error;

            return false;
        }

        return true;
    }
}
