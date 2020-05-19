<?php namespace Eppak\Stages\V1804;

use Eppak\Contracts\Runner;
use Eppak\Contracts\Stage;
use Eppak\Services\Daemons;
use Eppak\Stages\StageBase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class SuUser extends StageBase implements Stage
{
    public function run(array $env = null): bool
    {
        $this->internal = 'This user is not an superuser';

        return sudo();
    }

    public function name(): string
    {
        return 'Superuser';
    }

    public function env(): ?array
    {
        return null;
    }
}
