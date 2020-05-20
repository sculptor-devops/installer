<?php namespace Eppak\Services;

use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class Configuration
{
    /**
     * @var Templates
     */
    private $templates;

    /**
     * @var array
     */
    private $configuration;

    /**
     * Configuration constructor.
     * @param Templates $templates
     */
    public function __construct(Templates $templates)
    {
        $this->templates = $templates;

        $this->load();
    }

    /**
     *
     */
    private function load(): void
    {
        $custom = getcwd() . '/' . APP_CONFIG_FILENAME;
        $configuration = $this->template();

        if (File::exists($custom)) {
            $configuration = File::get($custom);
        }

        $this->configuration = Yaml::parse($configuration);
    }

    /**
     * @return array
     */
    public function stages(): array
    {
        return $this->configuration['stages'];
    }

    /**
     * @return string
     */
    public function user(): string
    {
        return $this->configuration['user'];
    }

    public function template(): string
    {
        return $this->templates->read('config.yml');
    }
}
