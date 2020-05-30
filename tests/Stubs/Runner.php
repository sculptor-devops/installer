<?php

namespace Tests\Stubs;

use Mockery;
use Sculptor\Foundation\Contracts\Response;
use Sculptor\Foundation\Contracts\Runner as RunnerInterface;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Runner
{
    public static function success($runner, $command, $output)
    {
        if ($runner == null) {
            $runner = Mockery::mock(RunnerInterface::class);
        }

        $response = Mockery::mock(Response::class);

        $runner->shouldReceive('run')->with($command)->andReturn($response);

        $response->shouldReceive('success')->andReturn(true);

        $response->shouldReceive('code')->andReturn(0);

        $response->shouldReceive('error')->andReturn('');

        $response->shouldReceive('output')->andReturn($output);

        return $runner;
    }

    public static function error($runner, $command, $output)
    {
        if ($runner == null) {
            $runner = Mockery::mock(RunnerInterface::class);
        }

        $response = Mockery::mock(Response::class);

        $runner->shouldReceive('run')->with($command)->andReturn($response);

        $response->shouldReceive('success')->andReturn(false);

        $response->shouldReceive('code')->andReturn(1);

        $response->shouldReceive('error')->andReturn($output);

        $response->shouldReceive('output')->andReturn('');

        return $runner;
    }
}
