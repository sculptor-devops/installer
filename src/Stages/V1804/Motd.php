<?php namespace Eppak\Stages\V1804;

use Eppak\Contracts\Stage;
use Eppak\Stages\StageBase;
use Illuminate\Support\Facades\File;

class Motd extends StageBase implements Stage
{
    public function run(array $env = null): bool
    {
        $motd = $this->template('motd');

        $written = File::put('/etc/motd', $motd);

        if (!$written) {

            return false;
        }

        $this->internal = 'Cannot write to motd file';

        return true;
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
