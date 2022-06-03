<?php

namespace Sculptor\Stages\V1804;

use Exception;
use League\Flysystem\FileNotFoundException;
use Sculptor\Contracts\Stage;
use Sculptor\Stages\Environment;
use Sculptor\Stages\StageBase;
use Illuminate\Support\Facades\Log;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Php extends StageBase implements Stage
{
    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
    {
        $php = $env->get('php');

        try {
            $versions = $env->getArray('php_versions');

            $modules = $env->getArray('php_modules');

            $this->version($versions, $modules);

            $this->agent($php);

            $this->command(['update-alternatives', '--set', 'php', '/usr/bin/php' . APP_PANEL_PHP_VERSION]);

            return true;
        } catch (Exception $e) {
            $this->internal = $e->getMessage();
            Log::error($e->getMessage());

            return false;
        }
    }

    /**
     * @param string $php
     * @return void
     * @throws FileNotFoundException
     * @throws Exception
     */
    private function agent(string $php): void
    {
        if (
            !$this->write(
                "/etc/php/{$php}/fpm/conf.d/sculptor.ini",
                $this->template('php.ini'),
                'Cannot write ini configuration'
            )
        ) {
            throw new Exception("Cannot write php{$php} for sculptor agent");
        }

        foreach ([APP_PANEL_HTTP_USER, APP_PANEL_HTTP_PANEL] as $user) {
            Log::info("     Installing php fpm pool version {$php} for {$user}");

            if (!$this->pool($php, $user)) {
                throw new Exception("Cannot write php{$php} for user {$user}");
            }
        }

        if (!$this->restart("php{$php}-fpm")) {
            throw new Exception("Cannot restart php{$php} service");
        }
    }

    /**
     * @param string $php
     * @param string $user
     * @return bool
     * @throws FileNotFoundException
     */
    private function pool(string $php, string $user): bool
    {
        $pool = $this->replaceTemplate('php-pool.conf')
            ->replace("{USER}", $user)
            ->replace("{PHP}", $php)
            ->value();

        if (!$this->write("/etc/php/{$php}/fpm/pool.d/{$user}.conf", $pool, "Cannot write pool configuration {$user}")) {
            return false;
        }

        return true;
    }

    private function modules(array $modules, string $version): array
    {
        return collect($modules)
            ->map(function ($item) use ($version) {
                return "php{$version}-{$item}";
            })
            ->toArray();
    }

    /**
     * @param array $versions
     * @param array $modules
     * @return void
     * @throws FileNotFoundException
     * @throws Exception
     */
    private function version(array $versions, array $modules): void
    {
        foreach ($versions as $version) {
            Log::info("     Installing php fpm pool version {$version} user " . APP_PANEL_HTTP_USER);

            $apt = $this->modules($modules, $version);

            $this->command(collect(['apt-get', '-y', 'install'])->concat($apt)->toArray());

            if (!$this->pool($version, APP_PANEL_HTTP_USER)) {
                throw new Exception("Cannot restart php{$version} service");
            }

            if (!$this->restart("php{$version}-fpm")) {
                throw new Exception("Cannot restart php{$version} service");
            }
        }
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Php';
    }
}
