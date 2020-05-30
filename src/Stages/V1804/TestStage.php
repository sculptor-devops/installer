<?php

namespace Sculptor\Stages\V1804;

use Sculptor\Contracts\Stage;
use Sculptor\Stages\Environment;
use Sculptor\Stages\StageBase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class TestStage extends StageBase implements Stage
{
    public function run(Environment $env): bool
    {
        return true;
    }

    public function name(): string
    {
        return "Test Stage";
    }
}
