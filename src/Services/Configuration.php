<?php

namespace Sculptor\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\Yaml\Yaml;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
class Configuration
{
    /**
     * @var Templates
     */
    private $templates;

    /**
     * @var mixed
     */
    private $configuration;

    /**
     * Configuration constructor.
     * @param Templates $templates
     * @throws FileNotFoundException
     */
    public function __construct(Templates $templates)
    {
        $this->templates = $templates;

        $this->load();
    }

    /**
     * @throws FileNotFoundException
     */
    private function load(): void
    {
        LOG::info('Current directory is ' . getcwd());

        $custom = getcwd() . '/' . APP_CONFIG_FILENAME;

        $configuration = $this->template();

        if (File::exists($custom)) {
            LOG::info("Using custom configuration {$custom}");

            $configuration = File::get($custom);
        }

        $this->configuration = Yaml::parse($configuration);
    }

    /**
     * @return array<string>
     */
    public function stages(): array
    {
        return $this->configuration['stages'];
    }

    /**
     * @param string $key
     * @param string $default
     * @return string
     */
    private function getString(string $key, string $default = ''): string
    {
        if (!$this->has($key)) {
            return $default;
        }

        $value = $this->configuration[$key];

        if (!is_string($value)) {
            return $default;
        }

        return $value;
    }

     /**
     * @param string $key
     * @return array
     */
    private function getArray(string $key): array
    {
        if (!$this->has($key)) {
            return [];
        }

        $value = $this->configuration[$key];

        if (!is_array($value)) {
            return [];
        }

        return $value;
    }

     /**
     * @param string $key
     * @return int
     */
    private function getInt(string $key): int
    {
        if (!$this->has($key)) {
            return 0;
        }

        $value = $this->configuration[$key];

        if (!is_int($value)) {
            return 0;
        }

        return $value;
    }       

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->configuration);
    }

    /**
     * @return string
     */
    public function user(): string
    {
        return $this->getString('user', APP_PANEL_USER);
    }

    /**
     * @return string
     * @throws FileNotFoundException
     */
    public function template(): string
    {
        return $this->templates->read(APP_CONFIG_FILENAME);
    }

    /**
     * @return string
     */
    public function php(): string
    {
        return $this->getString('php', APP_PANEL_PHP_VERSION);
    }

    /**
     * @return string
     */
    public function password(): string
    {
        $password = $this->getString('password');

        if ($password) {
            return $password;
        }

        return '';
    }

    /**
     * @return string
     */
    public function dbPassword(): string
    {
        $dbPassword = $this->getString('dbPassword');

        if ($dbPassword) {
            return $dbPassword;
        }

        return '';
    }

    /**
     * @return string
     */
    public function port(): string
    {
        return $this->getString('port', APP_PANEL_HTTP_PORT);
    }

    /**
     * @return array
     */
    public function phpVersions(): array
    {
        return $this->getArray('php_versions');
    }    

    /**
     * @return string
     */
    public function nodeVersion(): string
    {
        return $this->getInt('node_version');
    } 
   
}
