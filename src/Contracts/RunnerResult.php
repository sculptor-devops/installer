<?php namespace Sculptor\Contracts;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

interface RunnerResult
{
    public function success(): bool;
    public function output(): string;
    public function error(): string;
    public function code(): int;
}
