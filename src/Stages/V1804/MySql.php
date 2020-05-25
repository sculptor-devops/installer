<?php namespace Sculptor\Stages\V1804;

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
    public function run(array $env = null): bool
    {
        try {
            $dbPassword = $env['db_password'];

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

    private function secure(string $password)
    {
        try {
            $this->command(['mysql', '-e', 'use mysql; UPDATE user SET authentication_string = password(\'' . $password . '\') WHERE user = \'root\';']);

            $this->command(['mysql', '-e', 'use mysql;UPDATE user SET plugin=\'mysql_native_password\' WHERE User=\'root\' and plugin=\'auth_socket\'']);

            $this->command(['mysql', '-e', 'use mysql; DELETE FROM user WHERE user=\'auth_socket\'']);

            $this->command(['mysql', '-e', 'use mysql; DELETE FROM user WHERE user=\'root\' AND host NOT IN (\'localhost\', \'127.0.0.1\', \'::1\')']);

            $this->command(['mysql', '-e', 'DROP DATABASE IF EXISTS test']);

            $this->command(['mysql', '-e', 'FLUSH PRIVILEGES']);

            $this->daemons->restart('mysql');

        } catch (\Exception $e) {
            Log::warning("Unable to secure MySql: {$e->getMessage()}");
        }
    }

    public function name(): string
    {
        return 'MySql Server';
    }

    public function env(): ?array
    {
        return null;
    }
}
