<?php

namespace Sculptor\Stages;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Environment
{

    /**
     * @var array<string, string>
     */
    private $env = [];

    /**
     * Environment constructor.
     * @param array<string,string>|null $env
     */
    public function __construct(array $env = null)
    {
        if ($env) {
            $this->env = $env;
        }
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function add(string $key, string $value): void
    {
        $this->env[$key] = $value;
    }

    /**
     * @param string $key
     * @param string $default
     * @return string
     */
    public function get(string $key, string $default = ''): string
    {
        if (!$this->has($key)) {
            return $default;
        }

        $value = $this->env[$key];

        if ($value == null) {
            return '';
        }

        return $value;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        if (!array_key_exists($key, $this->env)) {
            return false;
        }

        $value = $this->env[$key];

        return $value != null || $value != '';
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return $this->env;
    }

    /**
     * @return $this
     */
    public function connection(): self
    {
        Log::info('New DB connection db_server');

        config(['database.connections.db_server' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'database' => 'mysql',
            'username' => 'root',
            'password' => $this->get('db_password')
        ]]);

        DB::setDefaultConnection('db_server');

        return $this;
    }
}
