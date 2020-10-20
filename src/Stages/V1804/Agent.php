<?php

namespace Sculptor\Stages\V1804;

use Sculptor\Contracts\Stage;
use Sculptor\Stages\Environment;
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
    /**
     * @var string
     */
    private $path = '/var/www/html';

    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
    {
        try {
            $password = $this->password(16);

            if (!$this->write("{$this->path}/deploy.php",
                                $this->template('agent-deploy.php'),
                                'Cannot write deploy script')) {
                return false;
            }
    
            File::deleteDirectory("{$this->path}/current");

            $this->command(['dep', 'deploy', '-q'], $this->path);

            $agent = $this->replaceTemplate('agent-env')
                ->replace('{PASSWORD}', $password)
                ->replace('{INSTALLED}', $env->get('stages'))
                ->replace('{URL}', 'https://' . $env->get('ip') . ':' . $env->get('port'))
                ->value();

            File::put('/var/www/html/shared/.env', $agent);

            $this->db->db(APP_PANEL_DB);

            $this->db->user(APP_PANEL_DB_USER, $password, APP_PANEL_DB);

            $this->noninteractive();

            $this->command(['php', "{$this->path}/current/artisan", 'key:generate'], $this->path);

            $this->command(['dep', 'deploy:migrate'], $this->path);

            $this->command(['dep', 'deploy:owner'], $this->path);

            File::put('/bin/sculptor', "php {$this->path}/current/artisan $@");

            File::chmod('/bin/sculptor', 0755);

            File::put(
                '/etc/supervisor/conf.d/system.sculptor.conf',
                $this->replaceTemplate('system.sculptor.conf')
                            ->replace('{USER}', APP_PANEL_USER)
                ->value()
            );

            File::put(
                '/etc/supervisor/conf.d/events.sculptor.conf',
                $this->replaceTemplate('events.sculptor.conf')
                            ->replace('{USER}', APP_PANEL_HTTP_PANEL)
                ->value()
            );

            $this->restart('supervisor');

            return true;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Agent';
    }
}
