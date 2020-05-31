<?php

namespace Sculptor\Stages\V1804;

use Sculptor\Stages\Environment;
use Sculptor\Stages\StageBase;
use Sculptor\Contracts\Stage;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class MySql extends StageBase implements Stage
{
    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
    {
        try {
            $dbPassword = $env->get('dbPassword ');

            $env->connection();

            $this->command([
                'echo',
                '"mysql-server mysql-server/root_password password ' . $dbPassword . '"',
                '|',
                'debconf-set-selections'
            ]);

            $this->command([
                'echo',
                '"mysql-server mysql-server/root_password_again password ' . $dbPassword . '"',
                '|',
                'debconf-set-selections'
            ]);

            $this->command(['apt-get', '-y', 'install', 'mysql-server', 'mysql-client']);

            $this->secure($dbPassword);

            return true;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    /**
     * @param string $password
     */
    private function secure(string $password): void
    {
        try {
            foreach (
                [
                         'UPDATE user SET authentication_string = password(\'' . $password . '\') WHERE user = \'root\';',
                         'UPDATE user SET plugin=\'mysql_native_password\' WHERE User=\'root\' and plugin=\'auth_socket\'',
                         'DELETE FROM user WHERE user=\'auth_socket\'',
                         'DELETE FROM user WHERE user=\'root\' AND host NOT IN (\'localhost\', \'127.0.0.1\', \'::1\')',
                         'DROP DATABASE IF EXISTS test',
                         'FLUSH PRIVILEGES'
                     ] as $command
            ) {
                $this->command([
                    'mysql',
                    '-e',
                    "use mysql; {$command}"
                ]);
            }

            $this->restart('mysql');
        } catch (Exception $e) {
            Log::warning("Unable to secure MySqlManager: {$e->getMessage()}");
        }
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'MySql Server';
    }
}
