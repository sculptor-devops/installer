<?php namespace Sculptor\Database;

use Illuminate\Support\Facades\DB;
use Sculptor\Contracts\DatabaseManager;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class MySqlManager implements DatabaseManager
{
    public function set(string $password): self
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

    public function db(string $name): bool
    {
        return DB::statement("CREATE DATABASE IF NOT EXISTS {$name};");
    }

    public function user(string $user, string $password, string $db, string $host = 'localhost'): bool
    {
        $dropped = DB::statement("DROP USER IF EXISTS {$user}@'{$host}'");

        if (!$dropped) {
            return false;
        }

        $created = DB::statement("CREATE USER {$user}@'{$host}' IDENTIFIED BY '{$password}'");

        if (!$created) {
            return false;
        }

        $grant = DB::statement("GRANT ALL PRIVILEGES ON {$db}.* TO '{$user}'@'{$host}';");

        if (!$grant) {
            return false;
        }

        return DB::statement("FLUSH PRIVILEGES;");
    }
}
