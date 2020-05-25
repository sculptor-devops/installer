<?php namespace Sculptor\Runner;

// composer require spatie/ssh

use Sculptor\Contracts\RunnerResult;
use Sculptor\Contracts\Runner as RunnerInterface;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Ssh implements RunnerInterface
{
    /**
     * @var string
     */
    private $input = null;

    /**
     * @var bool
     */
    private $useTty = false;
    /**
     * @var null
     */
    private $path = null;
    /**
     * @var int
     */
    private $timeout = 60;
    /**
     * @return $this|RunnerInterface
     */
    public function tty(): RunnerInterface
    {
        $this->useTty = true;

        return $this;
    }

    /**
     * @param string $path
     * @return $this|RunnerInterface
     * @throws PathNotFoundException
     */
    public function from(string $path): RunnerInterface
    {
        $this->path = $path;

        if (!file_exists($path)) {
            throw new PathNotFoundException($path);
        }

        return $this;
    }

    /**
     * @param int|null $timeout
     * @return $this|RunnerInterface
     */
    public function timeout(?int $timeout): RunnerInterface
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @param array $command
     * @return RunnerResult
     */
    public function run(array $command): RunnerResult
    {
        $line = join(' ', $command);

        Log::info("RUNNING COMMAND: {$line}");

        try {
            $process = Ssh::create('user', 'host', 22)
                ->usePrivateKey('/home/user/.ssh/id_rsa')
                ->disableStrictHostKeyChecking()
                ->configureProcess(function (Process $process) {

                    $process->setTimeout($this->timeout);

                    if ($this->useTty) {
                        $process->setTty(true);
                    }

                    if ($this->input) {
                        $process->setInput($this->input);
                    }
                })->execute($command);

            return $this->response($process->isSuccessful(), $process);

        } catch (ProcessFailedException $exception) {
            return $this->response(false, $process);
        }
    }

    /**
     * @param bool $status
     * @param Process $process
     * @return Response
     */
    private function response(bool $status, Process $process): Response
    {
        if (!$status) {
            Log::error("Error stdout: {$process->getOutput()}");
            Log::error("Error code: {$process->getExitCode()}");
            Log::error("Error: {$process->getErrorOutput()}");
        }

        return new Response(
            $status,
            $process->getOutput(),
            $process->getExitCode(),
            $process->getErrorOutput()
        );
    }

    public function input(string $input): RunnerInterface
    {
        $this->input = $input;

        return $this;
    }

    public function env(array $export): RunnerInterface
    {
        // TODO: Implement env() method.
    }
}
