<?php namespace Sculptor\Stages;

use Illuminate\Support\Facades\DB;

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
        if (!array_key_exists($key, $this->env)) {
            return $default;
        }

        return $this->env[$key];
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return $this->env;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function connection(string $password): self
    {
        config([
            'database.connections.temp' => [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'database' => 'mysql',
                'username' => 'root',
                'password' => $password
            ]
        ]);

        DB::setDefaultConnection('temp');

        return $this;
    }
}
