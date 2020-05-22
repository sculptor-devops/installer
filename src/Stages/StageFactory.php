<?php namespace Eppak\Stages;

use Eppak\Services\Configuration;
use Illuminate\Support\Str;

class StageFactory
{
    /**
     * @var string
     */
    private $version;
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function version(string $version)
    {
        $this->version = 'V' . str_replace('.', '', $version);
    }

    public function list(): array
    {
        $resolved = [];

        foreach ($this->all() as $stage) {
            $resolved[] = $this->resolve($stage);
        }

        return $resolved;
    }

    public function all()
    {
        return $this->configuration->stages();
    }

    private function resolve(string $stage): string
    {
        return "Eppak\Stages\\{$this->version}\\{$stage}";
    }

    public function make(string $class)
    {
        $resolved = $this->resolve($class);

        return resolve($resolved);
    }

    public function find(string $name)
    {
        foreach ($this->configuration->stages() as $stage) {
            $instance = static::make($stage);

            if (Str::lower($instance->name()) == Str::lower($name) ||
                Str::lower($instance->className()) == Str::lower($name)) {
                return $instance;
            }
        }

        return null;
    }
}
