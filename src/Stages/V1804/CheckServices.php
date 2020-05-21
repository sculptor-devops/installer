<?php namespace Eppak\Stages\V1804;

use Eppak\Contracts\Stage;
use Eppak\Stages\StageBase;
use Exception;
use Illuminate\Support\Facades\Log;

class CheckServices extends StageBase implements Stage
{
    public function run(array $env = null): bool
    {
        try {
            foreach ([
                         'mysql' => 'MySql is not running',
                         'nginx' => 'Nginx is not running',
                         'redis' => 'Redis is not running',
                         'supervisor' => 'Supervisror is not running'
                     ] as $service => $error) {
                if (!$this->active($service, $error)) {

                    return false;
                }
            }

            return true;

        } catch (Exception $e) {

            Log::error($e->getMessage());

            return false;
        }
    }

    private function active(string $name, string $error): bool
    {
        $active = $this->daemons->active($name);

        if (!$active) {
            $this->internal = $error;

            return false;
        }

        return true;
    }

    public function name(): string
    {
        return "Check services";
    }

    public function env(): ?array
    {
        return null;
    }
}
