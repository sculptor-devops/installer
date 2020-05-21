<?php namespace Eppak\Stages\V1804;

use Eppak\Contracts\Stage;
use Eppak\Stages\StageBase;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class Motd extends StageBase implements Stage
{
    public function run(array $env = null): bool
    {
        try {


            $motd = $this->template('motd');

            $written = File::put('/etc/motd', $motd);

            if (!$written) {

                return false;
            }

            $this->internal = 'Cannot write to motd file';

            return true;

        } catch (Exception $e) {

            Log::error($e->getMessage());

            return false;
        }
    }

    public function name(): string
    {
        return 'Motd';
    }

    public function env(): ?array
    {
        return null;
    }
}
