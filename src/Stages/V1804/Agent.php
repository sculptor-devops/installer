<?php namespace Eppak\Stages\V1804;

use Eppak\Contracts\Stage;
use Eppak\Stages\StageBase;
use Eppak\Replacer;
use Exception;
use Illuminate\Support\Facades\Log;

class Agent extends StageBase implements Stage
{
    public function run(array $env = null): bool
    {
        try {
            // /etc/supervisor/conf.d/system.sculptor.conf

            // $replaced = new Replacer();

            return true;

        } catch (Exception $e) {

            Log::error($e->getMessage());

            return false;
        }
    }

    public function name(): string
    {
        return 'Agent';
    }

    public function env(): ?array
    {
        return null;
    }
}
