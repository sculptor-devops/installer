<?php namespace Eppak\Stages\V1804;

use Eppak\Contracts\Stage;
use Eppak\Stages\StageBase;
use Exception;

class NodeJs extends StageBase implements Stage
{
    public function run(array $env = null): bool
    {
        try {
            $setup = '/tmp/setup_12.x';

            $copy = copy('https://deb.nodesource.com/setup_12.x', $setup);

            if (!$copy) {
                $this->internal = "Unable to download setup";

                return false;
            }

            $this->command(['sh', '/tmp/setup_12.x'], false);

            $this->command(['apt-get', 'install', '-y', 'nodejs']);

            $delete = unlink($setup);

            if (!$delete) {
                $this->internal = "Unable to delete setup";

                return false;
            }

            return true;

        } catch (Exception $e) {

            return false;
        }
    }

    public function name(): string
    {
        return 'NodeJs';
    }

    public function env(): ?array
    {
        return null;
    }
}
