<?php

namespace Tests\Stubs;

use Illuminate\Support\Facades\File;
use Mockery;
use Sculptor\Services\Templates as TemplatesService;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Templates
{
    public static function make()
    {
        $templates = Mockery::mock(TemplatesService::class);

        File::shouldReceive('exists')
            ->with(getcwd() . '/' . APP_CONFIG_FILENAME)
            ->andReturnFalse();

        $templates->shouldReceive('read')
            ->with(APP_CONFIG_FILENAME)
            ->andReturn(file_get_contents('tests/Fixtures/' . APP_CONFIG_FILENAME));

        return $templates;
    }
}
