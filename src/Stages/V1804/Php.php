<?php

namespace Sculptor\Stages\V1804;

use Exception;
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
    private $modules = [
        'fpm',
        'common',
        'mbstring',
        'mysql',
        'xml',
        'zip',
        'bcmath',
        'imagick',
        'redis',
        'sqlite3',
        'intl'
    ];

    /**
     * @param Environment $env
     * @return bool
     */
    public function run(Environment $env): bool
    {
        $php = $env->get('php');

        try {
            $this->version($env->getArray('php_versions'));

            if (
                !$this->write(
                    "/etc/php/{$php}/fpm/conf.d/sculptor.ini",
                    $this->template('php.ini'),
                    'Cannot write ini configuration'
                )
            ) {
                return false;
            }

            foreach ([ APP_PANEL_HTTP_USER, APP_PANEL_HTTP_PANEL ] as $user) {
                if (!$this->pool($php, $user)) {
                    return false;
                }
            }

            if (!$this->restart("php{$php}-fpm")) {
                $this->internal = 'Cannot restart service';

                return false;
            }

            return true;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }

    /**
     * @param string $php
     * @param string $user
     * @return bool
     */
    private function pool(string $php, string $user): bool
    {
        $pool = $this->replaceTemplate('php-pool.conf')
        ->replace("{USER}", $user)
        ->value();

        if (!$this->write("/etc/php/{$php}/fpm/pool.d/{$user}.conf", $pool, "Cannot write pool configuration {$user}")) {
            return false;
        }

        return true;
    }

    private function version(array $versions): bool
    {
        foreach ($versions as $version) {
            $modules = collect($this->modules)
                ->map(function ($item) use($version) {
                    return "php{$version}-{$item}";
                });

            $this->command(collect(['apt-get', '-y', 'install'])->concat($modules)->toArray());

            $this->pool($version, APP_PANEL_HTTP_USER);
        }

        $this->command(['update-alternatives', '--set', 'php', '/usr/bin/php' . APP_PANEL_PHP_VERSION]);

        return true;
    }    

    /**
     * @return string
     */
    public function name(): string
    {
        return 'Php Cgi';
    }
}
