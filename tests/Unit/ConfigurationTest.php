<?php

namespace Tests\Unit;

use League\Flysystem\FileNotFoundException;
use Sculptor\Services\Configuration;
use Tests\Stubs\Templates;
use Tests\TestCase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class ConfigurationTest extends TestCase
{
    /**
     * @throws FileNotFoundException
     */
    public function testProperties()
    {
        $configuration = new Configuration(Templates::make());

        $this->assertCount(2, $configuration->stages());

        $this->assertEquals('7.2', $configuration->php());

        $this->assertEquals('custom', $configuration->user());

        $this->assertEquals('1234', $configuration->port());

        $this->assertEquals('dbPassword', $configuration->dbPassword());

        $this->assertEquals('password', $configuration->password());        
    }
}
