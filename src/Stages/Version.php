<?php namespace Sculptor\Stages;

use Sculptor\Services\Env;
use Sculptor\Contracts\Runner;

/**
 * (c) Alessandro Cappellozza <alessandro.cappellozza@gmail.com>
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

class Version
{
    /**
     * @var Runner
     */
    private $runner;
    /**
     * @var Env
     */
    private $env;

    public function __construct(Runner $runner)
    {
        $this->env = new Env('/etc/os-release');

        $this->runner = $runner;
    }

    public function version(): ?string
    {
        return $this->env->get('VERSION_ID');
    }

    public function name(): ?string
    {
        return $this->env->get('VERSION');
    }

    public function compatible(): bool
    {
        return in_array($this->version(), APP_COMPATIBLE_VERSION) &&
               in_array($this->arch(), APP_COMPATIBLE_ARCH);
    }

    public function arch(): string
    {
        return clearNl($this->runner->run(['uname', '-m'])->output());
    }

    public function bits(): string
    {
        return clearNl($this->runner->run(['getconf', 'LONG_BIT'])->output());
    }
}
