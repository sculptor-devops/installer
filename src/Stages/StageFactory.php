<?php namespace Eppak\Stages;

use Illuminate\Support\Str;

class StageFactory
{
    /**
     * @var string
     */
    private $version;

    public function version(string $version)
    {
        $this->version = 'V' . str_replace('.', '', $version);
    }

    public function list(): array
    {
        $resolved = [];

        foreach (static::all() as $stage) {
            $resolved[] = $this->resolve($stage);
        }

        return $resolved;
    }

    public static function all()
    {
        return APP_STAGES;
    }

    private function resolve(string $stage): string
    {
        return "Eppak\Stages\\{$this->version}\\{$stage}";
    }

    public function make(string $class)
    {
        return resolve($this->resolve($class));
    }

    public function find(string $name)
    {
        foreach (APP_STAGES as $stage) {
            $instance = static::make($stage);

            if (Str::lower($instance->name()) == Str::lower($name)) {
                return $instance;
            }
        }

        return null;
    }
}
