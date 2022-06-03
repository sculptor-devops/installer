<?php

namespace Sculptor\Stages;

use Exception;
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
     * @var Configuration
     */
    private $configuration;
    /**
     * @var StageResolver
     */
    private $resolver;

    /**
     * StageFactory constructor.
     * @param Configuration $configuration
     * @param StageResolver $resolver
     */
    public function __construct(Configuration $configuration, StageResolver $resolver)
    {
        $this->configuration = $configuration;

        $this->resolver = $resolver;
    }

    /**
     * @param string|null $version
     * @return void
     */
    public function version(?string $version): void
    {
        $this->resolver->version($version);
    }

    /**
     * @return array<string>
     * @throws Exception
     */
    public function list(): array
    {
        $resolved = [];

        foreach ($this->all() as $stage) {
            $resolved[] = $this->resolver->resolve($stage);
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
     * @param string $class
     * @return Stage
     * @throws Exception
     */
    public function make(string $class): Stage
    {
        $resolved = $this->resolver->resolve($class);

        return resolve($resolved);
    }

    /**
     * @param string $name
     * @return Stage|null
     * @throws Exception
     */
    public function find(string $name): ?Stage
    {
        foreach ($this->configuration->stages() as $stage) {
            $instance = $this->make($stage);

            if (
                Str::lower($instance->name()) == Str::lower($name) ||
                Str::lower($instance->className()) == Str::lower($name)
            ) {
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

        $this->env->add('port', $this->configuration->port());

        $this->env->addArray('php_versions', $this->configuration->phpVersions());

        $this->env->addArray('php_modules', $this->configuration->phpModules());

        $this->env->add('node_version', $this->configuration->nodeVersion());

        $this->env->add('stages', Str::lower(implode(',', $this->all())));

        if ($this->configuration->password()) {
            $this->env->add('password', $this->configuration->password());
        }

        if ($this->configuration->dbPassword()) {
            $this->env->add('db_password', $this->configuration->dbPassword());
        }

        return $this->env;
    }
}
