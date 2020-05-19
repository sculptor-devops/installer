<?php namespace Eppak;

use Eppak\Contracts\Stage;
use Eppak\Services\Daemons;
use Eppak\Services\Env;
use Eppak\Stages\Resolver;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

    private $version;

    private $error;

    /**
     * @var array
     */
    private $stages = [
        'SuUser',
        'Motd',
        'Php',
        'Packages',
        'Sshd',
        'Credentials',
        'Nginx',
        'MySql',
        'Redis',
        'LetsSEncrypt',
        'Composer',
        'Firewall',
        'Crontab'
    ];

    /**
     * @var Daemons
     */
    private $daemons;

    public function __construct(Daemons $daemons)
    {
        $this->daemons = $daemons;

        $this->version = $this->version();

        $this->stages = Resolver::prepare($this->stages, $this->version);
    }

    private function compatible(): bool
    {
        return in_array($this->version, APP_COMPATIBLE);
    }

    public function run($context): bool
    {
        Log::info("Running on Os version {$this->version}");

        if (!$this->compatible()) {
            $this->error = 'This version of the operating system is not compatible';

            return false;
        }

        foreach ($this->stages as $stage) {
            $instance = Resolver::make($stage);

            $result = $context->task($instance->name(), function () use($instance) {
               return $this->instance($instance);
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

        foreach ($this->stages as $stage) {
            $instance = resolve($stage);

            $result[] = ['stage' => $index, 'name' => $instance->name()];

            $index++;
        }

        return $result;
    }

    public function stage(string $name): ?bool
    {
        foreach ($this->stages as $stage) {
            $instance = resolve($stage);

            if ( Str::lower($instance->name()) == Str::lower($name)) {
                Log::info("RUNNING STAGE {$name}");

                return $this->instance($instance);
            }
        }

        $this->error = 'Unknown stage';

        return false;
    }

    private function instance(Stage $instance): bool
    {
        if (!$instance->run($this->env)) {
            $this->error = $instance->error();

            Log::error("ERROR: {$this->error}");

            return false;
        }

        if ($instance->env() != null) {
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

    public function version(): ?string
    {
        $env = new Env('/etc/os-release');

        return $env->get('VERSION_ID');
    }

    public function error(): string
    {
        if (!$this->error) {
            return 'Unknown error';
        }

        return $this->error;
    }
}
