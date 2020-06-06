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
            $dbPassword = $env->get('db_password');

            if ($this->daemons->installed('mysql-server')) {
                $this->internal = 'Machine seem to be already installed (mysql server at least is present)';

                return false;
            }

            $this->debconf($dbPassword);

            $this->command(['apt-get', '-y', 'install', 'mysql-server', 'mysql-client']);

            $this->secure($dbPassword);

            $this->restart('mysql');

            return true;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    /**
     * @param string $password
     * @throws Exception
     */
    protected function debconf(string $password): void
    {
        $this->command([
            'echo',
            '"mysql-server mysql-server/root_password password ' . $password . '"',
            '|',
            'debconf-set-selections'
        ]);

        $this->command([
            'echo',
            '"mysql-server mysql-server/root_password_again password ' . $password . '"',
            '|',
            'debconf-set-selections'
        ]);
    }

    /**
     * @param string $password
     */
    protected function secure(string $password): void
    {
        try {
            $this->command(['mysql', '-e', 'use mysql; UPDATE user SET authentication_string = password(\'' . $password . '\') WHERE user = \'root\';']);

            $this->command(['mysql', '-e', 'use mysql; UPDATE user SET plugin=\'mysql_native_password\' WHERE User=\'root\' and plugin=\'auth_socket\';']);

            $this->command(['mysql', '-e', 'use mysql; DELETE FROM user WHERE user=\'root\' AND host NOT IN (\'localhost\', \'127.0.0.1\', \'::1\');']);

            $this->command(['mysql', '-e', 'DROP DATABASE IF EXISTS test;']);

            $this->command(['mysql', '-e', 'FLUSH PRIVILEGES;']);
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
