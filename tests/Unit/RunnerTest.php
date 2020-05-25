<?php namespace Tests\Unit;

use Sculptor\Exceptions\PathNotFoundException;
use Sculptor\Runner\Runner;
use Tests\TestCase;

class RunnerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRunnerExistent()
    {
        $runner = new Runner();
        $result = $runner->from('/tmp')->run([ 'ls'] );

        $this->assertTrue($result->success());
        $this->assertTrue($result->code() == 0);
    }

    public function testRunnerNotExistent()
    {
        $runner = new Runner();
        $result = $runner->from('/tmp')->run([ 'notexistent'] );

        $this->assertFalse($result->success());
        $this->assertTrue($result->code() == 127);
    }

    public function testRunnerPathNotExistent()
    {
        $this->expectException(PathNotFoundException::class);

        $runner = new Runner();
        $runner->from('/tmp-not-Exists')->run([ 'notexistent'] );
    }
}
