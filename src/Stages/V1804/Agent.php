<?php

namespace Sculptor\Stages\V1804;

use League\Flysystem\FileNotFoundException;
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

            if (
                !$this->write(
                    "{$this->path}/deploy.php",
                    $this->template('agent-deploy.php'),
                    'Cannot write deploy script'
                )
            ) {
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

            $this->database($env, $password);

            $this->noninteractive();

            $this->command(['php', "{$this->path}/current/artisan", 'key:generate'], $this->path);

            $this->command(['dep', 'deploy:migrate'], $this->path);

            $this->command(['php', "{$this->path}/current/artisan", 'passport:keys'], $this->path);

            $this->command(['dep', 'deploy:owner'], $this->path);

            $this->bin();

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

    private function database(Environment $env, string $password): void
    {
        $env->connection();

        if (!$this->db->db(APP_PANEL_DB)) {
            throw new Exception("Cannot create database " . APP_PANEL_DB . ": {$this->db->error()}");
        }

        if (!$this->db->user(APP_PANEL_DB_USER, $password, APP_PANEL_DB)) {
            throw new Exception("Cannot create database user " . APP_PANEL_DB_USER . ": {$this->db->error()}");
        }
    }

    /**
     * @throws FileNotFoundException
     * @throws Exception
     */
    private function bin(): void
    {
        foreach (
            [
                     'sculptor_agent' => '/bin/sculptor',
                     'sculptor_upgrade' => '/bin/sculptor-upgrade'
                 ] as $source => $destination
        ) {
            if (
                !File::put(
                    $destination,
                    $this->replaceTemplate($source)
                    ->replace('{USER}', APP_PANEL_HTTP_PANEL)
                    ->replace('{PATH}', $this->path)
                    ->value()
                )
            ) {
                throw new Exception("Cannot write file {$destination}");
            }

            File::chmod($destination, 0755);
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
