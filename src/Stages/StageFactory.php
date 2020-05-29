<?php namespace Sculptor\Stages;

use Sculptor\Contracts\Stage;
use Sculptor\Services\Configuration;
use Illuminate\Support\Str;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class StageFactory
{
    /**
     * @var Environment
     */
    private $env;

    /**
     * @var string
     */
    private $version;
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * StageFactory constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param string|null $version
     * @return void
     */
    public function version(?string $version): void
    {
        if($version) {
            $this->version = 'V' . str_replace('.', '', $version);

            return;
        }

        $this->version = 'UNKNOWN';
    }

    /**
     * @return array<string>
     */
    public function list(): array
    {
        $resolved = [];

        foreach ($this->all() as $stage) {
            $resolved[] = $this->resolve($stage);
        }

        return $resolved;
    }

    /**
     * @return array<string>
     */
    public function all(): array
    {
        return $this->configuration->stages();
    }

    /**
     * @param string $stage
     * @return string
     */
    private function resolve(string $stage): string
    {
        return "Sculptor\Stages\\{$this->version}\\{$stage}";
    }

    /**
     * @param string $class
     * @return Stage
     */
    public function make(string $class): Stage
    {
        $resolved = $this->resolve($class);

        return resolve($resolved);
    }

    /**
     * @param string $name
     * @return Stage|null
     */
    public function find(string $name): ?Stage
    {
        foreach ($this->configuration->stages() as $stage) {
            $instance = $this->make($stage);

            if (Str::lower($instance->name()) == Str::lower($name) ||
                Str::lower($instance->className()) == Str::lower($name)) {

                return $instance;
            }
        }

        return null;
    }

    /**
     * @return Environment
     */
    public function env(): Environment
    {
        if ($this->env == null) {
            $this->env = new Environment();
        }

        $this->env->add('php', $this->configuration->php());

        $this->env->add('user', $this->configuration->user());

        return $this->env;
    }
}
