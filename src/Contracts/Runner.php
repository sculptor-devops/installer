<?php namespace Sculptor\Contracts;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

interface Runner
{
    /**
     * @return Runner
     */
    public function tty(): Runner;

    /**
     * @param int|null $timeout
     * @return Runner
     */
    public function timeout(?int $timeout): Runner;

    /**
     * @param string $path
     * @return Runner
     */
    public function from(string $path): Runner;

    /**
     * @param string $input
     * @return Runner
     */
    public function input(string $input): Runner;

    /**
     * @param array<string, string> $export
     * @return Runner
     */
    public function env(array $export): Runner;

    /**
     * @param array<int, int|string> $command
     * @return RunnerResult
     */
    public function run(array $command): RunnerResult;
}
