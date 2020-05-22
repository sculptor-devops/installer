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
    public function testList()
    {
        $stages = resolve(Stages::class);

        $list = $stages->list();

        $this->assertTrue(count($list) > 0);
    }
}
