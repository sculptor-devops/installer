<?php

namespace Sculptor\Stages\V2004;

use Exception;
use Illuminate\Support\Facades\Log;
use Sculptor\Stages\Environment;
use Sculptor\Stages\V1804\MySql as MySqlBase;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class MySql extends MySqlBase
{
    /**
     * @param string $password
     */
    protected function secure(string $password): void
    {
        try {
            $this->command(['mysql', '-e', 'use mysql; ALTER USER \'root\'@\'localhost\' IDENTIFIED WITH caching_sha2_password BY \'' . $password . '\'']);

            $this->command(['mysql', '-e', 'use mysql; UPDATE user SET plugin=\'mysql_native_password\' WHERE User=\'root\' and plugin=\'auth_socket\'']);

            $this->command(['mysql', '-e', 'use mysql; DELETE FROM user WHERE user=\'auth_socket\'']);

            $this->command(['mysql', '-e', 'use mysql; DELETE FROM user WHERE user=\'root\' AND host NOT IN (\'localhost\', \'127.0.0.1\', \'::1\')']);

            $this->command(['mysql', '-e', 'DROP DATABASE IF EXISTS test']);

            $this->command(['mysql', '-e', 'FLUSH PRIVILEGES']);
        } catch (Exception $e) {
            Log::warning("Unable to secure MySqlManager: {$e->getMessage()}");
        }
    }
}
