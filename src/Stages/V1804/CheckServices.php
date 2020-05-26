<?php namespace Sculptor\Stages\V1804;

use Sculptor\Contracts\Stage;
use Sculptor\Stages\Environment;
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
    /**
     * @var string[]
     */
    private $services = [
        'mysql' => 'MySqlManager is not running',
        'nginx' => 'Nginx is not running',
        'redis' => 'Redis is not running',
        'supervisor' => 'Supervisror is not running',
        'php7.4-fpm' => 'PHP FPM is not running'
    ];

    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
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

    /**
     * @param string $name
     * @param string $error
     * @return bool
     */
    private function active(string $name, string $error): bool
    {
        $active = $this->daemons->active($name);

        if (!$active) {
            $this->internal = $error;

            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return "Check services";
    }

    /**
     * @return Environment|null
     */
    public function env(): ?Environment
    {
        return null;
    }
}
