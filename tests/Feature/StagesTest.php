<?php

namespace Tests\Feature;

use Eppak\Stages;
use Tests\TestCase;

class StagesTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testListCount()
    {
        $stages = resolve(Stages::class);

        $list = $stages->list();

        $this->assertTrue(count($list) > 0);
    }

    public function testSingleStage()
    {
        $this->artisan('run-stage', [ '--step' => 'credentials'])->assertExitCode(0);
    }

    public function testAllStages()
    {
        $this->artisan('run')->assertExitCode(0);
    }
}
