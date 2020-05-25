<?php namespace Sculptor\Stages\V1804;

use Sculptor\Contracts\Stage;
use Sculptor\Stages\StageBase;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class CheckServices extends StageBase implements Stage
{
    private $services = [
        'mysql' => 'MySql is not running',
        'nginx' => 'Nginx is not running',
        'redis' => 'Redis is not running',
        'supervisor' => 'Supervisror is not running',
        'php7.4-fpm' => 'PHP FPM is not running'
    ];

    public function run(array $env = null): bool
    {
        try {
            foreach ($this->services as $service => $error) {
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
