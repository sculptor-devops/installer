<?php

namespace Tests\Stubs;

use Sculptor\Foundation\Support\Version as FoundationVersion;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Version
{
    public static function make()
    {
        Env::make();

        $runner = Runner::success(null, ['uname', '-m'], 'x86_64');

        $runner = Runner::success($runner, ['getconf', 'LONG_BIT'], '64');

        return new FoundationVersion($runner);
    }
}
