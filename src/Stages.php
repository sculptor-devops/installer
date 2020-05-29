<?php namespace Sculptor;

use Sculptor\Contracts\Stage;
use Sculptor\Foundation\Support\Version;
use Sculptor\Stages\Environment;
use Sculptor\Stages\StageFactory;

use Illuminate\Support\Facades\Log;
use LaravelZero\Framework\Commands\Command;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Stages
{
    /**
     * @var Environment
     */
    private $env;

    /**
     * @var Version
     */
    private $version;

    /**
     * @var string|null
     */
    private $error;
    /**
     * @var StageFactory
     */
    private $stages;

    public function __construct(StageFactory $stages, Version $version)
    {
        $this->version = $version;

        $this->stages = $stages;

        $this->stages->version($this->version->version());

        $this->env = new Environment();
    }

    /**
     * @param Command $context
     * @param bool $remove
     * @return bool
     */
    public function run(Command $context, bool $remove = false): bool
    {
        Log::info("Running on Os version {$this->version->name()}");

        Log::info("Detected version {$this->version->version()}, architecture {$this->version->arch()} (bits {$this->version->bits()})");

        if (!$this->version->compatible(APP_COMPATIBLE_VERSION, APP_COMPATIBLE_ARCH)) {
            $this->error = 'This version of the operating system is not compatible';

            return false;
        }

        foreach ($this->stages->all() as $stage) {
            $instance = $this->stages->make($stage);

            if ($instance == null) {
                $this->error = "Unknown stage {$stage}";

                return false;
            }

            Log::info("RUNNING STAGE {$instance->name()}");

            $result = $context->task($instance->name(), function () use($instance, $remove) {
               return $this->instance($instance, $remove);
            });

            if (!$result) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<int, array<string, int|string>>
     */
    public function list(): array
    {
        $index = 1;
        $result = [];

        foreach ($this->stages->all() as $stage) {
            $instance = $this->stages->make($stage);

            $result[] = ['stage' => $index, 'name' => $instance->name()];

            $index++;
        }

        return $result;
    }

    /**
     * @param string $name
     * @param bool $remove
     * @return bool|null
     */
    public function stage(string $name, bool $remove = false): ?bool
    {
        $credentials = $this->stages->find('Credentials');

        if (!$credentials) {
            $this->error = 'Cannot load credential stage';

            return false;
        }

        Log::info("RUNNING STAGE {$credentials->name()}");

        if (!$this->instance($credentials, $remove)) {
            $this->error = $credentials->error();

            return false;
        }

        $instance = $this->stages->find($name);

        if (!$instance) {
            $this->error = 'Unknown stage';

            return false;
        }

        Log::info("RUNNING STAGE {$name}");

        return $this->instance($instance);
    }

    /**
     * @param Stage $instance
     * @param bool $remove
     * @return bool
     */
    private function instance(Stage $instance, bool $remove = false): bool
    {
        $run = false;

        if (!$remove) {
            $run = $instance->run($this->env);
        }

        if ($remove) {
            $run = $instance->remove($this->env);
        }

        if (!$run) {
            $this->error = $instance->error();

            Log::error("ERROR: {$this->error}");

            return false;
        }

        return true;
    }

    /**
     * @return Environment
     */
    public function env(): Environment
    {
        return $this->env;
    }

    /**
     * @return array|array[]
     */
    public function show(): array
    {
        return [
            ['name' => 'Public IP', 'value' => $this->env->get('ip')],
            ['name' => 'Password', 'value' => $this->env->get('password')],
            ['name' => 'Database Password', 'value' => $this->env->get('db_password')]
        ];
    }

    /**
     * @return string
     */
    public function error(): string
    {
        if (!$this->error) {
            return 'Unknown stage error';
        }

        return $this->error;
    }
}
