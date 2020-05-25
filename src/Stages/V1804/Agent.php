<?php namespace Sculptor\Stages\V1804;

use Sculptor\Contracts\Stage;
use Sculptor\Stages\StageBase;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Agent extends StageBase implements Stage
{
    private $path = '/var/www/html';

    public function run(array $env = null): bool
    {
        try {
            $password = $this->password(16);

            $dbPassword = $this->env['db_password'];

            $deploy = $this->template('agent-deploy.php');

            $written = File::put("{$this->path}/deploy.php", $deploy);

            if (!$written) {
                $this->internal = 'Cannot write deploy script';

                return false;
            }

            File::deleteDirectory("{$this->path}/current");

            $this->command(['dep', 'deploy'], false, $this->path);

            $env = $this->replaceTemplate('agent-env')
                ->replace('{PASSWORD}', $password)
                ->replace('{DB_PASSWORD}', $dbPassword)
                ->value();

            File::put('/var/www/html/shared/.env', $env);

            $this->db->set($dbPassword)->db(APP_PANEL_DB);

            $this->db->user(APP_PANEL_DB_USER, $password, APP_PANEL_DB);

            $this->command(['php', "{$this->path}/current/artisan", 'key:generate'], false, $this->path);

            $this->command(['dep', 'deploy:migrate'], false, $this->path);

            $this->command(['dep', 'deploy:owner'], false, $this->path);

            File::put('/bin/sculptor', "php {$this->path}/current/artisan $@");

            File::chmod('/bin/sculptor', 755);

            $supervisor = $this->replaceTemplate('system.sculptor.conf')
                ->replace('{USER}', APP_PANEL_USER)
                ->value();

            File::put('/etc/supervisor/conf.d/system.sculptor.conf', $supervisor);

            $supervisor = $this->replaceTemplate('www.sculptor.conf')
                ->replace('{USER}', APP_PANEL_HTTP_PANEL)
                ->value();

            File::put('/etc/supervisor/conf.d/www.sculptor.conf', $supervisor);

            $this->daemons->restart('supervisor');

            return true;

        } catch (Exception $e) {

            Log::error($e->getMessage());

            return false;
        }
    }

    public function name(): string
    {
        return 'Agent';
    }

    public function env(): ?array
    {
        return null;
    }
}
