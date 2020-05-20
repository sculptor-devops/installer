<?php namespace Eppak;

use Eppak\Contracts\Stage;
use Eppak\Stages\StageFactory;

use Eppak\Stages\Version;
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
     * @var array
     */
    private $env = [];

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $error;
    /**
     * @var StageFactory
     */
    private $stages;

    public function __construct(StageFactory $stages)
    {
        $this->version = Version::get();

        $this->stages = $stages;

        $this->stages->version($this->version);
    }

    public function run(Command $context, bool $remove = false): bool
    {
        Log::info("Running on Os version {$this->version}");

        if (!Version::compatible()) {
            $this->error = 'This version of the operating system is not compatible';

            return false;
        }

        foreach (StageFactory::all() as $stage) {
            $instance = $this->stages->make($stage);

            if (!$instance) {
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

    public function list(): array
    {
        $index = 1;
        $result = [];

        foreach (StageFactory::all() as $stage) {
            $instance = $this->stages->make($stage);

            $result[] = ['stage' => $index, 'name' => $instance->name()];

            $index++;
        }

        return $result;
    }

    public function stage(string $name, bool $remove = false): ?bool
    {
        $credentials = $this->stages->find('Credentials');

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

        if ($instance->env()) {
            Log::info("ENV updated");

            $this->env = $instance->env();
        }

        return true;
    }

    public function env(): array
    {
        return $this->env;
    }

    public function show(): array
    {
        return [
            ['name' => 'Public IP', 'value' => $this->env['ip']],
            ['name' => 'Password', 'value' => $this->env['password']],
            ['name' => 'Database Password', 'value' => $this->env['db_password']]
        ];
    }

    public function error(): string
    {
        if (!$this->error) {
            return 'Unknown stage error';
        }

        return $this->error;
    }
}
