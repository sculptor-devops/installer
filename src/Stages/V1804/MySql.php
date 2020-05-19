<?php namespace Eppak\Stages\V1804;

use Eppak\Stages\StageBase;
use Eppak\Contracts\Stage;
use Exception;
use Illuminate\Support\Facades\File;
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

            return true;

        } catch (Exception $e) {

            Log::error($e->getMessage());

            return false;
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
