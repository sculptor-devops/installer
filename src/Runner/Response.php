<?php namespace Sculptor\Runner;

use Sculptor\Contracts\RunnerResult;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Response implements RunnerResult
{
    private $success;
    private $error;
    private $code;
    private $output;

    public function __construct(bool $success, string $output, int $code = 0, string $error = null)
    {
        $this->success = $success;
        $this->output = $output;
        $this->code = $code;
        $this->error = $error;
    }

    public function success(): bool
    {
        return $this->success;
    }

    public function error(): string
    {
        return $this->error;
    }

    public function code(): int
    {
        return $this->code;
    }

    public function output(): string
    {
        return $this->output;
    }
}
