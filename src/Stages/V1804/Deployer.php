<?php namespace Sculptor\Stages\V1804;

use Sculptor\Contracts\Stage;
use Sculptor\Stages\StageBase;

use Exception;
use Illuminate\Support\Facades\Log;

class Deployer extends StageBase implements Stage
{

    public function run(array $env = null): bool
    {
        try {
            $setup = '/tmp/deployer.phar';

            $copy = copy('https://deployer.org/deployer.phar', $setup);

            if (!$copy) {
                $this->internal = "Unable to download setup";

                return false;
            }

            $this->command(['mv', $setup, '/usr/local/bin/dep']);

            $this->command(['chmod', '+x', '/usr/local/bin/dep']);

            return true;

        } catch (Exception $e) {

            Log::error($e->getMessage());

            return false;
        }
    }

    public function name(): string
    {
        return "Deployer";
    }

    public function env(): ?array
    {
        return null;
    }
}
